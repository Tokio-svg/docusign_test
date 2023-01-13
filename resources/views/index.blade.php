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
      @php
        $redirect_url = config('app.url');
        $integration_key = config('app.docusign_integration_key');
      @endphp
      <a
        class="integration__button"
        href="https://account-d.docusign.com/oauth/auth?response_type=code&scope=signature&client_id={{$integration_key}}&state=a39fh23hnf23&redirect_uri={{$redirect_url}}/docusign">
        連携する
      </a>
    @else
      <div class="status__container">
        <h2>DocuSign Status</h2>
        <ol>
          <li>ID: {{$account_id}}</li>
          <li>Name: {{$account_name}}</li>
          <li>Base Url: {{$base_url}}</li>
          <li>Expires_at: {{$expires_at}}</li>
        </ol>
        <form action="/release" method="post">
          @csrf
          <button class="integration__button" type="submit">連携解除</button>
        </form>
      </div>

      <div class="menu__container">
        <h2>API Menu</h2>
        <ol>
          <li><a class="link" href="/getUsers">ユーザー一覧</a></li>
          <li><a class="link" href="/requestSign">電子署名を依頼</a></li>
          <li><a class="link" href="/envelopes">封筒リスト</a></li>
        </ol>
      </div>
    @endif
  </article>
</body>
</html>