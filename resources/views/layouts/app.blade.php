<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{ $title ?? 'Dashboard' }}</title>
		<style>
			:root{
				--bg:#f5f6fa;
				--surface:#ffffff;
				--primary:#5f73ff;
				--primary-2:#6c7bff;
				--text:#111827;
				--muted:#6b7280;
				--border:#e7eaf3;

				--sidebar-w:260px;
			}
			*{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
			body{margin:0;background:var(--bg);color:var(--text);overflow-x:hidden}

			.layout{display:flex;min-height:100vh}

			/* Sidebar drawer (overlay) */
			.sidebar{position:fixed;left:0;top:0;bottom:0;z-index:30;transform:translateX(-100%);width:var(--sidebar-w);background:var(--surface);border-right:1px solid var(--border);padding:14px 10px;transition:transform .18s ease;}
			.sidebar.expanded{transform:translateX(0)}

			.brand{display:flex;align-items:center;gap:12px;padding:6px 8px;margin-bottom:8px}
			.logo{width:38px;height:38px;border-radius:10px;background:transparent;display:flex;align-items:center;justify-content:center;color:#111827;font-weight:900;letter-spacing:.5px}

			.nav{margin-top:10px;display:flex;flex-direction:column;gap:6px}
			.nav a,.nav button{display:flex;align-items:center;gap:12px;width:100%;padding:10px 10px;border-radius:12px;border:1px solid transparent;background:transparent;color:var(--text);text-decoration:none;cursor:pointer}
			.nav a:hover,.nav button:hover{background:#f3f5ff;border-color:#eef1ff}
			.nav .icon{width:22px;display:flex;justify-content:center;opacity:.9}
			.nav .label{white-space:nowrap}

			.section{margin-top:12px;padding:10px 10px 6px;color:var(--muted);font-size:12px;}
			.nav .active{background:#eef1ff;border-color:#e0e6ff}

			.submenu{margin-left:6px;display:none;flex-direction:column;gap:6px}
			.submenu a{padding-left:36px}
			.submenu.open{display:flex}

			/* Main */
			.main{flex:1;display:flex;flex-direction:column;width:100%}

			/* Topbar (never shifts) */
			.topbar{height:64px;background:var(--primary-2);color:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 18px;position:sticky;top:0;z-index:20}
			.left{display:flex;align-items:center;gap:12px}
			.hamburger{width:40px;height:40px;border:none;border-radius:10px;background:rgba(255,255,255,.18);color:#fff;cursor:pointer;font-size:18px;box-shadow:0 6px 18px rgba(0,0,0,.12)}
			.hamburger:hover{background:rgba(255,255,255,.26)}
			.pageTitle{font-weight:800}

			.userMenu{position:relative}
			.userBtn{border:1px solid rgba(255,255,255,.22);background:rgba(255,255,255,.12);color:#fff;border-radius:12px;padding:10px 12px;cursor:pointer;display:flex;align-items:center;gap:10px;box-shadow:0 6px 18px rgba(0,0,0,.10)}
			.userBtn:hover{background:rgba(255,255,255,.20)}
			.avatar{width:28px;height:28px;border-radius:100px;background:rgba(255,255,255,.26);display:flex;align-items:center;justify-content:center;font-weight:800}
			.dropdown{position:absolute;right:0;top:52px;background:var(--surface);color:var(--text);border:1px solid var(--border);border-radius:12px;min-width:220px;box-shadow:0 14px 50px rgba(0,0,0,.12);padding:8px;display:none}
			.dropdown.open{display:block}
			.dropdown form{margin:0}
			.dropdown a,.dropdown button{width:100%;text-align:left;border:none;background:transparent;padding:10px 10px;border-radius:10px;cursor:pointer;color:var(--text);text-decoration:none}
			.dropdown a:hover,.dropdown button:hover{background:#f6f7fb}
			.danger{color:#fff;background:#e11d48}
			.danger:hover{background:#be123c}

			.content{padding:22px}
			@media(max-width:900px){
				.content{padding:16px}
			}

			/* Backdrop */
			.backdrop{display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:25}
			.backdrop.open{display:block}
			body.no-scroll{overflow:hidden}
		</style>
	</head>
	<body>
		<div class="backdrop" id="backdrop"></div>
		<div class="layout">
			<aside class="sidebar" id="sidebar">
				<div class="brand">
					<div class="logo">WG</div>
				</div>

				<nav class="nav">
					<div class="section">MENU UTAMA</div>
					<a href="{{ route('dashboard') }}" class="{{ (isset($active) && $active === 'dashboard') ? 'active' : '' }}">
						<span class="icon">🏠</span>
						<span class="label">Dashboard</span>
					</a>

					<button type="button" id="userSettingsToggle" aria-expanded="false">
						<span class="icon">👤</span>
						<span class="label">Setting User</span>
						<span class="label" style="margin-left:auto;opacity:.7">▾</span>
					</button>
					<div class="submenu" id="userSettingsMenu">
						<a href="{{ route('users.create') }}">Add User</a>
						<a href="{{ route('users.index') }}">View User</a>
					</div>

					<a href="{{ route('admin.kalender') }}" class="{{ (isset($active) && $active === 'kalender') ? 'active' : '' }}">
						<span class="icon">📅</span>
						<span class="label">Kalender Kerja</span>
					</a>
				</nav>
			</aside>

			<div class="main">
				<header class="topbar">
					<div class="left">
						<button class="hamburger" type="button" id="hamburger" aria-label="Toggle sidebar">☰</button>
						<div class="pageTitle">{{ $pageTitle ?? ($title ?? 'Dashboard') }}</div>
					</div>

					<div class="userMenu">
						<button class="userBtn" type="button" id="userBtn" aria-haspopup="true" aria-expanded="false">
							<span class="avatar">👤</span>
							<span>{{ $userName ?? 'User' }}</span>
							<span style="opacity:.8">▾</span>
						</button>
						<div class="dropdown" id="userDropdown">
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<button type="submit" class="danger">Logout</button>
							</form>
						</div>
					</div>
				</header>

				<main class="content">
					@if(session('status'))
						<div style="margin:0 0 12px;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:10px;border-radius:10px">{{ session('status') }}</div>
					@endif

					{{ $slot }}
				</main>
			</div>
		</div>

		<script>
			(function(){
				const sidebar = document.getElementById('sidebar');
				const hamburger = document.getElementById('hamburger');
				const toggle = document.getElementById('userSettingsToggle');
				const menu = document.getElementById('userSettingsMenu');
				const backdrop = document.getElementById('backdrop');

				function setExpanded(expanded){
					sidebar && sidebar.classList.toggle('expanded', expanded);
					if (backdrop) backdrop.classList.toggle('open', Boolean(expanded));
					document.body.classList.toggle('no-scroll', Boolean(expanded));
					try{ localStorage.setItem('wg_sidebar', expanded ? '1' : '0'); }catch(e){}
				}

				let expanded = false;
				try{ expanded = localStorage.getItem('wg_sidebar') === '1'; }catch(e){}
				setExpanded(expanded);

				hamburger && hamburger.addEventListener('click', () => {
					setExpanded(!sidebar.classList.contains('expanded'));
				});
				backdrop && backdrop.addEventListener('click', () => setExpanded(false));

				// submenu
				if (menu) menu.classList.remove('open');
				toggle && toggle.setAttribute('aria-expanded', 'false');
				toggle && toggle.addEventListener('click', () => {
					if (!sidebar.classList.contains('expanded')) setExpanded(true);
					const willOpen = !menu.classList.contains('open');
					menu.classList.toggle('open', willOpen);
					toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
				});

				// user dropdown
				const userBtn = document.getElementById('userBtn');
				const dropdown = document.getElementById('userDropdown');
				function closeDd(){ dropdown && dropdown.classList.remove('open'); userBtn && userBtn.setAttribute('aria-expanded','false'); }
				userBtn && userBtn.addEventListener('click', (e) => {
					e.stopPropagation();
					const open = dropdown.classList.toggle('open');
					userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
				});
				document.addEventListener('click', () => closeDd());
			})();
		</script>
	</body>
</html>
