<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard</title>
  </head>
  <body>
    <h1>User Dashboard</h1>
    <p>Welcome, user. Here you can submit attendance.</p>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">Logout</button>
    </form>
  </body>
</html>
