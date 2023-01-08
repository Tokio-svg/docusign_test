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
        class="integration__button"
        href="https://account-d.docusign.com/oauth/auth?response_type=code&scope=signature&client_id=2c52bf7a-0c4e-4372-9fdf-1e2e6b35e314&state=a39fh23hnf23&redirect_uri=http://localhost:8000/docusign">
        連携する
      </a>
    @else
      <div class="status__container">
        <h2>DocuSign Status</h2>
        <ol>
          <li>ID: {{$account_id}}</li>
          <li>Name: {{$account_name}}</li>
          <li>Base Url: {{$base_url}}</li>
        </ol>
      </div>

      <div class="file__container">
        <h2>File Info</h2>
        @if(!$file_path)
          <form action="/upload" method="post" enctype="multipart/form-data">
            @csrf
            <div>
              <input type="file" name="file" id="file">
            </div>
            <button class="form__button" type="submit">Upload</button>
          </form>
        @else
          <div>file_path: {{$file_path}}</div>
        @endif
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