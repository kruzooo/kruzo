// Public JavaScript entry point for shared hosting deployments.

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const profileImage = document.querySelector('header img[alt="Profile"]');
    const profileIcon = [...document.querySelectorAll('header .material-symbols-outlined')]
        .find((icon) => icon.textContent.trim() === 'person');
    let profileControl = profileImage || profileIcon?.closest('.rounded-full') || profileIcon;
    if (profileImage && window.customerAvatar) {
        profileImage.src = window.customerAvatar;
    } else if (profileImage && window.location.pathname === '/') {
        const initialsAvatar = document.createElement('div');
        initialsAvatar.className = profileImage.className;
        initialsAvatar.textContent = 'PP';
        initialsAvatar.setAttribute('aria-label', 'Pryvst Pedrera profile');
        initialsAvatar.style.cssText = 'display:flex;align-items:center;justify-content:center;background:#000;color:#fff;font-size:10px;font-weight:700;letter-spacing:.04em;';
        profileImage.replaceWith(initialsAvatar);
        profileControl = initialsAvatar;
    }
    if (profileControl) {
        profileControl.setAttribute('role', 'button');
        profileControl.setAttribute('tabindex', '0');
        profileControl.setAttribute('aria-label', 'Open account menu');
        profileControl.style.cursor = 'pointer';
        const accountMenu = document.createElement('div');
        accountMenu.className = 'account-menu hidden fixed z-[100] w-64 max-w-[calc(100vw-2rem)] text-white p-4';
        accountMenu.setAttribute('aria-label', 'Account menu');
        accountMenu.innerHTML = `
            <div class="flex items-center gap-3 pb-4 border-b border-[#444]">
                <div class="account-menu-avatar w-10 h-10 rounded-full bg-white text-black flex items-center justify-center font-bold">PP</div>
                <div class="min-w-0">
                    <p class="font-semibold truncate">Pryvst Pedrera</p>
                    <p class="text-xs text-gray-400 truncate">Customer account</p>
                </div>
            </div>
            <div class="pt-3 space-y-2">
                <a href="/dashboard" class="block w-full rounded-full border border-[#555] px-3 py-2 text-center text-sm font-semibold hover:bg-white hover:text-black transition-colors">Customer Dashboard</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="_token" value="${csrfToken || ''}">
                    <button type="submit" class="block w-full rounded-full border border-[#555] px-3 py-2 text-center text-sm font-semibold hover:bg-white hover:text-black transition-colors">Sign out</button>
                </form>
            </div>
            <div class="flex justify-between pt-4 text-[10px] uppercase tracking-wider text-gray-400">
                <span>Terms &amp; Privacy</span><span>Account</span>
            </div>`;
        if (window.customerAuthenticated !== true) {
            accountMenu.innerHTML = `
                <div class="pb-4 border-b border-[#444]">
                    <p class="font-semibold">Customer account</p>
                    <p class="text-xs text-gray-400 mt-1">Log in to access your dashboard.</p>
                </div>
                <a href="/login" class="mt-3 block w-full rounded-full border border-[#555] px-3 py-2 text-center text-sm font-semibold hover:bg-white hover:text-black transition-colors">Log in</a>`;
        }
        const menuAvatar = accountMenu.querySelector('.account-menu-avatar');
        if (menuAvatar && window.customerAvatar) {
            menuAvatar.textContent = '';
            const avatarImage = document.createElement('img');
            avatarImage.src = window.customerAvatar;
            avatarImage.alt = 'Profile photo';
            avatarImage.className = 'w-full h-full rounded-full object-cover';
            menuAvatar.appendChild(avatarImage);
        }
        accountMenu.insertAdjacentHTML('afterbegin', `
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#444]">
                <span class="font-bold tracking-[0.18em]">KRUZO MNL</span>
                <span class="rounded-full bg-[#2d2d2d] px-2 py-1 text-[10px] font-semibold tracking-wider text-gray-300">VIP CLIENT</span>
            </div>`);
        document.body.appendChild(accountMenu);

        const positionMenu = () => {
            const rect = profileControl.getBoundingClientRect();
            accountMenu.style.top = `${Math.min(rect.bottom + 8, window.innerHeight - accountMenu.offsetHeight - 8)}px`;
            accountMenu.style.right = `${Math.max(8, window.innerWidth - rect.right)}px`;
        };
        const toggleMenu = (event) => {
            event?.stopPropagation();
            const isHidden = accountMenu.classList.contains('hidden');
            accountMenu.classList.toggle('hidden', !isHidden);
            if (isHidden) positionMenu();
        };
        profileControl.addEventListener('click', toggleMenu);
        profileControl.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleMenu(event);
            }
        });
        document.addEventListener('click', (event) => {
            if (!accountMenu.contains(event.target) && event.target !== profileControl) accountMenu.classList.add('hidden');
        });
        window.addEventListener('resize', () => {
            if (!accountMenu.classList.contains('hidden')) positionMenu();
        });
    }

    const productOverlays = document.querySelectorAll('.product-view-overlay');
    if (productOverlays.length) {
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) document.body.classList.add('suppress-product-hover');
        });
        window.addEventListener('pointermove', () => {
            document.body.classList.remove('suppress-product-hover');
        }, { once: true });
    }

    const productOptions = document.getElementById('productOptions');
    if (productOptions) {
        const selectedColorLabel = document.getElementById('selectedColorLabel');

        productOptions.addEventListener('click', (event) => {
            const colorOption = event.target.closest('[data-product-color]');
            const sizeOption = event.target.closest('[data-product-size]');

            if (colorOption) {
                productOptions.querySelectorAll('[data-product-color]').forEach((color) => {
                    const selected = color === colorOption;
                    color.setAttribute('aria-pressed', String(selected));
                    color.classList.toggle('ring-2', selected);
                    color.classList.toggle('ring-primary', selected);
                    color.classList.toggle('shadow-md', selected);
                    color.classList.toggle('shadow-sm', !selected);
                    color.innerHTML = selected ? '<span class="material-symbols-outlined text-label-sm">check</span>' : '';
                });

                if (selectedColorLabel) selectedColorLabel.textContent = `${colorOption.dataset.productColor} (SELECTED)`;
            }

            if (sizeOption && !sizeOption.disabled) {
                productOptions.querySelectorAll('[data-product-size]').forEach((size) => {
                    const selected = size === sizeOption;
                    size.setAttribute('aria-pressed', String(selected));
                    size.classList.toggle('bg-primary', selected);
                    size.classList.toggle('text-on-primary', selected);
                    size.classList.toggle('shadow-md', selected);
                    size.classList.toggle('bg-surface-container-low', !selected);
                    size.classList.toggle('text-on-surface', !selected);
                });
            }
        });
    }

    const addToCart = async (button, data) => {
        if (!csrfToken || button.dataset.adding === 'true') return;

        const originalContent = button.innerHTML;
        button.dataset.adding = 'true';
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.innerHTML = '<span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span><span>ADDING...</span>';

        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 8000);

        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(data),
                signal: controller.signal,
            });

            if (!response.ok) throw new Error('Unable to add this item to the bag.');

            const result = await response.json();
            window.location.assign(result.redirect || '/cart');
        } catch (error) {
            button.innerHTML = '<span class="material-symbols-outlined text-[16px]">error</span><span>TRY AGAIN</span>';
            button.disabled = false;
            button.dataset.adding = 'false';
            button.removeAttribute('aria-busy');
            window.setTimeout(() => {
                if (button.dataset.adding === 'false') button.innerHTML = originalContent;
            }, 1800);
        } finally {
            window.clearTimeout(timeout);
        }
    };

    document.querySelectorAll('[data-product-slug]').forEach((card) => {
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'link');

        const openProduct = () => {
            window.location.href = `/product/${card.dataset.productSlug}`;
        };

        card.addEventListener('click', (event) => {
            if (event.target.closest('button, a')) return;
            openProduct();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openProduct();
            }
        });

        const quickAdd = card.querySelector('button:not([aria-label="Add to wishlist"])');
        if (quickAdd && quickAdd.textContent.includes('QUICK ADD')) {
            quickAdd.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                addToCart(quickAdd, {
                    slug: card.dataset.productSlug,
                    name: card.querySelector('h3')?.textContent.trim() || 'KRUZO product',
                    price: card.querySelector('.font-price-lg')?.textContent.trim() || '₱0.00',
                    image: card.querySelector('img')?.src || window.location.origin,
                });
            });
        }
    });

    const productAdd = document.getElementById('addToBagBtn');
    if (productAdd) {
        productAdd.addEventListener('click', (event) => {
            event.preventDefault();
            const selectedSize = productOptions?.querySelector('[data-product-size][aria-pressed="true"]')?.dataset.productSize;
            const selectedColor = productOptions?.querySelector('[data-product-color][aria-pressed="true"]')?.dataset.productColor;
            addToCart(productAdd, {
                slug: productAdd.dataset.productSlug,
                name: document.querySelector('h1')?.textContent.trim() || 'KRUZO product',
                price: productAdd.dataset.price || '₱0.00',
                image: document.querySelector('main img')?.src || window.location.origin,
                size: selectedSize,
                color: selectedColor,
            });
        });
    }
});
