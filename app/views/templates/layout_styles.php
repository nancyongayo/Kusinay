<style>
    :root {
        --kn-green   : #6B7A3A;
        --kn-green-d : #556030;
        --kn-green-xd: #3f4a24;
        --kn-orange  : #C4722A;
        --kn-cream   : #F5EDD6;
        --kn-dark    : #3D4A1E;
        --kn-muted   : rgba(61,74,30,.55);
        --sidebar-w  : 240px;
        --topbar-h   : 56px;
    }
    * { box-sizing: border-box; }
    body { background:#fde8d8; font-family:'Segoe UI',system-ui,sans-serif; color:var(--kn-dark); margin:0; }

    /* Topbar */
    .kn-topbar { position:fixed; top:0; left:0; right:0; height:var(--topbar-h); background:var(--kn-green-xd); display:flex; align-items:center; padding:0 1.25rem 0 calc(var(--sidebar-w) + 1.25rem); z-index:200; box-shadow:0 2px 8px rgba(0,0,0,.18); }
    .kn-topbar .page-title { font-size:1rem; font-weight:700; color:var(--kn-cream); flex:1; }
    .kn-topbar .user-chip { display:flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); border-radius:50px; padding:.3rem .85rem; color:var(--kn-cream); font-size:.88rem; }
    .kn-topbar .user-chip i { color:#f0c080; }
    .btn-signout { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.18); color:var(--kn-cream); border-radius:8px; padding:.3rem .85rem; font-size:.88rem; text-decoration:none; margin-left:.75rem; transition:background .2s; }
    .btn-signout:hover { background:rgba(255,255,255,.2); color:#fff; }

    /* Sidebar */
    .kn-sidebar { position:fixed; top:0; left:0; bottom:0; width:var(--sidebar-w); background:var(--kn-green-xd); display:flex; flex-direction:column; z-index:300; box-shadow:2px 0 12px rgba(0,0,0,.15); }
    .sidebar-brand { height:var(--topbar-h); display:flex; align-items:center; padding:0 1.25rem; border-bottom:1px solid rgba(255,255,255,.08); flex-shrink:0; }
    .sidebar-brand .brand-text { font-size:1.25rem; font-weight:800; color:var(--kn-cream); text-decoration:none; }
    .sidebar-brand .brand-text em { color:#f0c080; font-style:normal; }
    .sidebar-brand .brand-badge { font-size:.62rem; font-weight:700; background:var(--kn-orange); color:#fff; border-radius:4px; padding:.1rem .35rem; margin-left:.4rem; text-transform:uppercase; letter-spacing:.04em; }
    .sidebar-nav { flex:1; padding:1rem 0; overflow-y:auto; }
    .nav-section-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:rgba(245,237,214,.35); padding:.75rem 1.25rem .3rem; }
    .nav-item { display:flex; align-items:center; gap:.75rem; padding:.65rem 1.25rem; color:rgba(245,237,214,.65); text-decoration:none; font-size:.92rem; font-weight:500; border-left:3px solid transparent; transition:all .15s; }
    .nav-item:hover { background:rgba(255,255,255,.07); color:var(--kn-cream); border-left-color:rgba(245,237,214,.3); }
    .nav-item.active { background:rgba(255,255,255,.1); color:#fff; border-left-color:#f0c080; font-weight:600; }
    .nav-item.disabled { opacity:.4; pointer-events:none; }
    .nav-item i { font-size:1.05rem; width:20px; text-align:center; }
    .nav-item .nav-badge { margin-left:auto; background:var(--kn-orange); color:#fff; font-size:.65rem; font-weight:700; border-radius:20px; padding:.1rem .45rem; }
    .sidebar-footer { padding:1rem 1.25rem; border-top:1px solid rgba(255,255,255,.08); flex-shrink:0; }
    .sidebar-user { display:flex; align-items:center; gap:.65rem; }
    .sidebar-user .avatar { width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,.12); display:flex; align-items:center; justify-content:center; font-size:1rem; color:#f0c080; flex-shrink:0; }
    .sidebar-user .info { overflow:hidden; flex:1; }
    .sidebar-user .name { font-size:.85rem; font-weight:600; color:var(--kn-cream); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .sidebar-user .role { font-size:.72rem; color:rgba(245,237,214,.5); }
    .sidebar-user .signout-icon { color:rgba(245,237,214,.4); font-size:1rem; text-decoration:none; transition:color .15s; flex-shrink:0; }
    .sidebar-user .signout-icon:hover { color:#f87171; }

    /* Main */
    .kn-main { margin-left:var(--sidebar-w); padding-top:var(--topbar-h); min-height:100vh; }
    .kn-content { padding:1.75rem 2rem; }

    /* Mobile */
    .sidebar-toggle { display:none; position:fixed; top:.75rem; left:.75rem; z-index:400; background:var(--kn-green-xd); border:none; border-radius:8px; color:var(--kn-cream); width:38px; height:38px; align-items:center; justify-content:center; font-size:1.1rem; cursor:pointer; }
    .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:250; }
    @media (max-width:768px) {
        .kn-sidebar { transform:translateX(-100%); transition:transform .25s; }
        .kn-sidebar.open { transform:translateX(0); }
        .kn-topbar { padding-left:3.5rem; }
        .kn-main { margin-left:0; }
        .kn-content { padding:1.25rem 1rem; }
        .sidebar-toggle { display:flex; }
        .sidebar-overlay.open { display:block; }
    }
    @media print {
        .sidebar-toggle,
        .sidebar-overlay,
        .kn-sidebar,
        .kn-topbar { display:none !important; }
    }
</style>
