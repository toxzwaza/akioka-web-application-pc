<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAX API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 8px; }
        .desc { color: #555; margin-bottom: 16px; }
        .row { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input, select, textarea, button {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        textarea { min-height: 160px; font-family: Consolas, monospace; }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .result {
            white-space: pre-wrap;
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 12px;
            min-height: 220px;
        }
    </style>
</head>
<body>
    <h1>FAX API 疎通確認</h1>
    <p class="desc">`/api/fax/*` エンドポイントをブラウザから直接試験できます。</p>

    <div class="grid">
        <div>
            <div class="row">
                <label for="baseUrl">Base URL</label>
                <input id="baseUrl" type="text" value="{{ url('/api') }}">
            </div>

            <div class="row">
                <label for="token">Bearer Token</label>
                <input id="token" type="text" placeholder="FAX_API_TOKEN を入力">
            </div>

            <div class="row">
                <label for="endpoint">Endpoint</label>
                <select id="endpoint">
                    <option value="/fax/jobs">GET /fax/jobs</option>
                    <option value="/fax/jobs">POST /fax/jobs</option>
                    <option value="/fax/jobs/{id}">GET /fax/jobs/{id}</option>
                    <option value="/fax/jobs/{id}/status">PATCH /fax/jobs/{id}/status</option>
                    <option value="/fax/jobs/{id}/converted-pdf">PATCH /fax/jobs/{id}/converted-pdf</option>
                    <option value="/fax/jobs/retry-errors">POST /fax/jobs/retry-errors</option>
                    <option value="/fax/jobs/{id}/retry">POST /fax/jobs/{id}/retry</option>
                    <option value="/fax/jobs/completed">DELETE /fax/jobs/completed</option>
                    <option value="/fax/jobs">DELETE /fax/jobs</option>
                    <option value="/fax/initial-orders/{id}">GET /fax/initial-orders/{id}</option>
                    <option value="/fax/initial-orders/{id}">PATCH /fax/initial-orders/{id}</option>
                </select>
            </div>

            <div class="row">
                <label for="method">HTTP Method</label>
                <select id="method">
                    <option>GET</option>
                    <option>POST</option>
                    <option>PATCH</option>
                    <option>DELETE</option>
                </select>
            </div>

            <div class="row">
                <label for="pathId">Path ID ({id} 置換用)</label>
                <input id="pathId" type="text" placeholder="UUIDまたはinitial_order_id">
            </div>

            <div class="row">
                <label for="requestBody">Request JSON</label>
                <textarea id="requestBody">{
  "file_url": "https://example.com/sample.pdf",
  "fax_number": "0312345678",
  "request_user": "api_test_user",
  "file_name": "sample.pdf",
  "callback_url": "https://example.com/callback",
  "order_destination": "test_supplier"
}</textarea>
            </div>

            <div class="row">
                <button id="sendBtn" type="button">送信</button>
            </div>
        </div>

        <div>
            <div class="row">
                <label>Response</label>
                <div id="response" class="result">ここにレスポンスを表示します</div>
            </div>
        </div>
    </div>

    <script>
        const endpointEl = document.getElementById('endpoint');
        const methodEl = document.getElementById('method');
        const bodyEl = document.getElementById('requestBody');
        const baseUrlEl = document.getElementById('baseUrl');
        const tokenEl = document.getElementById('token');
        const responseEl = document.getElementById('response');
        const sendBtn = document.getElementById('sendBtn');
        const pathIdEl = document.getElementById('pathId');

        endpointEl.addEventListener('change', () => {
            const value = endpointEl.value;
            if (value.includes('/retry-errors')) methodEl.value = 'POST';
            else if (value.includes('/status') || value.includes('/converted-pdf') || value.includes('/initial-orders/{id}')) methodEl.value = value.includes('/initial-orders/{id}') ? 'PATCH' : 'PATCH';
            else if (value.includes('/completed') || value === '/fax/jobs') methodEl.value = value === '/fax/jobs' ? 'GET' : 'DELETE';
            else methodEl.value = 'GET';
        });

        sendBtn.addEventListener('click', async () => {
            try {
                const baseUrl = baseUrlEl.value.replace(/\/$/, '');
                const endpoint = endpointEl.value.replace('{id}', encodeURIComponent(pathIdEl.value.trim()));
                const method = methodEl.value;
                const token = tokenEl.value.trim();
                const url = baseUrl + endpoint;

                const headers = { 'Accept': 'application/json' };
                if (token) headers['Authorization'] = 'Bearer ' + token;

                const options = { method, headers };
                if (method !== 'GET' && method !== 'DELETE') {
                    headers['Content-Type'] = 'application/json';
                    const parsed = JSON.parse(bodyEl.value || '{}');
                    options.body = JSON.stringify(parsed);
                }

                const res = await fetch(url, options);
                const text = await res.text();
                let parsedText = text;
                try {
                    parsedText = JSON.stringify(JSON.parse(text), null, 2);
                } catch (e) {
                    // 文字列のまま表示
                }

                responseEl.textContent = 'HTTP ' + res.status + '\n\n' + parsedText;
            } catch (error) {
                responseEl.textContent = 'Error:\n' + error.message;
            }
        });
    </script>
</body>
</html>
