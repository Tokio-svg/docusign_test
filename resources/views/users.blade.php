<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DocuSign User List</title>
  <link rel="stylesheet" href="{{asset('/css/destyle.css')}}">
  <link rel="stylesheet" href="{{asset('/css/style.css')}}">
</head>
<body>
  <article>
    <h1>User List</h1>

    @if($users)
      <div class="user-list__container">
        @foreach($users as $user)
          <div class="user-list__item">
            <ol>
              <li><span>userName:</span>{{$user['userName']}}</li>
              <li><span>userId:</span>{{$user['userId']}}</li>
              <li><span>userType:</span>{{$user['userType']}}</li>
              <li><span>isAdmin:</span>{{$user['isAdmin']}}</li>
              <li><span>userStatus:</span>{{$user['userStatus']}}</li>
              <li><span>email:</span>{{$user['email']}}</li>
            </ol>
          </div>
        @endforeach
      </div>
    @endif
      <a class="link" href="/">index</a>
  </article>
</body>
</html>