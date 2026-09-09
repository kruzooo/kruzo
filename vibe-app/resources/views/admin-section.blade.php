<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | {{ $sectionInfo['title'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
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
            <div class="admin-brand"><a href="{{ route('home') }}">KRUZO</a><span>MNL</span></div>
            <div class="admin-node"><i></i> MAKATI NODE 01 // ONLINE</div>
            <nav class="admin-menu">
                <a href="{{ route('admin.dashboard') }}"><span class="material-symbols-outlined">grid_view</span> Overview</a>
                <a class="{{ $section === 'orders' ? 'active' : '' }}" href="{{ route('admin.orders') }}"><span class="material-symbols-outlined">local_shipping</span> Live Orders &amp; Dispatch</a>
                <a href="{{ route('admin.inventory') }}"><span class="material-symbols-outlined">inventory_2</span> Inventory &amp; Vault SKUs</a>
                <a class="{{ $section === 'clientele' ? 'active' : '' }}" href="{{ route('admin.clientele') }}"><span class="material-symbols-outlined">styler</span> Clientele &amp; VIP Fittings</a>
                <a class="{{ $section === 'financials' ? 'active' : '' }}" href="{{ route('admin.financials') }}"><span class="material-symbols-outlined">receipt_long</span> Financials &amp; BIR</a>
                <a class="{{ $section === 'contact' ? 'active' : '' }}" href="{{ route('admin.contact') }}"><span class="material-symbols-outlined">inbox</span> Contact / Feedback Inbox</a>
                <a class="{{ $section === 'settings' ? 'active' : '' }}" href="{{ route('admin.settings') }}"><span class="material-symbols-outlined">settings</span> System Settings</a>
            </nav>
        </div>
        <div class="admin-user"><div class="admin-avatar">{{ strtoupper(substr($adminName, 0, 1) . substr(strrchr($adminName, ' '), 1, 1)) }}</div><div><strong>{{ $adminName }}</strong><span>{{ $adminRole }}</span></div></div>
    </aside>

    <div class="admin-shell">
        <header class="admin-topbar"><label><span class="material-symbols-outlined">search</span><input type="search" placeholder="Search operations data"></label><div><span class="admin-health"><i></i> SYS HEALTH // OPTIMAL</span><a class="admin-topbar-link" href="{{ route('admin.dashboard') }}">Back to overview</a></div></header>
        <main class="admin-main">
            <section class="admin-hero"><div><p>{{ $sectionInfo['eyebrow'] }}</p><h1>{{ $sectionInfo['title'] }}</h1><span>{{ $sectionInfo['description'] }}</span></div></section>

            @if ($section === 'orders')
                <section class="admin-panel"><div class="admin-panel-heading"><div><p>RECORDED CUSTOMER ACTIVITY</p><h2>Active Orders</h2></div><span class="admin-inventory-count">{{ count($orders) }} ORDERS</span></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Waybill</th><th>Customer</th><th>Payment</th><th>Total</th><th>Status</th></tr></thead><tbody>@forelse ($orders as $order)<tr><td><strong>{{ $order['number'] }}</strong></td><td>{{ $order['customer'] }}</td><td>{{ $order['payment'] }}</td><td>{{ $order['total'] }}</td><td><mark>{{ $order['status'] }}</mark></td></tr>@empty<tr><td class="admin-table-empty" colspan="5">No customer orders recorded yet.</td></tr>@endforelse</tbody></table></div></section>
            @elseif ($section === 'clientele')
                <section class="admin-panel"><div class="admin-panel-heading"><div><p>ORDER-BASED CUSTOMER RECORDS</p><h2>Clientele</h2></div></div><div class="admin-clientele-list">@forelse ($orders as $order)<article><span class="material-symbols-outlined">person</span><div><strong>{{ $order['customer'] }}</strong><p>Latest order {{ $order['number'] }} · {{ $order['status'] }}</p></div><b>{{ $order['total'] }}</b></article>@empty<p class="admin-data-empty">No customer profiles are available yet. Completed checkout records will appear here.</p>@endforelse</div></section>
            @elseif ($section === 'financials')
                <section class="admin-kpis"><article><span>Recorded Revenue</span><strong>₱{{ number_format($revenue, 2) }}</strong><p>From customer orders</p></article><article><span>Settlements</span><strong>{{ count($orders) }}</strong><p>Recorded transactions</p></article><article><span>Average Order</span><strong>₱{{ number_format(count($orders) ? $revenue / count($orders) : 0, 2) }}</strong><p>Current order average</p></article><article><span>Reporting Status</span><strong>LIVE</strong><p>Based on current records</p></article></section><section class="admin-panel"><div class="admin-panel-heading"><div><p>TRANSACTION LEDGER</p><h2>Financial Records</h2></div></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Payment</th><th>Total</th></tr></thead><tbody>@forelse ($orders as $order)<tr><td>{{ $order['number'] }}</td><td>{{ $order['customer'] }}</td><td>{{ $order['payment'] }}</td><td>{{ $order['total'] }}</td></tr>@empty<tr><td class="admin-table-empty" colspan="4">No financial records available yet.</td></tr>@endforelse</tbody></table></div></section>
            @elseif ($section === 'contact')
                <section class="admin-panel"><div class="admin-panel-heading"><div><p>INCOMING CLIENT COMMUNICATIONS</p><h2>Contact / Feedback Inbox</h2></div><span class="admin-inventory-count">{{ count($messages) }} MESSAGES</span></div><div class="admin-inbox-list">@forelse ($messages as $message)<article class="admin-inbox-message"><div class="admin-inbox-message-head"><div><strong>{{ $message['name'] }}</strong><span>{{ $message['contact'] }} · {{ $message['channel'] }}</span></div><mark>{{ $message['specialization'] }}</mark></div><p>{{ $message['message'] }}</p><small>{{ $message['reference'] }} · {{ $message['created_at'] ?? 'Recently submitted' }}</small></article>@empty<div class="admin-data-empty">No contact or feedback messages received yet.</div>@endforelse</div></section>
            @else
                <section class="admin-panel"><div class="admin-panel-heading"><div><p>ACTIVE WORKSPACE</p><h2>System Settings</h2></div></div><div class="admin-settings-list"><div><span>Admin operator</span><strong>{{ $adminName }}</strong></div><div><span>Clearance level</span><strong>{{ $adminRole }}</strong></div><div><span>Operations node</span><strong>MAKATI NODE 01</strong></div><div><span>Catalog storage</span><strong>{{ config('database.default') === 'mysql' ? 'MYSQL DATABASE' : 'LOCAL SESSION FALLBACK' }}</strong></div><div><span>Order status sync</span><strong>ENABLED</strong></div></div><form class="admin-logout-form" method="POST" action="{{ route('admin.logout') }}">@csrf<button class="admin-danger" type="submit"><span class="material-symbols-outlined">logout</span> LOG OUT ADMIN ACCOUNT</button></form></section>
            @endif
        </main>
    </div>
</body>
</html>
