<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Client Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page bg-surface text-on-surface antialiased">
    <header class="login-header">
        <a href="{{ route('home') }}" class="login-brand">KRUZO <span>|</span> MNL</a>
        <nav class="login-nav">
            <a href="{{ route('shop') }}">SHOP ALL</a>
            <a href="{{ route('cart') }}">BAG</a>
        </nav>
    </header>

    <main class="login-main">
        <section class="login-editorial">
            <div class="login-editorial-image" style="background-image: url('{{ asset('images/screen.png') }}')"></div>
            <div class="login-editorial-overlay"></div>
            <div class="login-editorial-content">
                <div class="login-telemetry">
                    <div>
                        <span>KRUZO CLIENT ARCHIVE // ACCESS PORTAL</span>
                        <small>MAKATI ATELIER // ENCRYPTED GATEWAY</small>
                    </div>
                    <b>SECURE // 256-BIT</b>
                </div>

                <div class="login-statement">
                    <p>ARCHIVE MEMBER PRIVILEGES</p>
                    <h1>PRECISION FORM.<br>UNCOMPROMISING SILHOUETTES.</h1>
                    <span>Access your orders, delivery details, saved client profile, and priority seasonal-drop notifications.</span>
                    <div class="login-features">
                        <div><span class="material-symbols-outlined">nfc</span>NFC AUTHENTICITY</div>
                        <div><span class="material-symbols-outlined">local_shipping</span>EXPRESS DISPATCH</div>
                        <div><span class="material-symbols-outlined">straighten</span>VAULT SIZING</div>
                        <div><span class="material-symbols-outlined">lock</span>PRIVATE ACCESS</div>
                    </div>
                </div>

                <div class="login-provenance">
                    <span class="material-symbols-outlined">verified</span>
                    <span>Handcrafted in Metro Manila. Certified provenance on every archival garment.</span>
                </div>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-form-wrap">
                <div class="login-form-heading">
                    <p><i></i> AUTHENTICATION GATEWAY</p>
                    <h2>CLIENT ACCESS</h2>
                    <span>Enter your account credentials to reach your KRUZO customer portal.</span>
                </div>

                @if ($errors->any())
                    <div class="login-errors" role="alert">{{ $errors->first() }}</div>
                @endif
                @if (session('logout_success'))
                    <div class="login-success" role="status">{{ session('logout_success') }}</div>
                @endif
                @if (session('registration_success'))
                    <div class="login-success" role="status">{{ session('registration_success') }}</div>
                @endif

                <form method="POST" action="{{ route('login.authenticate') }}" class="login-form">
                    @csrf
                    <label for="email">CLIENT EMAIL</label>
                    <div class="login-input-wrap">
                        <span class="material-symbols-outlined">alternate_email</span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="client@domain.com" required autofocus>
                    </div>

                    <div class="login-password-row">
                        <label for="password">PASSWORD</label>
                        <a href="{{ route('login') }}#support">FORGOT PASSWORD?</a>
                    </div>
                    <div class="login-input-wrap">
                        <span class="material-symbols-outlined">lock</span>
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter password" required>
                    </div>

                    <label class="login-remember"><input type="checkbox" name="remember" value="1" checked> <span>Keep this session on this trusted device</span></label>
                    <button type="submit">AUTHENTICATE &amp; ENTER <span class="material-symbols-outlined">arrow_forward</span></button>
                </form>

                <section class="login-register">
                    <div><strong>NEW TO KRUZO ATELIER?</strong><b>₱250 CREDIT</b></div>
                    <p>Create a client portfolio to receive studio credit toward your inaugural architectural garment drop.</p>
                    <a href="{{ route('register') }}">CREATE ACCOUNT <span class="material-symbols-outlined">chevron_right</span></a>
                </section>

                <p class="login-support" id="support"><span class="material-symbols-outlined">contact_support</span> Need access help? Contact the Makati Studio Concierge on Viber: +63 917 888 KRUZ.</p>
            </div>
        </section>
    </main>

    <footer class="login-footer">
        <span>PHILIPPINE BIR REGISTERED ENTITY</span>
        <span>METRO MANILA SAME-DAY HUB: MAKATI &amp; BGC</span>
        <span>SECURE SETTLEMENT: GCASH, MAYA, COD</span>
    </footer>
</body>
</html>
