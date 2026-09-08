<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRUZO MNL | Product Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-page">
    <aside class="admin-sidebar">
        <div>
            <div class="admin-brand"><a href="{{ route('home') }}">KRUZO</a><span>MNL</span></div>
            <div class="admin-node"><i></i> MAKATI NODE 01 // ONLINE</div>
            <nav class="admin-menu">
                <a href="{{ route('admin.dashboard') }}"><span class="material-symbols-outlined">grid_view</span> Overview</a>
                <a href="{{ route('admin.orders') }}"><span class="material-symbols-outlined">local_shipping</span> Live Orders &amp; Dispatch</a>
                <a class="active" href="{{ route('admin.inventory') }}"><span class="material-symbols-outlined">inventory_2</span> Inventory &amp; Vault SKUs</a>
                <a href="{{ route('admin.clientele') }}"><span class="material-symbols-outlined">styler</span> Clientele &amp; VIP Fittings</a>
                <a href="{{ route('admin.financials') }}"><span class="material-symbols-outlined">receipt_long</span> Financials &amp; BIR</a>
                <a href="{{ route('admin.settings') }}"><span class="material-symbols-outlined">settings</span> System Settings</a>
            </nav>
        </div>
        <div class="admin-user"><div class="admin-avatar">PP</div><div><strong>Pryvst Pedrera</strong><span>Operations Director</span></div></div>
    </aside>

    <div class="admin-shell">
        <header class="admin-topbar">
            <label><span class="material-symbols-outlined">search</span><input type="search" placeholder="Search inventory by SKU or product name"></label>
            <div><span class="admin-health"><i></i> SYS HEALTH // OPTIMAL</span><a class="admin-topbar-link" href="{{ route('admin.dashboard') }}">Back to overview</a></div>
        </header>

        <main class="admin-main">
            <section class="admin-hero">
                <div>
                    <p>CATALOG OPERATIONS</p>
                    <h1>Product Inventory</h1>
                    <span>Manage names, prices, stock levels, and low-stock alerts for every catalog SKU.</span>
                </div>
            </section>

            @if (session('inventory_success') || session('inventory_error'))
                <p class="admin-status-message {{ session('inventory_error') ? 'error' : '' }}">{{ session('inventory_success') ?? session('inventory_error') }}</p>
            @endif

            <div class="admin-inventory-grid">
                <section class="admin-panel">
                    <div class="admin-panel-heading"><div><p>NEW CATALOG ITEM</p><h2>Add Product</h2></div></div>
                    <form class="admin-inventory-form" method="POST" action="{{ route('admin.inventory.store') }}" enctype="multipart/form-data">
                        @csrf
                        <label>SKU <input name="sku" value="{{ old('sku') }}" placeholder="product-sku" required></label>
                        <label>Product name <input name="name" value="{{ old('name') }}" placeholder="Product name" required></label>
                        <label>Product image <input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
                        <div class="admin-inventory-fields">
                            <label>Price <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01" required></label>
                            <label>Stock <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required></label>
                        </div>
                        <label>Low-stock alert at <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" min="0" required></label>
                        <button class="admin-primary" type="submit">ADD PRODUCT</button>
                    </form>
                </section>

                <section class="admin-panel">
                    <div class="admin-panel-heading"><div><p>LIVE CATALOG RECORDS</p><h2>All Products</h2></div><span class="admin-inventory-count">{{ $inventory->count() }} SKUs</span></div>
                    <div class="admin-inventory-list">
                        @forelse ($inventory as $item)
                            <form class="admin-inventory-item" method="POST" action="{{ route('admin.inventory.update', $item->sku) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="admin-inventory-item-head"><div><strong>{{ $item->sku }}</strong><span>Catalog SKU</span></div><span class="{{ $item->stock <= $item->low_stock_threshold ? 'admin-inventory-low' : 'admin-inventory-ready' }}">{{ $item->stock <= $item->low_stock_threshold ? 'LOW STOCK' : 'IN STOCK' }}</span></div>
                                <div class="admin-inventory-fields">
                                    <label>Product name <input name="name" value="{{ $item->name }}" required></label>
                                    <label>Change image <input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
                                    <label>Price <input type="number" name="price" value="{{ $item->price }}" min="0" step="0.01" required></label>
                                    <label>Stock <input type="number" name="stock" value="{{ $item->stock }}" min="0" required></label>
                                    <label>Alert threshold <input type="number" name="low_stock_threshold" value="{{ $item->low_stock_threshold }}" min="0" required></label>
                                </div>
                                <div class="admin-inventory-actions">
                                    <button class="admin-secondary" type="submit">SAVE CHANGES</button>
                                    <button class="admin-danger" type="submit" name="_method" value="DELETE" formmethod="POST" formaction="{{ route('admin.inventory.delete', $item->sku) }}" onclick="return confirm('Remove this product from inventory?')">DELETE PRODUCT</button>
                                </div>
                            </form>
                        @empty
                            <p class="admin-data-empty">No inventory records found. Configure MySQL, then run <code>php artisan migrate --seed</code>.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
