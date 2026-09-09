<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Cart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="cart-page bg-surface text-on-surface font-body-md antialiased">
    <header class="cart-header border-b border-surface-container-highest bg-surface">
        <div class="cart-banner bg-primary px-gutter-mobile py-space-xs text-center text-on-primary lg:px-gutter-desktop">
            <span class="font-label-caps text-label-caps uppercase tracking-widest">FREE SHIPPING ACROSS THE PHILIPPINES ON ORDERS OVER ₱3,500</span>
        </div>
        <div class="cart-header-inner mx-auto flex h-20 max-w-max-width items-center justify-between px-gutter-mobile lg:px-gutter-desktop">
            <a href="{{ route('home') }}" class="font-headline-md text-title-sm font-bold tracking-widest text-primary">KRUZO <span class="font-normal text-on-surface-variant">|</span> MNL</a>
            <nav class="cart-nav flex items-center gap-space-md font-label-caps text-label-caps uppercase tracking-widest">
                <a href="{{ route('home') }}" class="font-bold text-on-surface-variant hover:text-primary">HOME</a>
                <a href="{{ route('shop') }}" class="text-on-surface-variant hover:text-primary">SHOP ALL</a>
                <a href="{{ route('cart') }}" class="font-bold text-primary">BAG ({{ collect($cart)->sum('quantity') }})</a>
            </nav>
        </div>
    </header>

    <main class="cart-main mx-auto max-w-max-width px-gutter-mobile py-space-3xl lg:px-gutter-desktop">
        <div class="cart-titlebar mb-space-xl flex flex-wrap items-end justify-between gap-space-md border-b border-surface-container-highest pb-space-md">
            <div>
                <p class="font-label-caps text-label-caps uppercase tracking-widest text-secondary">01 ARCHIVAL BAG / ACTIVE</p>
                <h1 class="mt-space-xs font-display-xl text-display-xl font-bold uppercase tracking-tight text-primary">Your Cart</h1>
            </div>
            <a href="{{ route('shop') }}" class="font-label-caps text-label-caps uppercase tracking-widest text-primary underline underline-offset-4">Continue shopping</a>
        </div>

        @if (count($cart) === 0)
            <section class="cart-empty border border-surface-container-highest bg-surface-container-lowest px-space-md py-space-3xl text-center">
                <span class="material-symbols-outlined text-5xl text-secondary">shopping_bag</span>
                <h2 class="mt-space-md font-headline-md text-headline-md font-bold uppercase text-primary">Your bag is empty</h2>
                <p class="mx-auto mt-space-xs max-w-md text-on-surface-variant">Select a piece from the SS25 archival collection and it will appear here.</p>
                <a href="{{ route('shop') }}" class="mt-space-lg inline-flex bg-primary px-space-lg py-space-md font-label-caps text-label-caps uppercase tracking-widest text-on-primary">SHOP ALL</a>
            </section>
        @else
            <div class="cart-layout grid grid-cols-1 items-start gap-space-2xl lg:grid-cols-12">
                <section class="cart-items space-y-space-sm lg:col-span-7">
                    @foreach ($cart as $item)
                        <article class="cart-item flex gap-space-md border-b border-surface-container-highest bg-surface-container-lowest p-space-sm sm:p-space-md" data-product-slug="{{ $item['slug'] ?? '' }}">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="cart-item-image h-32 w-24 object-cover sm:h-40 sm:w-32">
                            <div class="cart-item-details flex min-w-0 flex-1 flex-col justify-between gap-space-sm">
                                <div>
                                    <p class="font-label-caps text-label-caps uppercase tracking-widest text-secondary">SS25 ARCHIVE</p>
                                    <h2 class="mt-space-xs font-headline-md text-title-sm font-bold uppercase leading-snug text-primary">{{ $item['name'] }}</h2>
                                    @if (!empty($item['color']) || !empty($item['size']))
                                        <p class="mt-space-xs font-label-caps text-label-caps uppercase tracking-widest text-secondary">
                                            @if (!empty($item['color'])) COLOR: {{ $item['color'] }} @endif
                                            @if (!empty($item['size'])) <span class="ml-space-xs">SIZE: {{ $item['size'] }}</span> @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-end justify-between gap-space-sm">
                                    <div>
                                        <p class="font-label-caps text-label-caps uppercase text-secondary">QTY {{ $item['quantity'] }}</p>
                                        <p class="mt-1 font-price-lg text-price-lg font-bold text-primary">{{ $item['price'] }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('cart.remove') }}">
                                        @csrf
                                        <input type="hidden" name="slug" value="{{ $item['slug'] ?? '' }}">
                                        <button type="submit" class="font-label-caps text-label-caps uppercase tracking-widest text-secondary underline underline-offset-4 hover:text-primary">REMOVE</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                @php
                    $subtotal = collect($cart)->sum(fn ($item) => (float) str_replace([',', '₱'], '', $item['price']) * $item['quantity']);
                @endphp
                <aside class="cart-summary bg-primary p-space-md text-on-primary lg:col-span-5 lg:p-space-lg">
                    <p class="font-label-caps text-label-caps uppercase tracking-widest text-on-primary/70">SETTLEMENT SUMMARY</p>
                    <div class="mt-space-lg space-y-space-sm border-b border-on-primary/20 pb-space-lg">
                        <div class="cart-summary-row flex justify-between gap-space-md"><span class="font-label-caps text-label-caps uppercase">ITEMS</span><span>{{ collect($cart)->sum('quantity') }}</span></div>
                        <div class="cart-summary-row flex justify-between gap-space-md"><span class="font-label-caps text-label-caps uppercase">SUBTOTAL</span><span>₱{{ number_format($subtotal, 2) }}</span></div>
                        <div class="cart-summary-row flex justify-between gap-space-md"><span class="font-label-caps text-label-caps uppercase">DELIVERY</span><span>CALCULATED AT CHECKOUT</span></div>
                    </div>
                    <div class="mt-space-lg flex items-end justify-between gap-space-md"><span class="font-label-caps text-label-caps uppercase">TOTAL</span><span class="font-price-lg text-price-lg font-bold">₱{{ number_format($subtotal, 2) }}</span></div>
                    <a href="{{ route('checkout') }}" class="cart-checkout-button mt-space-lg block w-full bg-on-primary px-space-md py-space-md text-center font-label-caps text-label-caps uppercase tracking-widest text-primary">PROCEED TO CHECKOUT</a>
                    <p class="mt-space-md font-label-sm text-label-sm text-on-primary/70">GCASH, MAYA, COD, VISA, and Mastercard accepted.</p>
                </aside>
            </div>
        @endif
    </main>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
