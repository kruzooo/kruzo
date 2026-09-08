<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Client Portfolio Creation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="register-page">
    <header class="login-header">
        <a href="{{ route('home') }}" class="login-brand">KRUZO <span>|</span> MNL</a>
        <nav class="login-nav"><a href="{{ route('shop') }}">SHOP ALL</a><a href="{{ route('login') }}">SIGN IN</a></nav>
    </header>

    <main class="register-main">
        <aside class="register-aside">
            <div class="register-aside-image" style="background-image: url('{{ asset('images/screen.png') }}')"></div>
            <div class="register-aside-content">
                <p>KRUZO MNL // CLIENT ARCHIVE</p>
                <div>
                    <span>VIP ARCHIVE PASS</span>
                    <h1>FORM<br>REMAINS.</h1>
                    <p>Establish a client portfolio for certified garment provenance, bespoke sizing records, and priority seasonal-drop allocation.</p>
                </div>
                <small><span class="material-symbols-outlined">verified</span> MAINTAINED AT THE MAKATI ATELIER</small>
            </div>
        </aside>

        <section class="register-panel">
            <div class="register-form-wrap">
                <div class="register-heading">
                    <p>ATELIER ONBOARDING <span>/</span> <b>VIP ARCHIVE PASS</b></p>
                    <h2>CLIENT PORTFOLIO CREATION</h2>
                    <span>Establish your verified profile to unlock NFC garment provenance, bespoke sizing archives, and priority allocations.</span>
                </div>

                <div class="register-fast-pass"><strong>1-TAP PHILIPPINE INSTANT AUTHENTICATION</strong><button type="button"><span class="material-symbols-outlined">account_circle</span> CONTINUE WITH GOOGLE FAST PASS</button></div>
                <div class="register-divider"><span>OR REGISTER WITH PHILIPPINE MOBILE / EMAIL</span></div>

                @if ($errors->any())<div class="login-errors" role="alert">{{ $errors->first() }}</div>@endif

                <form method="POST" action="{{ route('register.store') }}" class="register-form">
                    @csrf
                    <div class="register-grid">
                        <label>FIRST &amp; MIDDLE NAME <input name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Alyssa Marie" required></label>
                        <label>SURNAME / FAMILY NAME <input name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Reyes" required></label>
                    </div>
                    <label>PHILIPPINE MOBILE CONTACT <div class="register-phone"><b>+63</b><input name="phone" value="{{ old('phone') }}" inputmode="numeric" maxlength="10" placeholder="917 888 1920" required></div><small>Used for courier coordination, VIP drops, and Viber dispatch notifications.</small></label>
                    <label>EMAIL ADDRESS // ARCHIVAL LEDGER ID <div class="login-input-wrap"><span class="material-symbols-outlined">alternate_email</span><input name="email" type="email" value="{{ old('email') }}" placeholder="alyssa.reyes@studio-mnl.ph" required></div></label>
                    <label>CRYPTOGRAPHIC ACCESS KEY / PASSWORD <small>MIN. 8 CHARACTERS</small><div class="login-input-wrap"><span class="material-symbols-outlined">lock</span><input name="password" type="password" minlength="8" autocomplete="new-password" placeholder="Enter a secure password" required></div></label>

                    <label class="register-choice"><input name="welcome_credit" value="1" type="checkbox" checked><span><strong>CLAIM COMPLIMENTARY ₱250 STUDIO WELCOME CREDIT</strong>Automatically reserved for 90 days toward any KRUZO atelier staple or capsule release.</span></label>
                    <label class="register-choice"><input name="drop_alerts" value="1" type="checkbox" checked><span><strong>REAL-TIME RIDER TELEMETRY &amp; VIP DROP ALERTS</strong>Receive courier updates, private lookbook codes, and pre-order slots by Viber and SMS.</span></label>
                    <label class="register-terms"><input name="terms" value="1" type="checkbox" required><span>I accept the KRUZO Terms of Service and consent to secure data processing under the Philippine Data Privacy Act of 2012.</span></label>
                    <button type="submit">CREATE ARCHIVE ACCOUNT <span class="material-symbols-outlined">arrow_forward</span></button>
                </form>

                <p class="register-signin">Already possess a registered atelier portfolio? <a href="{{ route('login') }}">SIGN IN TO ARCHIVE</a></p>
                <div class="register-support"><span class="material-symbols-outlined">support_agent</span><p><strong>NEED SIZING CONSULTATION OR SHOWROOM ACCESS?</strong>Direct Viber Atelier Concierge: +63 917 888 KRUZ (5789)</p></div>
            </div>
        </section>
    </main>
</body>
</html>
