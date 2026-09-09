<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-page">
@php
    $adminEmail = strtolower((string) session('admin_login.operator_id'));
    $adminName = $adminEmail === 'briggspedrera@gmail.com' ? 'Briggs Pedrera' : 'Pryvst Pedrera';
    $adminRole = match (session('admin_login.division')) {
        'ceo' => 'Chief Executive Officer',
        'admin' => 'System Administrator',
        default => 'Operations Director',
    };
@endphp
    <aside class="admin-sidebar">
        <div>
            <div class="admin-brand">
                <a href="{{ route('home') }}">KRUZO</a>
                <span>MNL</span>
            </div>
            <div class="admin-node"><i></i> MAKATI NODE 01 // ONLINE</div>
            <nav class="admin-menu">
                <a class="active" href="{{ route('admin.dashboard') }}"><span class="material-symbols-outlined">grid_view</span> Overview</a>
                <a href="{{ route('admin.orders') }}"><span class="material-symbols-outlined">local_shipping</span> Live Orders &amp; Dispatch</a>
                <a href="{{ route('admin.inventory') }}"><span class="material-symbols-outlined">inventory_2</span> Inventory &amp; Vault SKUs</a>
                <a href="{{ route('admin.clientele') }}"><span class="material-symbols-outlined">styler</span> Clientele &amp; VIP Fittings</a>
                <a href="{{ route('admin.financials') }}"><span class="material-symbols-outlined">receipt_long</span> Financials &amp; BIR</a>
                <a href="{{ route('admin.contact') }}"><span class="material-symbols-outlined">inbox</span> Contact / Feedback Inbox</a>
                <a href="{{ route('admin.settings') }}"><span class="material-symbols-outlined">settings</span> System Settings</a>
            </nav>
        </div>
        <div class="admin-user">
            <div class="admin-avatar">{{ strtoupper(substr($adminName, 0, 1) . substr(strrchr($adminName, ' '), 1, 1)) }}</div>
            <div>
                <strong>{{ $adminName }}</strong>
                <span>{{ $adminRole }}</span>
            </div>
        </div>
    </aside>

    <div class="admin-shell">
        <header class="admin-topbar">
            <label>
                <span class="material-symbols-outlined">search</span>
                <input type="search" placeholder="Search waybill, SKU, customer or NFC tag">
            </label>
            <div>
                <span class="admin-health"><i></i> SYS HEALTH // OPTIMAL</span>
                <button type="button"><span class="material-symbols-outlined">add</span> New Dispatch</button>
            </div>
        </header>

        <main class="admin-main">
            <section class="admin-hero">
                <div>
                    <p>OPERATIONAL COMMAND CENTER</p>
                    <h1>Makati Atelier Dispatch Dock 01</h1>
                    <span>Monitor live orders, settlement gateways, stock velocity, and VIP fitting schedules.</span>
                </div>
                <div class="admin-hero-actions">
                    <button type="button">TODAY</button>
                    <button type="button">7D</button>
                    <button type="button">MTD</button>
                </div>
            </section>
            @if (session('status_success') || session('status_error'))
                <p class="admin-status-message {{ session('status_error') ? 'error' : '' }}">{{ session('status_success') ?? session('status_error') }}</p>
            @endif

            <section class="admin-kpis">
                <article>
                    <span>Gross Revenue MTD</span>
                    <strong>₱{{ number_format($revenue, 2) }}</strong>
                    <p>Recorded customer orders</p>
                </article>
                <article>
                    <span>Live Dispatch Pipeline</span>
                    <strong>{{ $orderCount }}</strong>
                    <p>Orders awaiting fulfillment</p>
                </article>
                <article>
                    <span>Average Order Value</span>
                    <strong>₱{{ number_format($averageOrderValue, 2) }}</strong>
                    <p>Based on recorded orders</p>
                </article>
                <article>
                    <span>Vault Stock Velocity</span>
                    <strong>{{ $inventoryCount }}</strong>
                    <p>{{ $lowStockCount }} SKUs at or below buffer</p>
                </article>
            </section>

            <section class="admin-grid">
                <div class="admin-column wide">
                    <section class="admin-panel">
                        <div class="admin-panel-heading">
                            <div>
                                <p>LIVE DISPATCH</p>
                                <h2>Active Waybills</h2>
                            </div>
                            <a href="{{ route('shop') }}">VIEW STORE</a>
                        </div>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Waybill</th>
                                        <th>Client</th>
                                        <th>Manifest</th>
                                        <th>Courier</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        <tr>
                                            <td><strong>{{ $order['number'] }}</strong></td>
                                            <td>{{ $order['customer'] }}<span>{{ $order['email'] }}</span><span>{{ $order['destination'] }}</span></td>
                                            <td>{{ $order['items'] }}</td>
                                            <td>{{ $order['courier'] }}</td>
                                            <td>{{ $order['payment'] }}</td>
                                            <td><strong>{{ $order['total'] }}</strong></td>
                                            <td>
                                                <form class="admin-status-form" method="POST" action="{{ route('admin.order.status', $order['number']) }}">
                                                    @csrf
                                                    <select aria-label="Update status for {{ $order['number'] }}" name="status" onchange="this.form.submit()">
                                                        @foreach (['order_received' => 'ORDER RECEIVED', 'processing' => 'PROCESSING', 'shipped' => 'SHIPPED', 'delivered' => 'DELIVERED', 'cancelled' => 'CANCELLED'] as $statusKey => $statusLabel)
                                                            <option value="{{ $statusKey }}" @selected(($order['status_key'] ?? 'order_received') === $statusKey)>{{ $statusLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="admin-table-empty">No customer orders recorded yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                </div>

                <aside class="admin-column">
                    <section class="admin-panel">
                        <div class="admin-panel-heading">
                            <div>
                                <p>GATEWAY TELEMETRY</p>
                                <h2>Settlements</h2>
                            </div>
                            <span class="material-symbols-outlined">account_balance</span>
                        </div>
                        <div class="admin-settlements">
                            @forelse ($settlements as $settlement)
                                <div><span>{{ $settlement['name'] }}</span><strong>{{ $settlement['total'] }}</strong></div>
                            @empty
                                <p class="admin-data-empty">No settlement data recorded yet.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="admin-panel danger">
                        <div class="admin-panel-heading">
                            <div>
                                <p>STOCK DEPLETION</p>
                                <h2>Vault Re-Stock</h2>
                            </div>
                            <span class="material-symbols-outlined">warning</span>
                        </div>
                        <div class="admin-stock">
                            @forelse ($inventory as $item)
                                <article>
                                    <strong>{{ $item->name }}</strong>
                                    <span>{{ $item->stock }} units left</span>
                                    <progress max="{{ max($item->stock + $item->low_stock_threshold, 1) }}" value="{{ $item->stock }}"></progress>
                                </article>
                            @empty
                                <p class="admin-data-empty">No inventory records found.</p>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </section>
        </main>
    </div>
    <script>
        window.setTimeout(() => window.location.reload(), 60000);
    </script>
</body>
</html>
