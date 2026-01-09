<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg);min-height:100vh;display:flex;flex-direction:column;font-size:15px}
  .center{display:flex;flex:1;align-items:center;justify-content:center;padding:32px 16px}
  .center > div{display:flex;flex-direction:column;align-items:center}
      .brand{display:flex;flex-direction:column;align-items:center;gap:20px;margin-bottom:20px}
  .logo{width:200px;height:60px;background:transparent;display:flex;align-items:center;justify-content:center}
  .logo-img{max-width:100%;height:auto;display:block;margin:0 auto}
  .card{width:450px;max-width:95%;background:var(--card);border-radius:10px;box-shadow:0 6px 20px rgba(20,20,60,0.1);overflow:hidden;margin:0 auto}
      .card .header{padding:20px 28px;border-top:4px solid var(--accent)}
      .card .header h2{margin:0;color:var(--accent);font-weight:700;font-size:22px}
      .card .body{padding:20px 28px}
      label{display:block;font-size:14px;color:var(--muted);margin-bottom:8px;font-weight:500}
      input[type=text],input[type=password]{width:100%;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;outline:none;font-size:15px;transition:all 0.2s ease}
      input:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.12);border-color:var(--accent)}
      .submit{display:block;width:100%;padding:12px 16px;background:var(--accent);color:#fff;border:none;border-radius:8px;margin-top:20px;font-weight:600;cursor:pointer;box-shadow:0 4px 16px rgba(89,102,247,0.2);font-size:15px;transition:all 0.2s ease}
      .submit:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(89,102,247,0.3)}
      .submit:active{transform:translateY(0)}
      .footer{padding:20px 0;text-align:center;color:#9ca3af;font-size:13px}
      .error{background:#fef2f2;color:#991b1b;padding:12px 14px;border-radius:8px;border:1.5px solid #fecaca;margin-bottom:14px;font-size:14px;font-weight:500}
      @media(max-width:480px){
        .card{width:100%;border-radius:8px}
        .card .header{padding:16px 20px}
        .card .header h2{font-size:20px}
        .card .body{padding:16px 20px}
        label{font-size:13px}
        input[type=text],input[type=password]{padding:10px 12px;font-size:14px}
        .submit{padding:11px 14px;font-size:14px}
        .logo{width:160px;height:48px}
      }
    </style>
  </head>
  <body>
    <div class="center">
      <div style="max-width:470px;width:100%;text-align:center">
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

              <div style="margin-bottom:16px">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
              </div>

              <div style="margin-bottom:6px">
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
