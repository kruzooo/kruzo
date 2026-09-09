<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Reset Access</title>
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
            <a href="{{ route('login') }}">CLIENT LOGIN</a>
        </nav>
    </header>

    <main class="login-main">
        <section class="login-editorial">
            <div class="login-editorial-image" style="background-image: url('{{ asset('images/screen.png') }}')"></div>
            <div class="login-editorial-overlay"></div>
            <div class="login-editorial-content">
                <div class="login-telemetry"><span>KRUZO CLIENT ARCHIVE // ACCESS RECOVERY</span><b>SECURE // 256-BIT</b></div>
                <div class="login-statement">
                    <p>RESTORE YOUR ACCESS</p>
                    <h1>RETURN TO<br>THE ARCHIVE.</h1>
                    <span>Enter your registered customer email and we will send instructions to recover your account access.</span>
                </div>
                <div class="login-provenance"><span class="material-symbols-outlined">verified</span><span>Your account information remains protected by the KRUZO client gateway.</span></div>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-form-wrap">
                <div class="login-form-heading">
                    <p><i></i> AUTHENTICATION GATEWAY</p>
                    <h2>FORGOT PASSWORD?</h2>
                    <span>Use your customer email to request password reset instructions.</span>
                </div>

                @if ($errors->any())
                    <div class="login-errors" role="alert">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('password.email') }}" class="login-form">
                    @csrf
                    <label for="email">CLIENT EMAIL</label>
                    <div class="login-input-wrap">
                        <span class="material-symbols-outlined">alternate_email</span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="client@domain.com" required autofocus>
                    </div>
                    <label>DELIVERY CHANNEL</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 border border-surface-container-highest px-3 py-3 cursor-pointer hover:border-primary">
                            <input type="radio" name="channel" value="email" checked data-reset-channel>
                            <span class="font-label-caps text-label-caps uppercase">EMAIL</span>
                        </label>
                        <label class="flex items-center gap-2 border border-surface-container-highest px-3 py-3 cursor-pointer hover:border-primary">
                            <input type="radio" name="channel" value="sms" data-reset-channel>
                            <span class="font-label-caps text-label-caps uppercase">SMS</span>
                        </label>
                    </div>
                    <div id="reset-phone-field" class="hidden">
                        <label for="phone">MOBILE NUMBER</label>
                        <div class="login-input-wrap">
                            <span class="material-symbols-outlined">phone</span>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="+63 917 000 0000">
                        </div>
                    </div>
                    <button type="submit">REQUEST RESET LINK <span class="material-symbols-outlined">arrow_forward</span></button>
                </form>

                @if (session('password_reset_success'))
                    <div class="login-success mt-4" role="status">
                        {{ session('password_reset_success') }}
                        <br><small>Local test code: <strong>{{ session('password_reset_demo_code') }}</strong></small>
                    </div>
                @endif

                @if (session('password_reset'))
                    <div class="mt-6 border-t border-surface-container-highest pt-6">
                        <div class="login-form-heading">
                            <p><i></i> CODE VERIFICATION</p>
                            <h2>SET NEW PASSWORD</h2>
                            <span>Enter the six-digit code, then choose a new password for this account.</span>
                        </div>
                        <form method="POST" action="{{ route('password.update') }}" class="login-form mt-4">
                            @csrf
                            <label for="code">VERIFICATION CODE</label>
                            <div class="login-input-wrap">
                                <span class="material-symbols-outlined">pin</span>
                                <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="000000" required>
                            </div>
                            <label for="new-password">NEW PASSWORD</label>
                            <div class="login-input-wrap">
                                <span class="material-symbols-outlined">lock</span>
                                <input id="new-password" name="password" type="password" minlength="8" autocomplete="new-password" placeholder="At least 8 characters" required>
                            </div>
                            <label for="new-password-confirmation">CONFIRM NEW PASSWORD</label>
                            <div class="login-input-wrap">
                                <span class="material-symbols-outlined">lock_reset</span>
                                <input id="new-password-confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" placeholder="Repeat your new password" required>
                            </div>
                            <button type="submit">CHANGE PASSWORD <span class="material-symbols-outlined">check</span></button>
                        </form>
                    </div>
                @endif

                <section class="login-register">
                    <div><strong>REMEMBERED YOUR PASSWORD?</strong></div>
                    <p>Return to the customer login page to access your dashboard.</p>
                    <a href="{{ route('login') }}">BACK TO CLIENT LOGIN <span class="material-symbols-outlined">chevron_right</span></a>
                </section>
            </div>
        </section>
    </main>

    <footer class="login-footer">
        <span>PHILIPPINE BIR REGISTERED ENTITY</span>
        <span>METRO MANILA SAME-DAY HUB: MAKATI &amp; BGC</span>
        <span>SECURE SETTLEMENT: GCASH, MAYA, COD</span>
    </footer>
    <script>
        document.querySelectorAll('[data-reset-channel]').forEach((choice) => {
            choice.addEventListener('change', () => {
                const phoneField = document.getElementById('reset-phone-field');
                const phone = document.getElementById('phone');
                const smsSelected = document.querySelector('[data-reset-channel][value="sms"]')?.checked;
                phoneField?.classList.toggle('hidden', !smsSelected);
                if (phone) phone.required = smsSelected;
            });
        });
    </script>
</body>
</html>
