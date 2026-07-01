<style>
    :root {
        --kn-green   : #5A7038;
        --kn-green-d : #4A5D2E;
        --kn-green-xd: #3A4A24;
        --kn-orange  : #C4722A;
        --kn-cream   : #F5EDD6;
        --kn-dark    : #3D4A1E;
        --kn-muted   : rgba(90,112,56,.55);
        --sidebar-w  : 240px;
        --topbar-h   : 56px;
    }
    * { box-sizing: border-box; }
    body { 
        background: linear-gradient(135deg, #fde8d8 0%, #fef5e7 100%);
        font-family:'Inter','Segoe UI',system-ui,-apple-system,sans-serif; 
        color:var(--kn-dark); 
        margin:0; 
        line-height:1.6;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Smooth scrollbar */
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: rgba(90,112,56,.05); }
    ::-webkit-scrollbar-thumb { 
        background: rgba(90,112,56,.3); 
        border-radius: 5px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
    ::-webkit-scrollbar-thumb:hover { background: rgba(90,112,56,.5); background-clip: padding-box; }

    /* Topbar */
    .kn-topbar { 
        position:fixed; 
        top:0; 
        left:0; 
        right:0; 
        height:var(--topbar-h); 
        background: linear-gradient(135deg, #5A7038 0%, #4A5D2E 100%);
        display:flex; 
        align-items:center; 
        padding:0 1.5rem 0 calc(var(--sidebar-w) + 1.5rem); 
        z-index:200; 
        box-shadow: 0 4px 24px rgba(0,0,0,.2), 0 2px 8px rgba(0,0,0,.15);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255,255,255,.1);
    }
    .kn-topbar .page-title { 
        font-size:1.05rem; 
        font-weight:700; 
        color:var(--kn-cream); 
        flex:1; 
        letter-spacing:-0.02em;
        text-shadow: 0 2px 4px rgba(0,0,0,.2);
    }
    .kn-topbar .user-chip { 
        display:flex; 
        align-items:center; 
        gap:.6rem; 
        background: rgba(255,255,255,.12);
        backdrop-filter: blur(10px);
        border:1px solid rgba(255,255,255,.15); 
        border-radius:50px; 
        padding:.4rem 1rem; 
        color:var(--kn-cream); 
        font-size:.85rem; 
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
        transition:all .2s ease;
    }
    .kn-topbar .user-chip:hover { 
        background: rgba(255,255,255,.18);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
    }
    .kn-topbar .user-chip i { 
        color:#f0c080; 
        font-size:1rem;
    }
    .btn-signout { 
        background: rgba(255,255,255,.1);
        backdrop-filter: blur(10px);
        border:1px solid rgba(255,255,255,.15); 
        color:var(--kn-cream); 
        border-radius:10px; 
        padding:.4rem 1rem; 
        font-size:.85rem; 
        text-decoration:none; 
        margin-left:.65rem; 
        transition:all .2s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,.1);
        font-weight:500;
    }
    .btn-signout:hover { 
        background: rgba(248,113,113,.15);
        color:#fff; 
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(248,113,113,.2);
        border-color: rgba(248,113,113,.25);
    }

    /* Sidebar */
    .kn-sidebar { 
        position:fixed; 
        top:0; 
        left:0; 
        bottom:0; 
        width:var(--sidebar-w); 
        background: linear-gradient(180deg, #5A7038 0%, #4A5D2E 50%, #3A4A24 100%);
        display:flex; 
        flex-direction:column; 
        z-index:300; 
        box-shadow: 4px 0 32px rgba(0,0,0,.25), 8px 0 16px rgba(0,0,0,.15);
    }
    .sidebar-brand { 
        height:var(--topbar-h); 
        display:flex; 
        align-items:center; 
        padding:0 1.25rem; 
        border-bottom:1px solid rgba(255,255,255,.12); 
        flex-shrink:0;
        position: relative;
    }
    .sidebar-brand::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 1.25rem;
        right: 1.25rem;
        height: 2px;
        background: linear-gradient(90deg, transparent 0%, rgba(240,192,128,.4) 50%, transparent 100%);
    }
    .sidebar-brand .brand-text { 
        font-size:1.3rem; 
        font-weight:800; 
        color:var(--kn-cream); 
        text-decoration:none; 
        letter-spacing:-0.02em;
        text-shadow: 0 2px 8px rgba(0,0,0,.3);
    }
    .sidebar-brand .brand-text em { 
        color:#f0c080; 
        font-style:normal;
        background: linear-gradient(135deg, #f0c080 0%, #d4a574 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .sidebar-brand .brand-badge { 
        font-size:.62rem; 
        font-weight:700; 
        background: linear-gradient(135deg, #C4722A 0%, #a85e22 100%);
        color:#fff; 
        border-radius:5px; 
        padding:.22rem .45rem; 
        margin-left:.5rem; 
        text-transform:uppercase; 
        letter-spacing:.05em; 
        box-shadow: 0 2px 6px rgba(196,114,42,.3);
    }
    .sidebar-nav { flex:1; padding:1.25rem 0; overflow-y:auto; }
    .sidebar-nav::-webkit-scrollbar { width:6px; }
    .sidebar-nav::-webkit-scrollbar-track { background:rgba(255,255,255,.05); }
    .sidebar-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,.2); border-radius:3px; }
    .sidebar-nav::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,.3); }
    .nav-section-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.12em; color:rgba(245,237,214,.4); padding:.75rem 1.5rem .4rem; margin-top:.5rem; }
    .nav-item { 
        display:flex; 
        align-items:center; 
        gap:.85rem; 
        padding:.8rem 1.5rem; 
        margin:0 .75rem .35rem; 
        color:rgba(245,237,214,.7); 
        text-decoration:none; 
        font-size:.92rem; 
        font-weight:500; 
        border-radius:12px; 
        transition:all .2s ease;
        position:relative;
        overflow: hidden;
    }
    .nav-item::before { 
        content:''; 
        position:absolute; 
        left:0; 
        top:50%; 
        transform:translateY(-50%); 
        width:4px; 
        height:0; 
        background: linear-gradient(180deg, #f0c080 0%, #d4a574 100%);
        border-radius:0 3px 3px 0; 
        transition:height .2s ease;
        box-shadow: 0 0 8px rgba(240,192,128,.4);
    }
    .nav-item::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(240,192,128,.12) 0%, rgba(240,192,128,.04) 100%);
        opacity: 0;
        transition: opacity .2s;
    }
    .nav-item:hover { 
        background: rgba(255,255,255,.1);
        color:var(--kn-cream); 
        transform:translateX(2px);
    }
    .nav-item:hover::before { height:50%; }
    .nav-item:hover::after { opacity: 1; }
    .nav-item.active { 
        background: linear-gradient(135deg, rgba(240,192,128,.18) 0%, rgba(240,192,128,.1) 100%);
        color:#fff; 
        font-weight:600; 
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
        border: 1px solid rgba(240,192,128,.15);
    }
    .nav-item.active::before { 
        height:60%;
        box-shadow: 0 0 12px rgba(240,192,128,.6);
    }
    .nav-item.disabled { opacity:.4; pointer-events:none; }
    .nav-item i { 
        font-size:1.1rem; 
        width:22px; 
        text-align:center;
        position: relative;
        z-index: 1;
    }
    .nav-item .nav-badge { 
        margin-left:auto; 
        background: linear-gradient(135deg, #C4722A 0%, #a85e22 100%);
        color:#fff; 
        font-size:.68rem; 
        font-weight:700; 
        border-radius:20px; 
        padding:.2rem .55rem; 
        box-shadow: 0 2px 6px rgba(196,114,42,.3);
        position: relative;
        z-index: 1;
    }
    .sidebar-footer { 
        padding:1rem 1.25rem; 
        border-top:1px solid rgba(255,255,255,.12); 
        flex-shrink:0; 
        background: linear-gradient(180deg, rgba(0,0,0,.15) 0%, rgba(0,0,0,.1) 100%);
        position: relative;
    }
    .sidebar-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 1.25rem;
        right: 1.25rem;
        height: 1px;
        background: linear-gradient(90deg, transparent 0%, rgba(240,192,128,.3) 50%, transparent 100%);
    }
    .sidebar-user { display:flex; align-items:center; gap:.65rem; }
    .sidebar-user .avatar { 
        width:38px; 
        height:38px; 
        border-radius:50%; 
        background: linear-gradient(135deg, rgba(240,192,128,.25) 0%, rgba(240,192,128,.15) 100%);
        border:2px solid rgba(240,192,128,.4); 
        display:flex; 
        align-items:center; 
        justify-content:center; 
        font-size:1.05rem; 
        color:#f0c080; 
        flex-shrink:0; 
        box-shadow: 0 4px 12px rgba(0,0,0,.2), inset 0 1px 0 rgba(255,255,255,.1);
        position: relative;
    }
    .sidebar-user .avatar::after {
        content: '';
        position: absolute;
        top: -2px;
        right: -2px;
        width: 9px;
        height: 9px;
        background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
        border: 2px solid var(--kn-green-xd);
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(74,222,128,.6);
    }
    .sidebar-user .info { overflow:hidden; flex:1; }
    .sidebar-user .name { 
        font-size:.88rem; 
        font-weight:600; 
        color:var(--kn-cream); 
        white-space:nowrap; 
        overflow:hidden; 
        text-overflow:ellipsis; 
        letter-spacing:-0.01em;
        text-shadow: 0 1px 2px rgba(0,0,0,.2);
    }
    .sidebar-user .role { 
        font-size:.74rem; 
        color:rgba(245,237,214,.6); 
        margin-top:.05rem;
    }
    .sidebar-user .signout-icon { 
        color:rgba(245,237,214,.5); 
        font-size:1.1rem; 
        text-decoration:none; 
        transition:all .2s ease;
        flex-shrink:0; 
        padding:.3rem; 
        border-radius:8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sidebar-user .signout-icon:hover { 
        color:#f87171; 
        background: rgba(248,113,113,.15);
        transform: scale(1.05);
    }

    /* Main */
    .kn-main { margin-left:var(--sidebar-w); padding-top:var(--topbar-h); min-height:100vh; }
    .kn-content { 
        padding:1.75rem 2rem; 
        max-width:1600px;
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Card hover glow effect */
    .card, .modern-stat-card, .modern-quick-card, .settings-card {
        position: relative;
    }
    .card::before, .modern-stat-card::before, .modern-quick-card::before {
        transition: opacity .4s;
    }
    
    /* Smooth focus states for all interactive elements */
    button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible {
        outline: 3px solid rgba(90,112,56,.4);
        outline-offset: 2px;
    }

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
