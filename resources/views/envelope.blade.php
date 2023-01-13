<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocuSign Envelope Info</title>
  <link rel="stylesheet" href="{{asset('/css/destyle.css')}}">
  <link rel="stylesheet" href="{{asset('/css/style.css')}}">
</head>
<body>
  <article>
    <h1>Envelope Info</h1>

    <div class="envelope-info__container">
      <ol>
        @foreach($params as $key => $value)
          <li class="envelope-info__item">
            <span>{{$key}}:</span>
            {{$value}}
          </li>
        @endforeach
      </ol>
    </div>

    <div>
      <a class="integration__button" href="/downloadDocuments/{{$params['envelopeId']}}">ダウンロード</a>
    </div>

    <a class="link" href="/envelopes">一覧に戻る</a>
    <a class="link" href="/">index</a>
  </article>
</body>
</html>