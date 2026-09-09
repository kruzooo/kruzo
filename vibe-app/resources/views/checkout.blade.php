<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Checkout</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="checkout-page bg-surface text-on-surface antialiased">
    <header class="checkout-header">
        <div class="checkout-banner">FREE SHIPPING ACROSS THE PHILIPPINES ON ORDERS OVER ₱3,500</div>
        <div class="checkout-header-inner">
            <a href="{{ route('home') }}">KRUZO <span>|</span> MNL</a>
            <div class="checkout-nav"><a href="{{ route('cart') }}">01 BAG</a><span>/</span><strong>02 CHECKOUT</strong></div>
        </div>
    </header>

    <main class="checkout-main">
        <div class="checkout-titlebar">
            <div><p>02 SHIPPING &amp; FULFILLMENT</p><h1>Checkout</h1></div>
            <a href="{{ route('cart') }}">BACK TO BAG</a>
        </div>

        @if (count($cart) === 0)
            <section class="checkout-empty">
                <span class="material-symbols-outlined">shopping_bag</span>
                <h2>Your bag is empty</h2>
                <a href="{{ route('shop') }}">RETURN TO SHOP</a>
            </section>
        @else
            @php
                $subtotal = collect($cart)->sum(fn ($item) => (float) str_replace([',', '₱'], '', $item['price']) * $item['quantity']);
            @endphp
            <div class="checkout-layout">
                <form class="checkout-form" method="POST" action="{{ route('checkout.place') }}">
                    @csrf
                    @if ($errors->has('checkout'))
                        <p class="checkout-coupon-error">{{ $errors->first('checkout') }}</p>
                    @endif
                    <section class="checkout-section">
                        <div class="checkout-section-heading"><span>01</span><h2>Contact information</h2></div>
                        <div class="checkout-fields"><label>First name<input type="text" name="first_name" autocomplete="given-name" required></label><label>Last name<input type="text" name="last_name" autocomplete="family-name" required></label></div>
                        <label>Email address<input type="email" name="email" autocomplete="email" inputmode="email" placeholder="you@example.com" title="Enter a valid email address" required></label>
                        <label>Phone number<input type="tel" name="phone" autocomplete="tel" required></label>
                        <div class="checkout-password-field"><label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label><button aria-label="Show password" class="checkout-password-toggle" data-password-toggle="password" type="button"><span class="material-symbols-outlined">visibility</span></button></div>
                        <div class="checkout-password-field"><label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label><button aria-label="Show password confirmation" class="checkout-password-toggle" data-password-toggle="password_confirmation" type="button"><span class="material-symbols-outlined">visibility</span></button></div>
                        <label class="checkout-checkbox"><input type="checkbox" name="updates"> Email me with news and offers</label>
                    </section>
                    <section class="checkout-section">
                        <div class="checkout-section-heading"><span>02</span><h2>Delivery address</h2></div>
                        <label>Street address<input type="text" name="address" autocomplete="street-address" placeholder="House number and street" required></label>
                        <label>Barangay<input type="text" name="barangay" autocomplete="address-level3" placeholder="Barangay name" required></label>
                        <div class="checkout-fields"><label>City<input type="text" name="city" autocomplete="address-level2" required></label><label>Province<input type="text" name="province" autocomplete="address-level1" required></label></div>
                        <label>Postal code<input type="text" name="postal_code" autocomplete="postal-code" required></label>
                    </section>
                    <section class="checkout-section">
                        <div class="checkout-section-heading"><span>03</span><h2>Payment method</h2></div>
                        <label class="payment-option"><input type="radio" name="payment_method" value="cod" checked><span><strong>Cash on Delivery</strong><small>Available nationwide</small></span></label>
                        <label class="payment-option"><input type="radio" name="payment_method" value="gcash"><span><strong>GCash / Maya</strong><small>Payment instructions appear after checkout</small></span></label>
                        <label class="payment-option"><input type="radio" name="payment_method" value="bdo"><span><strong>BDO Online Banking</strong><small>Transfer instructions appear after checkout</small></span></label>
                        <label class="payment-option"><input type="radio" name="payment_method" value="bpi"><span><strong>BPI Online Banking</strong><small>Transfer instructions appear after checkout</small></span></label>
                        <label class="payment-option"><input type="radio" name="payment_method" value="card"><span><strong>Visa / Mastercard</strong><small>Secure card payment</small></span></label>
                        <button type="submit" class="checkout-submit">PLACE ORDER <span class="material-symbols-outlined">arrow_forward</span></button>
                    </section>
                </form>
                <aside class="checkout-summary">
                    <h2>Order summary</h2>
                    <div class="checkout-summary-items">
                        @foreach ($cart as $item)
                            <div class="checkout-summary-item"><img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"><div><strong>{{ $item['name'] }}</strong><span>QTY {{ $item['quantity'] }}</span></div><b>{{ $item['price'] }}</b></div>
                        @endforeach
                    </div>
                    <form class="checkout-coupon-form" method="POST" action="{{ route('coupon.apply') }}">
                        @csrf
                        <label>Coupon code<input type="text" name="coupon_code" value="{{ session('coupon.code') }}" placeholder="KRUZO250" autocomplete="off"></label>
                        <button type="submit">APPLY</button>
                    </form>
                    @if (session('coupon_success'))<p class="checkout-coupon-success"><strong>{{ session('coupon_success') }}</strong></p>@endif
                    @error('coupon_code')<p class="checkout-coupon-error">{{ $message }}</p>@enderror
                    <div class="checkout-total"><span>Subtotal</span><strong>₱{{ number_format($subtotal, 2) }}</strong></div>
                    @if ($discount > 0)<div class="checkout-total"><span>Coupon discount</span><strong>-₱{{ number_format($discount, 2) }}</strong></div>@endif
                    <div class="checkout-total"><span>Delivery</span><span>Calculated after address</span></div>
                    <div class="checkout-total checkout-grand-total"><span>Total</span><strong>₱{{ number_format(max(0, $subtotal - $discount), 2) }}</strong></div>
                    <p class="checkout-note">Your order is reserved for 12:35. Secure checkout for Philippine delivery.</p>
                </aside>
            </div>
        @endif
    </main>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
  document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
      const input = document.querySelector(`[name="${toggle.dataset.passwordToggle}"]`);
      if (!input) return;
      const showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
      toggle.querySelector('.material-symbols-outlined').textContent = showing ? 'visibility' : 'visibility_off';
    });
  });
</script>
</body>
</html>
