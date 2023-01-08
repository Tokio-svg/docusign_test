<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocuSign Envelope List</title>
  <link rel="stylesheet" href="{{asset('/css/destyle.css')}}">
  <link rel="stylesheet" href="{{asset('/css/style.css')}}">
</head>
<body>
  <article>
    <h1>Envelope List</h1>

    @if($envelopes)
      <div class="envelope-list__container">
        <ol>
            @foreach($envelopes as $envelope)
              <li class="envelope-list__item">
                <a href="/envelope/{{$envelope['id']}}">
                  <span>envelope_id:</span>{{$envelope['envelope_id']}}
                </a>
              </li>
            @endforeach
          </ol>
      </div>
    @endif
      <a class="link" href="/">index</a>
  </article>
</body>
</html>