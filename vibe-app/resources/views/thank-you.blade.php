<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Order Confirmed</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="thank-you-page bg-surface text-on-surface antialiased">
    <header class="thank-you-header">
        <div class="thank-you-banner">FREE SHIPPING ACROSS THE PHILIPPINES ON ORDERS OVER ₱3,500</div>
        <div class="thank-you-header-inner">
            <a href="{{ route('home') }}">KRUZO <span>|</span> MNL</a>
            <nav class="thank-you-nav">
                <a href="{{ route('shop') }}">SHOP ALL</a>
                <a href="{{ route('cart') }}">BAG</a>
            </nav>
        </div>
    </header>

    <main class="thank-you-main">
        @if ($order)
            <section class="thank-you-hero">
                <div>
                    <p class="thank-you-kicker"><span aria-label="Atelier color spectrum" class="project-status-dot inline-block w-2 h-2 rounded-full" role="img"></span>04 CONFIRMATION</p>
                    <span>ORDER CONFIRMED</span>
                    <h1>{{ $order['number'] }}</h1>
                    <strong>Thank you, {{ $order['customer']['first_name'] }}.</strong>
                    <p>Your KRUZO order has been received. We will send dispatch updates to {{ $order['customer']['email'] }}.</p>
                </div>
                <aside>
                    <span class="material-symbols-outlined">verified</span>
                    <p>Placed {{ $order['placed_at'] }}</p>
                    <strong>{{ strtoupper($order['customer']['payment_method'] === 'cod' ? 'Cash on Delivery' : 'GCash / Maya') }}</strong>
                </aside>
            </section>

            <section class="thank-you-grid">
                <div class="thank-you-panel">
                    <div class="thank-you-panel-heading">
                        <p>GARMENT MANIFEST</p>
                        <h2>{{ count($order['cart']) }} {{ count($order['cart']) === 1 ? 'Item' : 'Items' }}</h2>
                    </div>
                    <div class="thank-you-items">
                        @foreach ($order['cart'] as $item)
                            <article class="thank-you-item">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                <div>
                                    <span>SS25 ARCHIVE</span>
                                    <h3>{{ $item['name'] }}</h3>
                                    <p>QTY {{ $item['quantity'] }}</p>
                                </div>
                                <strong>{{ $item['price'] }}</strong>
                            </article>
                        @endforeach
                    </div>
                    <div class="thank-you-total">
                        <span>Total Paid</span>
                        <strong>₱{{ number_format($order['subtotal'], 2) }}</strong>
                    </div>
                </div>

                <aside class="thank-you-panel">
                    <div class="thank-you-panel-heading">
                        <p>DISPATCH DESTINATION</p>
                        <h2>Delivery Details</h2>
                    </div>
                    <address>
                        <strong>{{ $order['customer']['first_name'] }} {{ $order['customer']['last_name'] }}</strong>
                        {{ $order['customer']['address'] }}<br>
                        {{ $order['customer']['city'] }}, {{ $order['customer']['province'] }} {{ $order['customer']['postal_code'] }}<br>
                        Philippines<br>
                        {{ $order['customer']['phone'] }}
                    </address>
                    <div class="thank-you-dispatch">
                        <span class="material-symbols-outlined">local_shipping</span>
                        <div>
                            <strong>Dispatch queued</strong>
                            <p>Metro Manila orders are prepared for GrabExpress or Lalamove when eligible.</p>
                        </div>
                    </div>
                </aside>
            </section>

            <section class="thank-you-actions">
                <div>
                    <p>POST-CHECKOUT</p>
                    <h2>Keep Exploring The Drop</h2>
                    <p class="thank-you-redirect" id="dashboard-redirect-message">REDIRECTING TO YOUR CUSTOMER DASHBOARD IN <strong id="dashboard-countdown">5</strong> SECONDS</p>
                </div>
                <div>
                    <a href="{{ route('dashboard') }}">GO TO DASHBOARD NOW</a>
                    <a href="{{ route('shop') }}">CONTINUE SHOPPING</a>
                    <a href="{{ route('home') }}">BACK HOME</a>
                </div>
            </section>
        @else
            <section class="thank-you-empty">
                <span class="material-symbols-outlined">receipt_long</span>
                <h1>No confirmed order yet</h1>
                <p>Complete checkout first and your confirmation details will appear here.</p>
                <a href="{{ route('shop') }}">RETURN TO SHOP</a>
            </section>
        @endif
    </main>
    @if ($order)
        <script>
            (() => {
                const countdown = document.getElementById('dashboard-countdown');
                const dashboardUrl = @json(route('dashboard'));
                let seconds = 5;
                const timer = window.setInterval(() => {
                    seconds -= 1;
                    if (countdown) countdown.textContent = String(seconds);
                    if (seconds <= 0) {
                        window.clearInterval(timer);
                        window.location.assign(dashboardUrl);
                    }
                }, 1000);
            })();
        </script>
    @endif
</body>
</html>
