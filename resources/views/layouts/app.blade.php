<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield("title", "AbsensiWika")</title>
<style>
:root{--topbar-height:70px;--sidebar-width:260px;--sidebar-collapsed:70px;--primary:#5966f7;--primary-dark:#4854d8;--bg:#f4f7fa;--text:#1f2937;--text-muted:#6b7280}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Inter,system-ui,Arial;background:var(--bg);font-size:17px;color:var(--text)}
.topbar{position:fixed;top:0;left:0;right:0;height:var(--topbar-height);background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 32px;box-shadow:0 4px 12px rgba(89,102,247,0.15);z-index:100}
.topbar .brand{font-size:22px;font-weight:700;letter-spacing:-0.02em;display:flex;align-items:center;gap:16px}
.hamburger{width:36px;height:36px;background:rgba(255,255,255,0.15);border:none;border-radius:8px;cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;transition:all 0.2s ease}
.hamburger:hover{background:rgba(255,255,255,0.25)}
.hamburger span{display:block;width:20px;height:2.5px;background:#fff;border-radius:2px;transition:all 0.3s ease}
.hamburger.active span:nth-child(1){transform:rotate(45deg) translate(6px, 6px)}
.hamburger.active span:nth-child(2){opacity:0}
.hamburger.active span:nth-child(3){transform:rotate(-45deg) translate(6px, -6px)}
.topbar .right{display:flex;align-items:center;gap:20px}
.topbar .profile{position:relative;display:flex;align-items:center;gap:12px;padding:8px 14px;background:rgba(255,255,255,0.15);border-radius:10px;transition:all 0.2s ease;cursor:pointer}
.topbar .profile:hover{background:rgba(255,255,255,0.25)}
.topbar .profile-avatar{width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.3);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#fff;border:2px solid rgba(255,255,255,0.5)}
.topbar .profile-info{display:flex;flex-direction:column;gap:2px}
.topbar .profile-name{font-size:16px;font-weight:600;line-height:1.2}
.topbar .profile-role{font-size:13px;opacity:0.85}
.profile-dropdown{position:absolute;top:calc(100% + 8px);right:0;min-width:220px;background:#fff;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.15);opacity:0;visibility:hidden;transform:translateY(-10px);transition:all 0.2s ease;z-index:200}
.profile.active .profile-dropdown{opacity:1;visibility:visible;transform:translateY(0)}
.profile-dropdown-header{padding:16px;border-bottom:1px solid #e5e7eb}
.profile-dropdown-name{font-size:16px;font-weight:600;color:var(--text);margin-bottom:4px}
.profile-dropdown-role{font-size:14px;color:var(--text-muted)}
.profile-dropdown-menu{padding:8px}
.profile-dropdown-item{display:flex;align-items:center;gap:12px;width:100%;padding:12px 14px;background:none;border:none;border-radius:8px;color:var(--text);font-size:15px;font-weight:500;text-align:left;cursor:pointer;transition:all 0.2s ease}
.profile-dropdown-item:hover{background:#f3f4f6}
.profile-dropdown-item.logout{color:#dc2626}
.profile-dropdown-item.logout:hover{background:#fef2f2}
.sidebar{position:fixed;top:var(--topbar-height);left:0;width:var(--sidebar-width);height:calc(100vh - var(--topbar-height));background:#fff;box-shadow:2px 0 12px rgba(0,0,0,0.05);overflow-y:auto;overflow-x:hidden;z-index:50;transition:width 0.3s ease,transform 0.3s ease}
.sidebar.collapsed{width:var(--sidebar-collapsed)}
.sidebar-menu{padding:20px 0}
.sidebar-item{display:flex;align-items:center;gap:12px;padding:14px 24px;color:var(--text);text-decoration:none;font-size:16px;font-weight:500;transition:all 0.2s ease;border-left:3px solid transparent;white-space:nowrap}
.sidebar.collapsed .sidebar-item{padding:14px 20px;justify-content:center}
.sidebar-item .icon{font-size:20px;min-width:24px;text-align:center}
.sidebar-item .text{transition:opacity 0.2s ease}
.sidebar.collapsed .sidebar-item .text{opacity:0;width:0;overflow:hidden}
.sidebar-item:hover{background:#f3f4f6;border-left-color:var(--primary);color:var(--primary)}
.sidebar-item.active{background:#eef2ff;border-left-color:var(--primary);color:var(--primary);font-weight:600}
.main{margin-left:var(--sidebar-width);margin-top:var(--topbar-height);padding:32px;min-height:calc(100vh - var(--topbar-height));transition:margin-left 0.3s ease}
.main.sidebar-collapsed{margin-left:var(--sidebar-collapsed)}
@media(max-width:900px){
.topbar{padding:0 16px}
.topbar .brand{font-size:18px}
.topbar .profile-info{display:none}
.sidebar{transform:translateX(-100%);width:var(--sidebar-width);box-shadow:4px 0 20px rgba(0,0,0,0.15)}
.sidebar.open{transform:translateX(0)}
.sidebar.collapsed{width:var(--sidebar-width);transform:translateX(-100%)}
.sidebar.collapsed.open{transform:translateX(0)}
.main{margin-left:0;padding:20px}
.main.sidebar-collapsed{margin-left:0}
}
@yield("styles")
</style>
</head>
<body>
<div class="topbar">
<div class="brand">
<button class="hamburger" onclick="toggleSidebar()"><span></span><span></span><span></span></button>
<span>AbsensiWika</span>
</div>
<div class="right">
<div class="profile" id="profileMenu" onclick="toggleProfile()">
<div class="profile-avatar">@php echo strtoupper(substr(auth()->user()->nama ?? auth()->user()->username, 0, 1)); @endphp</div>
<div class="profile-info">
<div class="profile-name">{{ auth()->user()->nama ?? auth()->user()->username }}</div>
</div>
<div class="profile-dropdown">
<div class="profile-dropdown-header">
<div class="profile-dropdown-name">{{ auth()->user()->nama ?? auth()->user()->username }}</div>
</div>
<div class="profile-dropdown-menu">
<form method="POST" action="{{ route("logout") }}" style="margin:0">
@csrf
<button type="submit" class="profile-dropdown-item logout"><span></span><span>Logout</span></button>
</form>
</div>
</div>
</div>
</div>
</div>
<div class="sidebar" id="sidebar">
<div class="sidebar-menu">
<a href="{{ route("dashboard") }}" class="sidebar-item @if(request()->routeIs("dashboard")) active @endif"><span class="icon">🏠</span><span class="text">Dashboard</span></a>
@if($isAdmin ?? false)
<a href="{{ route("admin.kalender") }}" class="sidebar-item @if(request()->routeIs("admin.kalender*") || request()->routeIs("kalender.*")) active @endif"><span class="icon">📅</span><span class="text">Kalender Kerja</span></a>
<a href="{{ route("pengajuan.index") }}" class="sidebar-item @if(request()->routeIs("pengajuan.*")) active @endif"><span class="icon">📋</span><span class="text">Pengajuan WFO</span></a>
<a href="{{ route("settings.index") }}" class="sidebar-item @if(request()->routeIs("settings.*") || request()->routeIs("users.*") || request()->routeIs("biro.*") || request()->routeIs("jabatan.*") || request()->routeIs("role.*")) active @endif"><span class="icon">⚙️</span><span class="text">Setting User</span></a>
@else
{{-- Menu untuk user biasa --}}
<a href="{{ route("pengajuan.index") }}" class="sidebar-item @if(request()->routeIs("pengajuan.*")) active @endif"><span class="icon">📋</span><span class="text">Pengajuan WFO</span></a>
@endif
</div>
</div>
<div class="main" id="mainContent">
@yield("content")
</div>
<script>
function toggleProfile(){const p=document.getElementById("profileMenu");p.classList.toggle("active");event.stopPropagation()}
document.addEventListener("click",function(e){const p=document.getElementById("profileMenu");if(p&&!p.contains(e.target)){p.classList.remove("active")}});
function toggleSidebar(){const s=document.getElementById("sidebar");const m=document.getElementById("mainContent");const h=document.querySelector(".hamburger");const isMobile=window.innerWidth<=900;if(isMobile){s.classList.toggle("open");if(s.classList.contains("open")){h.classList.add("active")}else{h.classList.remove("active")}}else{s.classList.toggle("collapsed");m.classList.toggle("sidebar-collapsed");if(s.classList.contains("collapsed")){h.classList.remove("active")}else{h.classList.add("active")}}}
window.addEventListener("resize",function(){const s=document.getElementById("sidebar");const m=document.getElementById("mainContent");const h=document.querySelector(".hamburger");if(window.innerWidth>900){s.classList.remove("open");h.classList.remove("active")}else{s.classList.remove("collapsed");m.classList.remove("sidebar-collapsed");h.classList.remove("active")}});
document.addEventListener("click",function(e){if(window.innerWidth<=900){const s=document.getElementById("sidebar");const h=document.querySelector(".hamburger");if(s.classList.contains("open")&&!s.contains(e.target)&&!h.contains(e.target)){s.classList.remove("open");h.classList.remove("active")}}});
@yield("scripts")
</script>
</body>
</html>
