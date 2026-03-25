import requests
import os
from datetime import datetime

# FAX API接続先（ベタ書き設定）
API_BASE_URL = "https://akioka-sub.cloud/api"
API_TOKEN = "akioka_administrator"
API_TIMEOUT = int(os.getenv("FAX_API_TIMEOUT", "30"))


def _headers():
    headers = {"Accept": "application/json"}
    if API_TOKEN:
        headers["Authorization"] = f"Bearer {API_TOKEN}"
    return headers


def _request(method, path, payload=None, timeout=None):
    """本アプリのFAX APIを呼び出す共通関数"""
    url = f"{API_BASE_URL}{path}"
    request_timeout = timeout if timeout is not None else API_TIMEOUT
    response = requests.request(
        method=method,
        url=url,
        json=payload,
        headers=_headers(),
        timeout=request_timeout
    )
    response.raise_for_status()

    if not response.content:
        return {}

    body = response.json()
    if isinstance(body, dict) and body.get("success") is False:
        raise RuntimeError(body.get("error", "API request failed"))
    return body


# -------------------------------
# FAXパラメータデータベース操作
# -------------------------------

def load_parameters():
    print("[load_parameters] 取得開始")
    try:
        res = _request("GET", "/fax/jobs")
        return res.get("requests", [])
    except Exception as e:
        print(f"[load_parameters] エラー: {e}")
        return []


def add_fax_request(file_url, fax_number, request_user=None, file_name=None, callback_url=None, order_destination=None, initial_order_id=None):
    print(f"[add_fax_request] 開始: {fax_number}")
    try:
        payload = {
            "file_url": file_url,
            "fax_number": fax_number,
            "request_user": request_user,
            "file_name": file_name,
            "callback_url": callback_url,
            "order_destination": order_destination,
            "initial_order_id": initial_order_id
        }
        res = _request("POST", "/fax/jobs", payload)
        request_id = res.get("fax_parameter_id") or res.get("id")

        return {
            "id": request_id,
            "file_url": file_url,
            "fax_number": fax_number,
            "status": 0,
            "created_at": res.get("created_at"),
            "updated_at": res.get("updated_at"),
            "error_message": None,
            "converted_pdf_path": None,
            "request_user": res.get("request_user", request_user),
            "file_name": res.get("file_name", file_name),
            "callback_url": res.get("callback_url", callback_url),
            "order_destination": res.get("order_destination", order_destination)
        }
    except Exception as e:
        print(f"[add_fax_request] エラー: {e}")
        raise e


def update_request_status(request_id, status, error_message=None):
    try:
        payload = {"status": status, "error_message": error_message}
        _request("PATCH", f"/fax/jobs/{request_id}/status", payload)
    except Exception as e:
        print(f"[update_request_status] エラー: {e}")
        raise e


def update_request_converted_pdf(request_id, pdf_path):
    try:
        payload = {"converted_pdf_path": pdf_path}
        _request("PATCH", f"/fax/jobs/{request_id}/converted-pdf", payload)
    except Exception as e:
        print(f"[update_request_converted_pdf] エラー: {e}")
        raise e


def get_request_by_id(request_id):
    try:
        res = _request("GET", f"/fax/jobs/{request_id}")
        return res.get("request")
    except Exception as e:
        print(f"[get_request_by_id] エラー: {e}")
        return None


def clear_completed_requests():
    try:
        res = _request("DELETE", "/fax/jobs/completed")
        return int(res.get("deleted_count", 0))
    except Exception as e:
        print(f"[clear_completed_requests] エラー: {e}")
        raise e


def retry_error_requests():
    try:
        res = _request("POST", "/fax/jobs/retry-errors")
        return int(res.get("retry_count", 0))
    except Exception as e:
        print(f"[retry_error_requests] エラー: {e}")
        raise e


def retry_request_by_id(request_id):
    try:
        res = _request("POST", f"/fax/jobs/{request_id}/retry")
        return True, res.get("message", "再送キューへ戻しました")
    except Exception as e:
        print(f"[retry_request_by_id] エラー: {e}")
        return False, str(e)


def send_callback_notification(request_data):
    try:
        url = request_data.get("callback_url")
        if not url:
            return

        payload = {
            **request_data,
            "status": "completed",
            "completed_at": datetime.now().isoformat()
        }

        requests.post(url, json=payload, timeout=30)

    except Exception as e:
        print(f"[send_callback_notification] エラー: {e}")

def clear_all_requests():
    """fax_parameters テーブルの全データを削除"""
    try:
        res = _request("DELETE", "/fax/jobs")
        return int(res.get("deleted_count", 0))
    except Exception as e:
        print(f"[clear_all_requests] エラー: {e}")
        raise e


def get_initial_order_id(initial_order_id):
    """initial_ordersテーブルから指定IDのデータを取得（API経由）"""
    try:
        res = _request("GET", f"/fax/initial-orders/{initial_order_id}")
        row = res.get("initial_order") or {}
        return row.get("id")
    except Exception as e:
        print(f"[get_initial_order_id] エラー: {e}")
        return None


def update_initial_order_fax_parameter_id(initial_order_id, fax_request_id):
    """initial_ordersテーブルのfax_parameter_idを更新（API経由）"""
    try:
        payload = {"fax_parameter_id": fax_request_id}
        _request("PATCH", f"/fax/initial-orders/{initial_order_id}", payload)
        return True
    except Exception as e:
        print(f"[update_initial_order_fax_parameter_id] エラー: {e}")
        return False