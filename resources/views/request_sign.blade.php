<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocuSign Request Sign</title>
  <link rel="stylesheet" href="{{asset('/css/destyle.css')}}">
  <link rel="stylesheet" href="{{asset('/css/style.css')}}">
</head>
<body>
  <article>
    <h1>Request Sign</h1>

    <form action="/requestSign" method="post">
      @csrf
      <div>
        <label for="signer_email">署名者メールアドレス</label>
        <input class="form__input" type="email" name="signer_email" required>
      </div>
      <button class="form__button" type="submit">送信</button>
    </form>


    <a class="link" href="/">index</a>
  </article>
</body>
</html>