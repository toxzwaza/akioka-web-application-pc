#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
FAX送信ワーカースクリプト
app.pyから分離されたFAX送信処理を実行する
"""

import os
import json
import time
import threading
import uuid
from datetime import datetime
from fax_sender import send_fax_with_retry, cleanup_temp_files
from reportlab.lib.pagesizes import A4
from reportlab.pdfgen import canvas
from reportlab.lib.utils import ImageReader
from PIL import Image
import requests
import shutil
from db import (load_parameters, update_request_status,
                update_request_converted_pdf, send_callback_notification, API_BASE_URL)

# 設定
CONVERTED_PDF_FOLDER = "converted_pdfs"

# フォルダを作成
if not os.path.exists(CONVERTED_PDF_FOLDER):
    os.makedirs(CONVERTED_PDF_FOLDER)

# グローバルロックを定義（FAX送信中の並列実行を防止）
fax_lock = threading.Lock()

# -------------------------------
# データベース操作（db.pyからインポート済み）
# -------------------------------

# -------------------------------
# ファイル処理
# -------------------------------

def download_file(file_url, local_path):
    """ファイルをダウンロードまたはローカルコピー"""
    try:
        if file_url.startswith('file://'):
            local_file_path = file_url[7:]
            if local_file_path.startswith('/'):
                local_file_path = local_file_path[1:]
            if not os.path.exists(local_file_path):
                print(f"ローカルファイルが見つかりません: {local_file_path}")
                return False
            shutil.copy2(local_file_path, local_path)
            print(f"ローカルファイルをコピーしました: {local_file_path} -> {local_path}")
            return True
        else:
            response = requests.get(file_url)
            response.raise_for_status()
            with open(local_path, "wb") as f:
                f.write(response.content)
            print(f"リモートファイルをダウンロードしました: {file_url}")
            return True
    except Exception as e:
        print(f"ファイル処理エラー: {e}")
        return False

# -------------------------------
# PDF作成処理
# -------------------------------

def create_pdf_from_image(image_path, output_pdf_path):
    """画像をA4縦のPDFに貼り付けて保存（余白最小化）"""
    c = canvas.Canvas(output_pdf_path, pagesize=A4)
    width, height = A4

    img = Image.open(image_path)
    img_width, img_height = img.size
    aspect = img_height / img_width

    # A4余白を最小限（3mm程度）に設定
    margin = 6  # 3mm程度の最小余白
    max_width = width - (margin * 2)
    max_height = height - (margin * 2)
    
    # A4のアスペクト比（縦長）
    a4_aspect = height / width
    
    # 画像のアスペクト比とA4のアスペクト比を比較して最適な配置を決定
    if aspect > a4_aspect:
        # 画像が縦長の場合：高さを基準にサイズを決定
        display_height = max_height
        display_width = max_height / aspect
    else:
        # 画像が横長または正方形の場合：幅を基準にサイズを決定
        display_width = max_width
        display_height = max_width * aspect

    # 中央配置
    x = (width - display_width) / 2
    y = (height - display_height) / 2
    
    # 画像を描画
    c.drawImage(ImageReader(img), x, y, display_width, display_height)
    c.showPage()
    c.save()
    print(f"画像をA4 PDFに変換しました（余白最小化・最適化）: {output_pdf_path}")
    print(f"  元画像サイズ: {img_width}x{img_height}, アスペクト比: {aspect:.3f}")
    print(f"  表示サイズ: {display_width:.1f}x{display_height:.1f}, 余白: {margin}pt")

# コールバック通知機能はdb.pyに移動

# -------------------------------
# FAX送信処理
# -------------------------------

def process_single_fax_request(request_data):
    """単一のFAX送信リクエストを処理"""
    request_id = request_data["id"]
    file_url = request_data["file_url"]
    fax_number = request_data["fax_number"]
    print(f"FAX送信処理開始: ID={request_id}, FAX番号={fax_number}")

    try:
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        local_file_path = f"temp_fax_{timestamp}"

        # 元ファイルをダウンロード
        temp_ext = ".pdf" if file_url.lower().endswith(".pdf") else ".tmp"
        temp_path = local_file_path + temp_ext
        if not download_file(file_url, temp_path):
            update_request_status(request_id, -1, f"ファイル取得に失敗: {file_url}")
            return False

        # 🟡 PDF以外の場合はPDFに変換
        if not file_url.lower().endswith(".pdf"):
            # 永続フォルダに変換されたPDFを保存
            persistent_pdf_name = f"converted_{request_id}_{timestamp}.pdf"
            persistent_pdf_path = os.path.join(CONVERTED_PDF_FOLDER, persistent_pdf_name)
            
            # 一時PDFを作成
            temp_pdf_path = local_file_path + ".pdf"
            create_pdf_from_image(temp_path, temp_pdf_path)
            os.remove(temp_path)
            
            # 永続フォルダにコピー
            shutil.copy2(temp_pdf_path, persistent_pdf_path)
            
            send_path = temp_pdf_path
            
            # 変換後のPDFファイルパスを保存
            update_request_converted_pdf(request_id, os.path.abspath(persistent_pdf_path))
        else:
            send_path = temp_path

        # FAX送信実行
        if send_fax_with_retry(os.path.abspath(send_path), fax_number):
            update_request_status(request_id, 1)
            print(f"FAX送信完了: ID={request_id}")
            # コールバック通知を送信（成功時のみ）
            send_callback_notification(request_data)
            return True
        else:
            error_msg = "FAX送信に失敗しました"
            update_request_status(request_id, -1, error_msg)
            print(f"FAX送信失敗: ID={request_id}")
            return False

    except Exception as e:
        error_msg = str(e)
        update_request_status(request_id, -1, error_msg)
        print(f"FAX送信処理エラー: {e}")
        return False

    finally:
        # FAXドライバーがファイルを使用中の場合があるため、削除をリトライ
        for f in [local_file_path + ".pdf", local_file_path + ".tmp"]:
            if os.path.exists(f):
                for retry in range(5):
                    try:
                        os.remove(f)
                        print(f"一時ファイルを削除: {f}")
                        break
                    except PermissionError:
                        print(f"⚠ ファイル使用中のため削除保留: {f} (試行 {retry+1}/5)")
                        time.sleep(2)
                else:
                    print(f"⚠ ファイル削除失敗（使用中の可能性あり）: {f}")

# -------------------------------
# ワーカースレッド
# -------------------------------

def fax_worker():
    """FAX送信ワーカー（タスクスケジューラー用：未処理データをすべて処理して終了）"""
    print("FAX送信ワーカー開始（未処理データをすべて処理）")

    processed_count = 0
    error_count = 0

    while True:
        try:
            params_list = load_parameters()
            if not isinstance(params_list, list):
                print("パラメータデータの読み込みに失敗しました")
                break

            # 待機中のリクエストを取得し、作成日時でソート（古い順）
            pending = [p for p in params_list if p.get("status") == 0]
            if not pending:
                # 未処理データがない場合は終了
                print(f"すべてのFAX送信処理が完了しました（処理件数: {processed_count}, エラー件数: {error_count}）")
                break

            # created_atでソートして一番古いデータを取得
            pending_sorted = sorted(pending, key=lambda x: x.get("created_at", ""))
            request_data = pending_sorted[0]  # 一番古いデータ
            request_id = request_data["id"]

            print(f"📋 処理対象を取得: ID={request_id}, 作成日時={request_data.get('created_at')}")
            update_request_status(request_id, 2, "処理中")

            # 🔒 ロックでワーカー全体を排他制御
            with fax_lock:
                success = process_single_fax_request(request_data)

            if success:
                processed_count += 1
                print(f"✅ 処理完了: ID={request_id}（累計成功: {processed_count}件）")
            else:
                error_count += 1
                print(f"❌ 処理失敗: ID={request_id}（累計エラー: {error_count}件）")

            time.sleep(1)  # 次の処理まで1秒待機

        except Exception as e:
            error_count += 1
            print(f"FAXワーカーエラー: {e}")
            print(f"処理を継続します（累計エラー: {error_count}件）")
            time.sleep(1)

    print(f"FAX送信ワーカー終了（総処理件数: {processed_count + error_count}, 成功: {processed_count}, エラー: {error_count}）")

# -------------------------------
# メイン実行
# -------------------------------

if __name__ == '__main__':
    print("FAX送信ワーカー（タスクスケジューラー用）を起動中...")
    print("未処理のFAX送信リクエストをすべて処理します")
    print(f"FAXジョブAPI接続先: {API_BASE_URL}")

    try:
        fax_worker()
        print("FAX送信ワーカーが正常に終了しました")
    except Exception as e:
        print(f"FAX送信ワーカーでエラーが発生しました: {e}")
        exit(1)
