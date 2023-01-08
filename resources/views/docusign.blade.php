<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocuSign Window</title>
</head>
<body>
  <h1>トークン取得完了</h1>
  <p>access_token: {{ $access_token }}</p>
  <p>account_id: {{ $account['account_id'] }}</p>
  <p>account_name: {{ $account['account_name'] }}</p>
  <p>base_url: {{ $account['base_url'] }}</p>

  <a href="/">Index</a>
</body>
</html>