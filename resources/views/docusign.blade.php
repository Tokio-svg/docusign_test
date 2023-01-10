<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocuSign Integration</title>
  <link rel="stylesheet" href="{{asset('/css/destyle.css')}}">
  <link rel="stylesheet" href="{{asset('/css/style.css')}}">
</head>
<body>
  <article>
    <h1>トークン取得完了</h1>
    <div class="integrated__container">
      <p>access_token: {{ $access_token }}</p>
      <p>account_id: {{ $account['account_id'] }}</p>
      <p>account_name: {{ $account['account_name'] }}</p>
      <p>base_url: {{ $account['base_url'] }}</p>
    </div>

    <a class="link" href="/">Index</a>
  </article>
</body>
</html>