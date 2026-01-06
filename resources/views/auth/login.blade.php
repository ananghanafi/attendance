<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg);min-height:100vh;display:flex;flex-direction:column}
  .center{display:flex;flex:1;align-items:center;justify-content:center;padding:48px 16px}
  .center > div{display:flex;flex-direction:column;align-items:center}
      .brand{display:flex;flex-direction:column;align-items:center;gap:28px;margin-bottom:18px}
  .logo{width:240px;height:72px;background:transparent;display:flex;align-items:center;justify-content:center}
  .logo-img{max-width:100%;height:auto;display:block;margin:0 auto}
  .card{width:420px;background:var(--card);border-radius:6px;box-shadow:0 6px 20px rgba(20,20,60,0.08);overflow:hidden;margin:0 auto}
      .card .header{padding:20px 28px;border-top:4px solid var(--accent)}
      .card .header h2{margin:0;color:var(--accent);font-weight:600}
      .card .body{padding:22px 28px}
      label{display:block;font-size:13px;color:var(--muted);margin-bottom:8px}
      input[type=text],input[type=password]{width:100%;padding:12px 14px;border:1px solid #eef0f6;border-radius:6px;background:#fff;outline:none;font-size:15px}
      input:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--accent)}
      .submit{display:block;width:100%;padding:12px 14px;background:var(--accent);color:#fff;border:none;border-radius:8px;margin-top:18px;font-weight:600;cursor:pointer;box-shadow:0 6px 18px rgba(89,102,247,0.18)}
      .footer{padding:28px 0;text-align:center;color:#9ca3af;font-size:14px}
      .error{background:#fff0f0;color:#981b1b;padding:10px;border-radius:6px;border:1px solid #ffd6d6;margin-bottom:12px}
      @media(max-width:480px){.card{width:100%}}
    </style>
  </head>
  <body>
    <div class="center">
      <div style="max-width:920px;width:100%;text-align:center">
        <div class="brand">
          <div class="logo">
            <!-- Logo image should be placed at public/images/logo.png -->
            @if(file_exists(public_path('images/logo.png')))
              <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo-img">
            @endif
          </div>
        </div>

        <div class="card" role="main">
          <div class="header">
            <h2>Login</h2>
          </div>

          <div class="body">
            @if($errors->any())
              <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
              @csrf

              <div style="margin-bottom:14px">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
              </div>

              <div style="margin-bottom:4px">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
              </div>

              <button type="submit" class="submit">Login</button>
            </form>
          </div>
        </div>

        <div class="footer">Copyright © </div>
      </div>
    </div>
  </body>
</html>
