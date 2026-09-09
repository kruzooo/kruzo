// Public JavaScript entry point for shared hosting deployments.

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    document.querySelectorAll('header [data-path="editorial"]').forEach((link) => link.remove());

    const brandContainer = document.querySelector('header .h-20 > div > div:first-child');
    let storefrontLogo = document.querySelector('header img[alt="KRUZO Architectural Geometric Logo"], header img[alt="KRUZO MNL"], header img[alt*="Brand logo"]');
    if (!storefrontLogo && brandContainer) {
        [...brandContainer.children]
            .find((child) => child.tagName === 'SPAN' && child.textContent.trim() === 'KRUZO MNL')
            ?.remove();
        storefrontLogo = document.createElement('img');
        storefrontLogo.className = 'h-8 w-auto object-contain';
        storefrontLogo.alt = 'KRUZO MNL logo';
        brandContainer.prepend(storefrontLogo);
    }
    if (storefrontLogo) {
        storefrontLogo.src = '/images/kruzo-logo.svg';
        storefrontLogo.alt = 'KRUZO MNL logo';
    }

    const productImageFallbacks = {
        'k-01-structural-boxy-tee': '/images/products/k-07-raw-cut-box-tee.png',
        'brutalist-monolith-cuff-ring-set': '/images/products/k-11-modular-chest-harness.png',
        'k-02-dropped-raglan-longline': '/images/products/k-06-washed-cargo-tee.png',
        'geometric-carabiner-key-tether': '/images/products/k-11-modular-chest-harness.png',
        'k-03-wide-leg-pleated-cargo': '/images/products/k-09-modular-field-cargo.png',
        'atelier-heavyweight-tank': '/images/products/k-06-washed-cargo-tee.png',
        'modular-crossbody-chest-rig': '/images/products/k-11-modular-chest-harness.png',
        'k-04-sculpted-oversized-hoodie': '/images/products/k-08-sculpted-concrete-hoodie.png',
    };
    document.querySelectorAll('[data-product-slug] img').forEach((image) => {
        const fallback = productImageFallbacks[image.closest('[data-product-slug]')?.dataset.productSlug];
        if (!fallback) return;
        const useFallback = () => {
            if (!image.src.endsWith(fallback)) image.src = fallback;
        };
        image.addEventListener('error', useFallback, { once: true });
        if (image.complete && image.naturalWidth === 0) useFallback();
    });
    const currentProductSlug = window.location.pathname.match(/^\/product\/([^/]+)/)?.[1];
    const currentProductFallback = currentProductSlug && productImageFallbacks[currentProductSlug];
    if (currentProductFallback) {
        document.querySelectorAll('main img').forEach((image) => {
            const useProductFallback = () => {
                if (!image.src.endsWith(currentProductFallback)) image.src = currentProductFallback;
            };
            image.addEventListener('error', useProductFallback, { once: true });
            if (image.complete && image.naturalWidth === 0) useProductFallback();
        });
    }
    document.querySelectorAll('footer img[alt*="KRUZO"]').forEach((image) => {
        image.src = '/images/kruzo-logo.svg';
        image.alt = 'KRUZO MNL logo';
    });
    document.querySelectorAll('header img').forEach((image) => {
        if (/^Profile$/i.test(image.alt)) return;
        const replaceBrokenLogo = () => {
            if (image.naturalWidth !== 0) return;
            if (!image.src.endsWith('/images/kruzo-logo.svg')) image.src = '/images/kruzo-logo.svg';
        };
        image.addEventListener('error', replaceBrokenLogo, { once: true });
        if (image.complete && image.naturalWidth === 0) replaceBrokenLogo();
    });

    const headerBagButton = [...document.querySelectorAll('header button')]
        .find((button) => /BAG\s*\(/i.test(button.textContent));
    if (headerBagButton) {
        headerBagButton.onclick = () => window.location.assign('/cart');
    }

    const profileImage = document.querySelector('header img[alt="Profile"]');
    const profileIcon = [...document.querySelectorAll('header .material-symbols-outlined')]
        .find((icon) => icon.textContent.trim() === 'person');
    let profileControl = profileImage || profileIcon?.closest('.rounded-full') || profileIcon;
    const profileName = window.customerName || 'Pryvst Pedrera';
    const profileInitials = profileName
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join('') || 'PP';
    if (profileImage && window.customerAvatar) {
        profileImage.src = window.customerAvatar;
    } else if (profileImage && window.customerAuthenticated === true) {
        const initialsAvatar = document.createElement('div');
        initialsAvatar.className = profileImage.className;
        initialsAvatar.textContent = 'PP';
        initialsAvatar.setAttribute('aria-label', 'Pryvst Pedrera profile');
        initialsAvatar.style.cssText = 'display:flex;align-items:center;justify-content:center;background:#000;color:#fff;font-size:10px;font-weight:700;letter-spacing:.04em;';
        profileImage.replaceWith(initialsAvatar);
        profileControl = initialsAvatar;
    } else if (profileImage) {
        const guestAvatar = document.createElement('div');
        guestAvatar.className = profileImage.className;
        guestAvatar.innerHTML = '<span class="material-symbols-outlined" aria-hidden="true">person</span>';
        guestAvatar.setAttribute('aria-label', 'Guest account');
        guestAvatar.style.cssText = 'display:flex;align-items:center;justify-content:center;background:#000;color:#fff;';
        profileImage.replaceWith(guestAvatar);
        profileControl = guestAvatar;
    }
    if (!profileImage && profileControl && window.customerAuthenticated === true) {
        if (window.customerAvatar) {
            profileControl.innerHTML = '';
            const avatarImage = document.createElement('img');
            avatarImage.src = window.customerAvatar;
            avatarImage.alt = 'Profile photo';
            avatarImage.className = 'w-full h-full rounded-full object-cover';
            profileControl.appendChild(avatarImage);
        } else {
            profileControl.innerHTML = '';
            profileControl.textContent = profileInitials;
            profileControl.style.cssText = 'display:flex;align-items:center;justify-content:center;background:#000;color:#fff;font-size:10px;font-weight:700;letter-spacing:.04em;';
        }
        profileControl.setAttribute('aria-label', `${profileName} profile`);
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
                <div class="account-menu-avatar w-10 h-10 rounded-full bg-white text-black flex items-center justify-center font-bold">${profileInitials}</div>
                <div class="min-w-0">
                    <p class="font-semibold truncate">${profileName}</p>
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

    const wishlistKey = 'kruzo-wishlist';
    let wishlist = [];
    try {
        wishlist = JSON.parse(window.localStorage.getItem(wishlistKey) || '[]');
        if (!Array.isArray(wishlist)) wishlist = [];
    } catch (error) {
        wishlist = [];
    }
    const wishlistLink = document.querySelector('[data-path="wishlist"]');
    if (wishlistLink) wishlistLink.href = '/wishlist';
    const wishlistCount = wishlistLink?.querySelector('span:last-child');
    const updateWishlistCount = () => {
        if (wishlistCount) wishlistCount.textContent = `(${wishlist.length})`;
    };
    const saveWishlist = () => {
        try { window.localStorage.setItem(wishlistKey, JSON.stringify(wishlist)); } catch (error) { /* Storage can be disabled. */ }
        updateWishlistCount();
    };
    document.querySelectorAll('[data-product-slug]').forEach((card) => {
        const wishlistButton = [...card.querySelectorAll('button')]
            .find((button) => button.querySelector('.material-symbols-outlined')?.textContent.trim() === 'favorite');
        if (!wishlistButton) return;

        const slug = card.dataset.productSlug;
        const setWishlistState = (saved) => {
            wishlistButton.setAttribute('aria-label', saved ? 'Remove from wishlist' : 'Add to wishlist');
            wishlistButton.classList.toggle('bg-primary', saved);
            wishlistButton.classList.toggle('text-on-primary', saved);
            wishlistButton.querySelector('.material-symbols-outlined').textContent = saved ? 'favorite' : 'favorite_border';
        };
        setWishlistState(wishlist.includes(slug));
        wishlistButton.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const index = wishlist.indexOf(slug);
            if (index === -1) wishlist.push(slug);
            else wishlist.splice(index, 1);
            setWishlistState(index === -1);
            saveWishlist();
        });
    });
    updateWishlistCount();

    const syncCartSummary = async () => {
        try {
            const response = await fetch('/cart/summary', { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const summary = await response.json();
            const formattedTotal = `₱${Number(summary.total || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.querySelectorAll('header a, header button').forEach((control) => {
                if (!/BAG\s*\(/i.test(control.textContent)) return;
                const amount = [...control.querySelectorAll('span')].find((span) => /₱/.test(span.textContent));
                if (amount) {
                    const label = amount.textContent.trim();
                    amount.textContent = /BAG\s*\(/i.test(label)
                        ? `BAG (${formattedTotal} / ${summary.count || 0})`
                        : `(${formattedTotal} / ${summary.count || 0})`;
                }
                else control.textContent = `BAG (${formattedTotal} / ${summary.count || 0})`;
            });
        } catch (error) {
            // The cart remains usable when the summary request is unavailable.
        }
    };
    syncCartSummary();

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

    const galleryImage = document.getElementById('product-main-image');
    const galleryButtons = document.querySelectorAll('[data-gallery-image]');
    if (galleryImage && galleryButtons.length) {
        galleryImage.dataset.lastGoodSource = galleryImage.currentSrc || galleryImage.src;
        galleryImage.addEventListener('error', () => {
            const fallback = galleryImage.dataset.lastGoodSource;
            if (fallback && galleryImage.src !== fallback) galleryImage.src = fallback;
        });

        galleryButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const thumbnail = button.querySelector('img');
                const source = thumbnail?.currentSrc || thumbnail?.src || button.dataset.galleryImage;
                if (!source) return;

                galleryImage.src = source;
                galleryButtons.forEach((item) => item.classList.remove('ring-2', 'ring-primary'));
                button.classList.add('ring-2', 'ring-primary');
            });
        });
    }

    const addToCart = async (button, data, { redirect = false } = {}) => {
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
            await syncCartSummary();
            if (redirect) {
                window.location.assign(result.redirect || '/cart');
                return;
            }
            button.innerHTML = '<span class="material-symbols-outlined text-[16px]">check</span><span>ADDED TO BAG</span>';
            button.dataset.adding = 'false';
            button.removeAttribute('aria-busy');
            window.setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
            }, 1400);
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

        const quickAdd = [...card.querySelectorAll('button')]
            .find((button) => /(QUICK ADD|ADD TO BAG)/i.test(button.textContent));
        if (quickAdd && /(QUICK ADD|ADD TO BAG)/i.test(quickAdd.textContent)) {
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
            }, { redirect: true });
        });
    }
});
