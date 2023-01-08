<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocuSign Test</title>
  <link rel="stylesheet" href="{{asset('/css/destyle.css')}}">
  <link rel="stylesheet" href="{{asset('/css/style.css')}}">
</head>
<body>
  <article>
    <h1>DocuSign Test</h1>

    @if(!$account_id)
      <a
        class="button"
        href="https://account-d.docusign.com/oauth/auth?response_type=code&scope=signature&client_id=2c52bf7a-0c4e-4372-9fdf-1e2e6b35e314&state=a39fh23hnf23&redirect_uri=http://localhost:8000/docusign">
        連携する
      </a>
    @else
      <div class="status__container">
        <h2>DocuSign Status</h2>
        <ol>
          <li>ID: {{$account_id}}</li>
          <li>Name: {{$account_name}}</li>
        </ol>
      </div>

      <div class="menu__container">
        <h2>Menu</h2>
        <a class="link" href="/getUsers">ユーザー一覧</a>
      </div>
    @endif
  </article>
</body>
</html>