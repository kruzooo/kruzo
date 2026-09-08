<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Client Care &amp; Vault Concierge</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="contact-page">
    <div class="contact-banner">CONCIERGE &amp; ATELIER DESK AVAILABLE 10:00 - 20:00 PHT</div>
    <header class="contact-header">
        <a href="{{ route('home') }}">KRUZO <span>|</span> MNL</a>
        <nav><a href="{{ route('shop') }}">SHOP ALL</a><a href="{{ route('cart') }}">BAG</a><a href="{{ route('dashboard') }}">ACCOUNT</a></nav>
    </header>

    <main class="contact-main">
        <section class="contact-hero">
            <div><p>CONCIERGE / SUPPORT</p><h1>CLIENT CARE &amp;<br>VAULT CONCIERGE</h1><span>Direct communication for ordering, dispatch, garment recalibration, authentication, and studio feedback.</span></div>
            <aside><span class="material-symbols-outlined">support_agent</span><p>MAKATI ATELIER CONCIERGE</p><strong>AVAILABLE NOW</strong></aside>
        </section>

        <section class="contact-grid">
            <section class="contact-form-panel">
                <div class="contact-panel-heading"><div><p>TRANSMIT ATELIER INQUIRY</p><h2>How Can We Help?</h2></div><span>FORM REF // MNL-SR-2024</span></div>
                @if (session('contact_success'))
                    <div class="contact-success" role="status"><span class="material-symbols-outlined">mark_email_read</span><div><strong>Ticket reference logged: {{ session('contact_success') }}</strong><p>The on-duty concierge will contact you through your selected channel within 45 minutes.</p></div></div>
                @endif
                @if ($errors->any())<div class="login-errors" role="alert">{{ $errors->first() }}</div>@endif
                <form method="POST" action="{{ route('contact.send') }}" class="contact-form" id="contact-form">
                    @csrf
                    <label>SELECT INQUIRY SPECIALIZATION
                        <select name="specialization" required><option value="retail">Curated Retail Inquiry</option><option value="dispatch">Dispatch &amp; Courier Support</option><option value="recalibration">Recalibration &amp; Exchange</option><option value="provenance">NFC Provenance Verification</option><option value="feedback">Client Feedback</option></select>
                    </label>
                    <div class="contact-fields"><label>CLIENT NAME *<input name="name" value="{{ old('name') }}" placeholder="Your name" required></label><label>REGISTERED EMAIL / TELEPHONE *<input name="contact" value="{{ old('contact') }}" placeholder="+63 9XX XXX XXXX or client@domain.ph" required></label></div>
                    <fieldset><legend>PREFERRED CONTACT CHANNEL</legend><label><input name="channel" type="radio" value="viber" checked> VIBER</label><label><input name="channel" type="radio" value="sms"> SMS</label><label><input name="channel" type="radio" value="email"> EMAIL</label><label><input name="channel" type="radio" value="phone"> CALL</label></fieldset>
                    <label>MESSAGE / GARMENT SPECIFICATION *<textarea name="message" rows="5" placeholder="Detail your exact query, feedback, sizing request, or delivery concern..." required>{{ old('message') }}</textarea></label>
                    <button type="submit">TRANSMIT INQUIRY TO ATELIER <span class="material-symbols-outlined">send</span></button>
                </form>
            </section>

            <aside class="contact-side">
                <section class="contact-channel dark"><span class="material-symbols-outlined">chat</span><p>FAST-TRACK</p><h2>MAKATI VIBER CONCIERGE</h2><a href="{{ route('contact') }}#contact-form">+63 917 888 KRUZ</a><small>For active deliveries, same-day dispatch, and appointment requests.</small></section>
                <section class="contact-channel"><span class="material-symbols-outlined">location_on</span><p>PHYSICAL HUB</p><h2>THE MAKATI ATELIER</h2><address>Warehouse 8, The Alley at Karrivin<br>Chino Roces Ave Ext, Makati City<br>Tue-Sun, by appointment</address></section>
                <section class="contact-channel"><span class="material-symbols-outlined">schedule</span><p>RESPONSE WINDOW</p><h2>CLIENT DESK HOURS</h2><strong>10:00 - 20:00 PHT</strong><small>Messages after hours are queued for the next atelier shift.</small></section>
            </aside>
        </section>

        <section class="contact-faq"><div><p>CONCIERGE PROTOCOL</p><h2>Common Support Requests</h2></div><details><summary>How does same-day Metro Manila dispatch work?</summary><p>Orders finalized before 16:00 PHT may be eligible for direct dispatch from the Makati vault. The concierge sends the live tracking link by your selected channel.</p></details><details><summary>Can I request a size recalibration or exchange?</summary><p>Eligible pieces may receive one in-studio recalibration session within seven calendar days of delivery, subject to garment condition.</p></details><details><summary>Can I leave product or service feedback?</summary><p>Yes. Choose Client Feedback above and share your experience, garment notes, or suggestions for future drops.</p></details></section>
    </main>
    <footer class="contact-footer">KRUZO MNL // ARCHITECTURAL LUXURY STREETWEAR <span>MAKATI, PHILIPPINES</span></footer>
</body>
</html>
