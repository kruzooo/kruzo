<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Atelier Operations Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-login-page">
    <header class="admin-login-header">
        <a href="{{ route('home') }}" class="admin-login-brand">KRUZO <span>MNL</span></a>
        <strong>ATELIER OPERATIONS</strong>
        <div><i></i> SECURITY NODE // ENCRYPTED</div>
    </header>

    <main class="admin-login-main">
        <section class="admin-login-wrap">
            <div class="admin-login-node"><span><i></i> MAKATI NODE 01 // ENCRYPTED GATEWAY</span><b>SYS REV 4.8.2</b></div>
            <section class="admin-login-card">
                <div class="admin-login-card-heading">
                    <div><strong>KRUZO</strong><span>/</span><b>MNL</b><em>ATELIER SEC_OPS</em></div>
                    <h1>CONTROL ROOM AUTHENTICATION</h1>
                    <p>Restricted access portal for Atelier Directors, Dispatch Coordinators, and Inventory Vault Managers.</p>
                </div>

                <div class="admin-auth-tabs"><button type="button" class="active"><span class="material-symbols-outlined">key</span> CREDENTIALS &amp; PASSKEY</button><button type="button"><span class="material-symbols-outlined">fingerprint</span> FIDO2 / HARDWARE KEY</button></div>

                @if ($errors->any())<div class="login-errors" role="alert">{{ $errors->first() }}</div>@endif
                @if (session('admin_logout_success'))<div class="login-success" role="status">{{ session('admin_logout_success') }}</div>@endif

                <form method="POST" action="{{ route('admin.login.authenticate') }}" class="admin-login-form">
                    @csrf
                    <label>OPERATOR IDENTITY <small>ID OR REGISTERED EMAIL</small><div><span class="material-symbols-outlined">badge</span><input name="operator_id" value="{{ old('operator_id') }}" placeholder="pryvstpedrera@gmail.com" required autofocus></div></label>
                    <label>SECURITY PASSKEY <a href="{{ route('admin.login') }}#support">RESET PASSCODE</a><div><span class="material-symbols-outlined">lock</span><input name="passkey" type="password" minlength="8" autocomplete="current-password" placeholder="Enter secure passkey" required></div></label>
                    <label>ATELIER DIVISION &amp; CLEARANCE LEVEL <div><span class="material-symbols-outlined">domain</span><select name="division" required><option value="dispatch">Operations &amp; Dispatch Logistics</option><option value="vault">Vault Inventory, QC &amp; Archival</option><option value="concierge">VIP Concierge &amp; Bespoke Fittings</option><option value="finance">Finance, Compliance &amp; BIR Telemetry</option><option value="executive">Atelier Board &amp; Executive Governance</option></select></div></label>
                    <div class="admin-trusted"><label><input type="checkbox" name="trusted_session" value="1" checked> Enforce Trusted Hardware Session (12 Hours)</label><span><span class="material-symbols-outlined">verified_user</span> EAL6+</span></div>
                    <button type="submit">AUTHENTICATE &amp; ACCESS ATELIER OS <span class="material-symbols-outlined">arrow_forward</span></button>
                </form>

                <div class="admin-login-divider"><span>OR CONNECT SECURELY VIA</span></div>
                <button type="button" class="admin-sso"><span class="material-symbols-outlined">account_circle</span> ATELIER SSO / GOOGLE WORKSPACE</button>
            </section>
            <section class="admin-login-status" id="support"><p><span class="material-symbols-outlined">terminal</span> IP LOGGED • LAT 14°33'N LONG 121°01'E • MAKATI ATELIER HQ</p><p>GATEWAY ACTIVE</p><hr><span><span class="material-symbols-outlined">support_agent</span> Support: Makati IT Ops via Viber hotline <b>+63 917 888 0001</b></span></section>
        </section>
    </main>
    <footer class="admin-login-footer">© 2025 KRUZO APPAREL STUDIO PHILIPPINES INC. <span>RESTRICTED OPERATIONS INTERFACE // AUTHORIZED PERSONNEL ONLY</span></footer>
</body>
</html>
