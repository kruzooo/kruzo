<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Customer Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="dashboard-page bg-surface text-on-surface antialiased">
    @php
        $customer = $customer ?? [];
        $cartCount = collect($cart)->sum('quantity');
    @endphp

    <header class="dashboard-header">
        <div class="dashboard-banner">FREE SHIPPING ACROSS THE PHILIPPINES ON ORDERS OVER ₱3,500</div>
        <div class="dashboard-header-inner">
            <a href="{{ route('home') }}">KRUZO <span>|</span> MNL</a>
            <nav class="dashboard-nav">
                <a href="{{ route('shop') }}">SHOP ALL</a>
                <a href="{{ route('cart') }}">BAG ({{ $cartCount }})</a>
                <a href="{{ route('login') }}">CUSTOMER LOGIN</a>
                <details class="dashboard-profile-menu">
                    <summary class="dashboard-header-profile" aria-label="Open account profile">
                        @if (data_get($customer, 'avatar'))
                            <img src="{{ data_get($customer, 'avatar') }}" alt="Profile photo">
                        @else
                            {{ strtoupper(substr(data_get($customer, 'first_name', ''), 0, 1) . substr(data_get($customer, 'last_name', ''), 0, 1)) ?: 'PP' }}
                        @endif
                    </summary>
                    <div class="dashboard-profile-menu-panel">
                        <a href="#account-details">ACCOUNT INFO</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">LOG OUT</button>
                        </form>
                    </div>
                </details>
                <strong>DASHBOARD</strong>
            </nav>
        </div>
    </header>

    <main class="dashboard-main">
        <section class="dashboard-hero">
            <div>
                <p>CUSTOMER PORTAL</p>
                <h1>{{ data_get($customer, 'first_name') ? 'Welcome back, ' . data_get($customer, 'first_name') . '.' : 'Customer dashboard.' }}</h1>
                <span>Manage your orders, delivery details, and current bag activity.</span>
            </div>
            <aside>
                <span class="material-symbols-outlined">verified_user</span>
                <p>ACCOUNT STATUS</p>
                <strong>{{ data_get($customer, 'email') ? 'SIGNED IN' : 'NOT SIGNED IN' }}</strong>
                <a class="dashboard-account-link" href="#account-details">ACCOUNT INFO</a>
            </aside>
        </section>

        <section class="dashboard-stats">
            <article>
                <span>OPEN BAG</span>
                <strong>{{ $cartCount }}</strong>
                <p>{{ $cartCount === 1 ? 'item ready' : 'items ready' }} for checkout</p>
            </article>
            <article>
                <span>LAST ORDER</span>
                <strong>{{ $lastOrder['number'] ?? 'NONE' }}</strong>
                <p>{{ $lastOrder ? 'Latest confirmed order' : 'No successful order yet' }}</p>
            </article>
            <article>
                <span>FULFILLMENT</span>
                <strong>{{ $lastOrder ? 'PROCESSING' : '—' }}</strong>
                <p>{{ $lastOrder ? 'Order received by the atelier' : 'No order activity yet' }}</p>
            </article>
            <article>
                <span>REWARDS</span>
                <strong>—</strong>
                <p>No rewards balance recorded</p>
            </article>
        </section>

        <section class="dashboard-layout">
            <div class="dashboard-column">
                <section class="dashboard-panel">
                    <div class="dashboard-panel-heading">
                        <p>ORDER ACTIVITY</p>
                        <h2>Recent Order</h2>
                    </div>
                    @if ($lastOrder)
                        <div class="dashboard-order-card">
                            <div>
                                <span>{{ strtoupper(str_replace('_', ' ', $lastOrder['status'] ?? 'order_received')) }}</span>
                                <h3>{{ $lastOrder['number'] }}</h3>
                                <p>Placed {{ $lastOrder['placed_at'] }}</p>
                            </div>
                            <strong>₱{{ number_format($lastOrder['subtotal'], 2) }}</strong>
                        </div>
                        <div class="dashboard-live-status">
                            <span>LIVE ORDER STATUS</span>
                            <strong>{{ strtoupper(str_replace('_', ' ', $lastOrder['status'] ?? 'order_received')) }}</strong>
                        </div>
                        <div class="dashboard-order-items">
                            <p>ORDER ITEMS</p>
                            @foreach ($lastOrder['cart'] as $item)
                                <div><span>{{ $item['quantity'] }} × {{ $item['name'] }}</span><strong>{{ $item['price'] }}</strong></div>
                            @endforeach
                        </div>
                        <div class="dashboard-timeline">
                            <div><span class="material-symbols-outlined">check_circle</span><p>Order received</p></div>
                            <div><span class="material-symbols-outlined">inventory_2</span><p>Atelier packing</p></div>
                            <div><span class="material-symbols-outlined">local_shipping</span><p>Dispatch queue</p></div>
                        </div>
                        <a class="dashboard-primary-action" href="{{ route('thank-you') }}">VIEW CONFIRMATION</a>
                    @else
                        <div class="dashboard-empty">
                            <span class="material-symbols-outlined">receipt_long</span>
                            <h3>No orders yet</h3>
                            <p>Your confirmed KRUZO drops will appear here after checkout.</p>
                            <a href="{{ route('shop') }}">START SHOPPING</a>
                        </div>
                    @endif
                </section>

                <section class="dashboard-panel">
                    <div class="dashboard-panel-heading">
                        <p>CURRENT BAG</p>
                        <h2>Ready For Checkout</h2>
                    </div>
                    @if (count($cart) > 0)
                        <div class="dashboard-bag-list">
                            @foreach ($cart as $item)
                                <article class="dashboard-bag-item">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                    <div>
                                        <span>QTY {{ $item['quantity'] }}</span>
                                        <h3>{{ $item['name'] }}</h3>
                                    </div>
                                    <strong>{{ $item['price'] }}</strong>
                                </article>
                            @endforeach
                        </div>
                        <div class="dashboard-total">
                            <span>Bag subtotal</span>
                            <strong>₱{{ number_format($cartSubtotal, 2) }}</strong>
                        </div>
                        <a class="dashboard-primary-action" href="{{ route('checkout') }}">PROCEED TO CHECKOUT</a>
                    @else
                        <div class="dashboard-empty compact">
                            <span class="material-symbols-outlined">shopping_bag</span>
                            <h3>Your bag is empty</h3>
                            <p>Add pieces from the SS25 archive to build your next order.</p>
                            <a href="{{ route('shop') }}">SHOP ALL</a>
                        </div>
                    @endif
                </section>
            </div>

            <aside class="dashboard-column">
                <section class="dashboard-panel">
                    <div class="dashboard-panel-heading" id="account-details">
                        <p>PROFILE</p>
                        <h2>Account Details</h2>
                    </div>
                    @if (data_get($customer, 'email'))
                        <div class="dashboard-profile">
                            @if (data_get($customer, 'avatar'))
                                <img class="dashboard-avatar dashboard-avatar-image" src="{{ data_get($customer, 'avatar') }}" alt="Profile photo">
                            @else
                                <div class="dashboard-avatar">{{ strtoupper(substr(data_get($customer, 'first_name', ''), 0, 1) . substr(data_get($customer, 'last_name', ''), 0, 1)) }}</div>
                            @endif
                            <div>
                                <strong>{{ trim(data_get($customer, 'first_name', '') . ' ' . data_get($customer, 'last_name', '')) }}</strong>
                                <span>{{ data_get($customer, 'email') }}</span>
                                @if (data_get($customer, 'phone'))<span>{{ data_get($customer, 'phone') }}</span>@endif
                            </div>
                        </div>
                        @if (session('profile_success'))
                            <p class="dashboard-form-success">{{ session('profile_success') }}</p>
                        @endif
                        @if ($errors->any())
                            <div class="dashboard-form-errors" role="alert">
                                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                            </div>
                        @endif
                        <form class="dashboard-profile-form" method="POST" action="{{ route('dashboard.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            <label>Profile photo<input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"></label>
                            @if (data_get($customer, 'avatar'))
                                <button class="dashboard-remove-avatar" type="submit" name="remove_avatar" value="1">REMOVE PROFILE PHOTO</button>
                            @endif
                            <div class="dashboard-form-grid">
                                <label>First name<input type="text" name="first_name" value="{{ data_get($customer, 'first_name') }}" required></label>
                                <label>Last name<input type="text" name="last_name" value="{{ data_get($customer, 'last_name') }}" required></label>
                            </div>
                            <label>Email<input type="email" name="email" value="{{ data_get($customer, 'email') }}" required></label>
                            <label>Phone<input type="tel" name="phone" value="{{ data_get($customer, 'phone') }}" required></label>
                            <label>Street address<input type="text" name="address" value="{{ data_get($customer, 'address') }}" required></label>
                            <label>Barangay<input type="text" name="barangay" value="{{ data_get($customer, 'barangay') }}" required></label>
                            <div class="dashboard-form-grid">
                                <label>City<input type="text" name="city" value="{{ data_get($customer, 'city') }}" required></label>
                                <label>Province<input type="text" name="province" value="{{ data_get($customer, 'province') }}" required></label>
                            </div>
                            <label>Postal code<input type="text" name="postal_code" value="{{ data_get($customer, 'postal_code') }}" required></label>
                            <button type="submit">SAVE ACCOUNT DETAILS</button>
                        </form>
                    @else
                        <div class="dashboard-empty compact">
                            <span class="material-symbols-outlined">person</span>
                            <h3>No profile data</h3>
                            <p>Complete checkout or sign in to load your account details.</p>
                        </div>
                    @endif
                </section>

                <section class="dashboard-panel">
                    <div class="dashboard-panel-heading">
                        <p>DELIVERY</p>
                        <h2>Delivery Address</h2>
                    </div>
                    @if (data_get($customer, 'address'))
                        <address>
                            {{ data_get($customer, 'address') }}<br>
                            {{ data_get($customer, 'barangay') }}<br>
                            {{ data_get($customer, 'city') }}, {{ data_get($customer, 'province') }} {{ data_get($customer, 'postal_code') }}
                        </address>
                    @else
                        <div class="dashboard-empty compact">
                            <span class="material-symbols-outlined">location_on</span>
                            <h3>No delivery address</h3>
                            <p>Your address will appear here after checkout.</p>
                        </div>
                    @endif
                </section>

                <section class="dashboard-panel dark">
                    <div class="dashboard-panel-heading">
                        <p>PAYMENT</p>
                        <h2>Checkout Preference</h2>
                    </div>
                    <div class="dashboard-payment">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                        <div>
                            @if (data_get($customer, 'payment_method'))
                                <strong>{{ strtoupper(data_get($customer, 'payment_method') === 'gcash' ? 'GCash / Maya' : 'Cash on Delivery') }}</strong>
                                <p>Payment method used on the latest order.</p>
                            @else
                                <strong>No payment preference</strong>
                                <p>A payment method will appear after checkout.</p>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="dashboard-actions">
                    <a href="#account-settings">ACCOUNT SETTINGS</a>
                    <a href="{{ route('shop') }}">SHOP NEW DROP</a>
                    <a href="{{ route('cart') }}">VIEW BAG</a>
                    <a href="{{ route('contact') }}">CONTACT / FEEDBACK</a>
                    <a href="{{ route('home') }}">BACK HOME</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">LOG OUT ACCOUNT</button>
                    </form>
                </section>
            </aside>
        </section>
    </main>
    <script>
        window.setInterval(() => {
            if (!document.querySelector('input:focus, select:focus, textarea:focus')) {
                window.location.reload();
            }
        }, 60000);
    </script>
</body>
</html>
