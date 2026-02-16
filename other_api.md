# VPS API側で必要な対応

## 概要

発注依頼一覧画面の複数ファイルアップロード対応に伴い、VPS API側（https://akioka.cloud）で必要な対応をまとめます。

## 現在のVPS API

### ファイルアップロードAPI

- **エンドポイント**: `POST /api/order_request/upload_file`
- **機能**: 単一ファイルをアップロード
- **保存先**: `storage/app/public/order_request/`
- **レスポンス形式**:
  ```json
  {
    "status": true,
    "message": "ファイルアップロード成功",
    "file_url": "storage/order_request/20250409143000.pdf"
  }
  ```

## 必要な対応

### 1. ファイルアップロードAPI: 変更不要

- 現在のAPIで問題ありません
- 単一ファイルを複数回アップロードすることで複数ファイルに対応できます
- レスポンスの`file_url`を取得して使用します

### 2. ファイル削除API: 新規作成が必要

#### エンドポイント仕様

- **メソッド**: `DELETE`
- **URL**: `/api/order_request/delete_file`
- **パラメータ**:
  - `file_path` (string, required): 削除するファイルパス（例: `storage/order_request/20250409143000.pdf`）

#### 処理内容

1. リクエストから`file_path`を取得
2. ファイルの存在確認
3. 物理ファイルを削除（`Storage::delete()`など）
4. 成功/失敗をレスポンスで返す

#### レスポンス形式

**成功時**:
```json
{
  "status": true,
  "message": "ファイル削除成功"
}
```

**失敗時**:
```json
{
  "status": false,
  "message": "ファイルが見つかりません"
}
```

#### 実装例（Laravel）

```php
public function deleteFile(Request $request)
{
    $status = true;
    $message = '';
    
    $file_path = $request->input('file_path');
    
    try {
        // ファイル存在チェック
        if (!Storage::disk('public')->exists($file_path)) {
            throw new \Exception('ファイルが見つかりません。');
        }
        
        // ファイル削除
        Storage::disk('public')->delete($file_path);
        
        $message = 'ファイル削除成功';
    } catch (\Exception $e) {
        $status = false;
        $message = $e->getMessage();
    }
    
    return response()->json([
        'status' => $status,
        'message' => $message,
    ]);
}
```

#### 注意事項

- このAPIがない場合、ファイル削除機能は完全には実装できません
  - データベースからは削除できますが、実際のファイルは残ります
  - ストレージ容量の無駄になります
- セキュリティ対策として、ファイルパスの検証を必ず行ってください
  - パストラバーサル攻撃を防ぐため、`order_request`ディレクトリ内のファイルのみ削除可能にする

### 3. file_path_subの管理: 変更不要

- `file_path_sub`はこのアプリ側（akioka-web-application-pc）のデータベースで管理します
- VPS API側で`file_path_sub`を更新する必要はありません
- VPS API側はファイルのアップロード・削除のみを担当します

## 実装の流れ

### ファイルアップロード時

1. このアプリ側からVPS APIにファイルを送信
2. VPS APIがファイルを保存し、`file_url`を返す
3. このアプリ側で`file_url`を取得
4. `file_path_sub`（JSON配列）に追加して保存

### ファイル削除時

1. このアプリ側からVPS APIの削除エンドポイントを呼び出し
2. VPS APIが物理ファイルを削除
3. このアプリ側で`file_path_sub`配列から該当要素を削除

## 実装優先度

1. **高**: ファイル削除APIの実装（ファイル削除機能を完全に動作させるため）
2. **低**: ファイルアップロードAPIの変更（現在のAPIで問題なし）
