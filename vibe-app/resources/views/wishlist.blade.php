<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KRUZO MNL | Wishlist</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { primary: '#000000', 'on-primary': '#ffffff', surface: '#f9f9f9', 'surface-container-lowest': '#ffffff', 'surface-container-low': '#f3f3f3', 'surface-container-high': '#e8e8e8', 'surface-container-highest': '#e2e2e2', 'on-surface': '#1a1c1c', 'on-surface-variant': '#444748', secondary: '#5f5e5e', 'surface-bright': '#f9f9f9' },
            spacing: { 'max-width': '88rem', 'gutter-mobile': '1rem', 'gutter-desktop': '2rem', 'space-xs': '.5rem', 'space-sm': '.75rem', 'space-md': '1rem', 'space-lg': '1.5rem', 'space-xl': '2rem', 'space-2xl': '3rem', 'space-3xl': '4.5rem' },
            fontFamily: { 'headline-md': ['Syne'], 'headline-lg': ['Syne'], 'display-xl': ['Syne'], 'body-lg': ['Inter'], 'body-md': ['Inter'], 'title-sm': ['Inter'], 'label-caps': ['Inter'], 'price-lg': ['Inter'] },
            fontSize: { 'headline-md': ['1.75rem', { lineHeight: '1.2' }], 'headline-lg': ['2.5rem', { lineHeight: '1.1' }], 'display-xl': ['4.5rem', { lineHeight: '1' }], 'body-lg': ['1rem', { lineHeight: '1.6' }], 'body-md': ['.875rem', { lineHeight: '1.5' }], 'title-sm': ['1.125rem', { lineHeight: '1.4' }], 'label-caps': ['.6875rem', { lineHeight: '1' }], 'price-lg': ['1.25rem', { lineHeight: '1.2' }] }
        } };
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .wishlist-page { margin: 0; color: #1a1c1c; background: #f9f9f9; font-family: Inter, Arial, sans-serif; }
        .wishlist-page .hidden { display: none !important; }
        .wishlist-page > header { position: fixed; inset: 0 0 auto; z-index: 50; background: rgba(255,255,255,.96); border-bottom: 1px solid #e2e2e2; }
        .wishlist-page > header > div:first-child { min-height: 34px; padding: 9px 32px; color: #fff; background: #000; text-align: center; font-size: 11px; letter-spacing: .12em; }
        .wishlist-page > header > div:nth-child(2) { height: 80px; max-width: 1408px; margin: 0 auto; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .wishlist-page > header a { color: #000; text-decoration: none; white-space: nowrap; }
        .wishlist-page > header nav { display: flex; align-items: center; gap: 20px; font-size: 11px; font-weight: 700; letter-spacing: .14em; }
        .wishlist-page > header nav a:last-child { padding: 12px 16px; color: #fff; background: #000; }
        .wishlist-page main { padding-top: 114px; min-height: 100vh; }
        .wishlist-page main > section { border-bottom: 1px solid #e2e2e2; background: #fff; }
        .wishlist-page main > section:nth-child(2) { background: #f9f9f9; }
        .wishlist-page .max-w-max-width { width: min(100% - 64px, 1408px); max-width: 1408px; margin: 0 auto; }
        .wishlist-page main > section:first-child .max-w-max-width { padding: 48px 0; }
        .wishlist-page main > section:nth-child(2) .max-w-max-width { padding: 48px 0 72px; }
        .wishlist-page h1 { max-width: 900px; margin: 0; color: #000; font-family: Syne, Arial, sans-serif; font-size: clamp(34px, 4vw, 64px); line-height: 1.02; letter-spacing: 0; }
        .wishlist-page h2 { margin: 0; color: #000; font-family: Syne, Arial, sans-serif; font-size: 28px; line-height: 1.15; }
        .wishlist-page p { line-height: 1.55; }
        .wishlist-page main > section:first-child .flex.flex-col.lg\\:flex-row { display: flex; flex-direction: row; align-items: end; justify-content: space-between; gap: 32px; }
        .wishlist-page main > section:first-child .inline-flex { display: inline-flex; align-items: center; }
        .wishlist-page main > section:first-child a.inline-flex { padding: 14px 20px; color: #fff; background: #000; text-decoration: none; font-size: 11px; font-weight: 700; letter-spacing: .14em; white-space: nowrap; }
        .wishlist-page #wishlist-total { font-size: 28px; }
        .wishlist-page #wishlist-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .wishlist-page [data-wishlist-card] { min-width: 0; display: flex; flex-direction: column; border: 1px solid #e2e2e2; background: #fff; }
        .wishlist-page [data-wishlist-card] > div:first-child { position: relative; aspect-ratio: 3 / 4; overflow: hidden; background: #f3f3f3; }
        .wishlist-page [data-wishlist-card] img { width: 100%; height: 100%; display: block; object-fit: cover; }
        .wishlist-page [data-wishlist-remove] { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border: 1px solid rgba(255,255,255,.85); border-radius: 50%; background: transparent !important; color: #fff !important; cursor: pointer; text-shadow: 0 1px 3px rgba(0,0,0,.8); }
        .wishlist-page [data-wishlist-remove]:hover { background: transparent !important; color: #fff !important; border-color: #fff; }
        .wishlist-page [data-wishlist-card] > div:last-child { min-height: 150px; padding: 16px; display: flex; flex-direction: column; gap: 14px; }
        .wishlist-page [data-wishlist-card] h3 { margin: 0; color: #000; font-size: 18px; line-height: 1.25; }
        .wishlist-page [data-wishlist-card] p { margin: 8px 0 0; color: #5f5e5e; font-size: 14px; }
        .wishlist-page [data-wishlist-card] button:last-child { width: 100%; padding: 12px; border: 0; color: #fff; background: #000; cursor: pointer; font-size: 11px; font-weight: 700; letter-spacing: .14em; }
        @media (max-width: 900px) { .wishlist-page #wishlist-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .wishlist-page main > section:first-child .flex.flex-col.lg\\:flex-row { flex-direction: column; align-items: flex-start; } }
        @media (max-width: 560px) { .wishlist-page > header > div:nth-child(2) { padding: 0 16px; } .wishlist-page > header nav a:first-child { display: none; } .wishlist-page .max-w-max-width { width: min(100% - 32px, 1408px); } .wishlist-page #wishlist-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="wishlist-page bg-surface-container-lowest font-body-md text-on-surface antialiased">
<header class="fixed top-0 left-0 w-full z-50 bg-surface-container-lowest/95 backdrop-blur-md border-b border-surface-container-highest">
    <div class="bg-primary text-on-primary px-gutter-mobile lg:px-gutter-desktop py-space-xs text-center font-label-caps text-label-caps uppercase tracking-widest">MANILA ATELIER ARCHIVE • SAME-DAY DISPATCH OVER ₱3,500</div>
    <div class="h-20 max-w-max-width mx-auto px-gutter-mobile lg:px-gutter-desktop flex items-center justify-between gap-space-md">
        <a href="{{ route('home') }}" class="font-headline-md text-headline-md font-bold tracking-tight text-primary">KRUZO <span class="font-normal text-on-surface-variant">| MNL</span></a>
        <nav class="flex items-center gap-space-md font-label-caps text-label-caps uppercase tracking-widest">
            <a href="{{ route('home') }}" class="text-on-surface-variant hover:text-primary transition-colors">HOME</a>
            <a href="{{ route('shop') }}" class="text-on-surface-variant hover:text-primary transition-colors">SHOP ALL</a>
            <a href="{{ route('cart') }}" class="bg-primary text-on-primary px-space-sm py-space-xs">BAG</a>
        </nav>
    </div>
</header>
<main class="pt-20 min-h-screen">
    <section class="border-b border-surface-container-highest bg-surface-container-lowest">
        <div class="max-w-max-width mx-auto px-gutter-mobile md:px-gutter-desktop py-space-3xl">
            <div class="flex items-center gap-space-xs mb-space-sm">
                <span class="bg-primary text-on-primary px-2 py-1 font-label-caps text-[10px] uppercase tracking-widest">CLIENT ARCHIVE</span>
                <span class="font-label-caps text-label-caps uppercase text-secondary tracking-wider">SAVED PIECES REPOSITORY</span>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-lg">
                <div class="max-w-3xl">
                    <h1 class="font-display-xl text-display-xl uppercase text-primary tracking-tight">CURATED ARCHIVE &amp; SAVED PIECES</h1>
                    <p class="mt-space-sm font-body-lg text-body-lg text-on-surface-variant">Keep the pieces you are watching close. Your saved archive stays available in this browser while stock and pricing remain current.</p>
                </div>
                <a href="{{ route('shop') }}" class="inline-flex items-center justify-center bg-primary text-on-primary px-space-lg py-space-md font-label-caps text-label-caps uppercase tracking-widest">CONTINUE SHOPPING</a>
            </div>
            <div class="mt-space-2xl grid grid-cols-2 md:grid-cols-3 border border-surface-container-highest bg-surface-container-low">
                <div class="p-space-md border-r border-surface-container-highest"><span class="font-label-caps text-[10px] uppercase text-secondary tracking-widest">MONITORED OBJECTS</span><strong class="mt-1 block font-headline-md text-headline-md text-primary" id="wishlist-total">0</strong></div>
                <div class="p-space-md border-r border-surface-container-highest"><span class="font-label-caps text-[10px] uppercase text-secondary tracking-widest">ARCHIVE STATUS</span><strong class="mt-1 block font-title-sm text-title-sm uppercase text-primary">LIVE SYNC</strong></div>
                <div class="hidden md:block p-space-md"><span class="font-label-caps text-[10px] uppercase text-secondary tracking-widest">DISPATCH</span><strong class="mt-1 block font-title-sm text-title-sm uppercase text-primary">SAME-DAY NCR</strong></div>
            </div>
        </div>
    </section>
    <section class="bg-surface-bright py-space-2xl">
        <div class="max-w-max-width mx-auto px-gutter-mobile md:px-gutter-desktop">
            <div class="flex items-center justify-between mb-space-lg border-b border-surface-container-highest pb-space-sm"><h2 class="font-headline-md text-headline-md uppercase text-primary">ALL SAVED</h2><span class="font-label-caps text-label-caps uppercase text-secondary">CURRENT CATALOG</span></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md" id="wishlist-grid">
                @foreach ($inventory as $slug => $item)
                    <article class="wishlist-card bg-surface-container-lowest border border-surface-container-highest flex flex-col" data-product-slug="{{ $slug }}" data-wishlist-card>
                        <div class="relative aspect-[3/4] bg-surface-container-low overflow-hidden"><img src="{{ $item['image'] ?? asset('images/products/' . $slug . '.png') }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover"><button type="button" data-wishlist-remove title="Remove from wishlist" aria-label="Remove {{ $item['name'] }} from wishlist" class="absolute top-3 right-3 w-8 h-8 rounded-full border border-white/85 bg-transparent !text-white flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">delete</span></button></div>
                        <div class="p-space-md flex flex-col flex-1 gap-space-md"><div class="flex-1"><h3 class="font-title-sm text-title-sm uppercase font-bold text-primary">{{ $item['name'] }}</h3><p class="mt-space-xs font-body-md text-body-md text-secondary">{{ $item['material'] ?? 'ARCHIVAL MATERIAL' }}</p></div><div class="flex items-center justify-between gap-space-sm"><span class="font-price-lg text-price-lg font-bold text-primary">₱{{ number_format((float) $item['price'], 2) }}</span><a href="{{ route('product', $slug) }}" class="font-label-caps text-label-caps uppercase text-primary underline underline-offset-4">VIEW</a></div><button type="button" class="w-full bg-primary text-on-primary py-space-sm font-label-caps text-label-caps uppercase tracking-widest">ADD TO BAG</button></div>
                    </article>
                @endforeach
            </div>
            <div class="hidden border border-surface-container-highest bg-surface-container-lowest p-space-3xl text-center" id="wishlist-empty"><span class="material-symbols-outlined text-5xl text-secondary">favorite_border</span><h2 class="mt-space-md font-headline-md text-headline-md uppercase text-primary">YOUR ARCHIVE IS EMPTY</h2><p class="mt-space-xs text-secondary">Save a product from the shop to keep it here.</p><a href="{{ route('shop') }}" class="mt-space-lg inline-flex bg-primary text-on-primary px-space-lg py-space-md font-label-caps text-label-caps uppercase tracking-widest">BROWSE SHOP</a></div>
        </div>
    </section>
</main>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const key = 'kruzo-wishlist';
    const read = () => { try { const value = JSON.parse(localStorage.getItem(key) || '[]'); return Array.isArray(value) ? value : []; } catch (error) { return []; } };
    const sync = () => {
        const saved = read();
        let visible = 0;
        document.querySelectorAll('[data-wishlist-card]').forEach((card) => {
            const isSaved = saved.includes(card.dataset.productSlug);
            card.classList.toggle('hidden', !isSaved);
            if (isSaved) visible++;
        });
        document.getElementById('wishlist-total').textContent = visible;
        document.getElementById('wishlist-empty').classList.toggle('hidden', visible !== 0);
        document.getElementById('wishlist-grid').classList.toggle('hidden', visible === 0);
    };
    document.querySelectorAll('[data-wishlist-remove]').forEach((button) => button.addEventListener('click', () => {
        const slug = button.closest('[data-product-slug]').dataset.productSlug;
        const saved = read().filter((item) => item !== slug);
        localStorage.setItem(key, JSON.stringify(saved));
        sync();
    }));
    sync();
});
</script>
<script>window.customerAuthenticated = @json(session()->has('customer_login')); window.customerAvatar = @json(session('customer_profile.avatar'));</script>
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
