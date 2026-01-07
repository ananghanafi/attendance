<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg);min-height:100vh;display:flex;flex-direction:column;font-size:18px}
  .center{display:flex;flex:1;align-items:center;justify-content:center;padding:48px 20px}
  .center > div{display:flex;flex-direction:column;align-items:center}
      .brand{display:flex;flex-direction:column;align-items:center;gap:32px;margin-bottom:32px}
  .logo{width:320px;height:96px;background:transparent;display:flex;align-items:center;justify-content:center}
  .logo-img{max-width:100%;height:auto;display:block;margin:0 auto}
  .card{width:560px;max-width:95%;background:var(--card);border-radius:12px;box-shadow:0 8px 28px rgba(20,20,60,0.12);overflow:hidden;margin:0 auto}
      .card .header{padding:32px 40px;border-top:5px solid var(--accent)}
      .card .header h2{margin:0;color:var(--accent);font-weight:700;font-size:32px}
      .card .body{padding:32px 40px}
      label{display:block;font-size:18px;color:var(--muted);margin-bottom:12px;font-weight:500}
      input[type=text],input[type=password]{width:100%;padding:16px 18px;border:2px solid #e5e7eb;border-radius:10px;background:#fff;outline:none;font-size:18px;transition:all 0.2s ease}
      input:focus{box-shadow:0 0 0 4px rgba(89,102,247,0.12);border-color:var(--accent)}
      .submit{display:block;width:100%;padding:18px 20px;background:var(--accent);color:#fff;border:none;border-radius:12px;margin-top:28px;font-weight:700;cursor:pointer;box-shadow:0 8px 24px rgba(89,102,247,0.25);font-size:20px;transition:all 0.2s ease}
      .submit:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(89,102,247,0.35)}
      .submit:active{transform:translateY(0)}
      .footer{padding:32px 0;text-align:center;color:#9ca3af;font-size:17px}
      .error{background:#fef2f2;color:#991b1b;padding:16px 18px;border-radius:10px;border:2px solid #fecaca;margin-bottom:18px;font-size:17px;font-weight:500}
      @media(max-width:640px){
        .card{width:100%;border-radius:8px}
        .card .header{padding:24px 28px}
        .card .header h2{font-size:28px}
        .card .body{padding:24px 28px}
        label{font-size:17px}
        input[type=text],input[type=password]{padding:14px 16px;font-size:17px}
        .submit{padding:16px 18px;font-size:19px}
        .logo{width:280px;height:84px}
      }
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

              <div style="margin-bottom:20px">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
              </div>

              <div style="margin-bottom:8px">
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
