// DOM Elements
const html = document.documentElement;

// Dark mode handling
function initializeDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const storedDarkMode = localStorage.getItem('darkMode');

    // Set initial dark mode
    if (storedDarkMode === 'true' || (storedDarkMode === null && prefersDark)) {
        html.classList.add('dark');
    }

    // Add click handler for dark mode toggle
    darkModeToggle?.addEventListener('click', () => {
        html.classList.toggle('dark');
        localStorage.setItem('darkMode', html.classList.contains('dark'));
    });

    // Watch for system dark mode changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (localStorage.getItem('darkMode') === null) {
            if (e.matches) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initializeDarkMode();
    initializeSidenav();
});

// More menu functionality
const moreMenuElement = document.createElement('div');
moreMenuElement.className = 'fixed bottom-16 right-4 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-2 hidden transform transition-transform duration-200 ease-in-out';
moreMenuElement.innerHTML = `
    <a href="business-plan.html" class="flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Business Plans</span>
    </a>
    <a href="premium.html" class="flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
        <span>Premium Account</span>
    </a>
    <a href="profile.html" class="flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <span>My Profile</span>
    </a>
    <a href="settings.html" class="flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Settings</span>
    </a>
    <button id="darkModeToggle" class="w-full flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
        <span>Dark Mode</span>
        <div class="ml-auto relative inline-flex items-center h-6 rounded-full w-11 bg-gray-200 dark:bg-primary transition-colors">
            <span class="inline-block h-4 w-4 rounded-full bg-white transform transition-transform duration-200 ease-in-out translate-x-1 dark:translate-x-6"></span>
        </div>
    </button>
    <div class="border-t dark:border-gray-700 my-2"></div>
    <a href="help.html" class="flex items-center gap-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Help Center</span>
    </a>
`;

document.body.appendChild(moreMenuElement);

// Toggle more menu
const moreBtn = document.querySelector('.more-btn');
moreBtn?.addEventListener('click', () => {
    moreMenuElement.classList.toggle('hidden');
});

// Close more menu when clicking outside
document.addEventListener('click', (e) => {
    if (moreBtn && !moreBtn.contains(e.target) && !moreMenuElement.contains(e.target)) {
        moreMenuElement.classList.add('hidden');
    }
});

// More Menu Toggle
moreBtn.addEventListener('click', () => {
    moreMenu.classList.add('open');
    document.body.style.overflow = 'hidden';
});

closeMenuBtn.addEventListener('click', () => {
    moreMenu.classList.remove('open');
    document.body.style.overflow = '';
});

// Search Modal Toggle
searchBtn.addEventListener('click', () => {
    searchModal.classList.add('open');
    document.body.style.overflow = 'hidden';
});

searchModal.addEventListener('click', (e) => {
    if (e.target === searchModal) {
        searchModal.classList.remove('open');
        document.body.style.overflow = '';
    }
});

// Navigation active state handling
function setActiveNavButton() {
    const currentPath = window.location.pathname;
    const navButtons = document.querySelectorAll('.nav-btn');
    
    navButtons.forEach(btn => {
        // Remove active class from all buttons
        btn.classList.remove('active', 'text-primary');
        
        // Get the href if it's a link
        const href = btn.getAttribute('href');
        if (href) {
            const buttonPath = href.split('/').pop();
            if (currentPath.endsWith(buttonPath)) {
                btn.classList.add('active', 'text-primary');
            }
        }
    });
}

// Initialize active state
document.addEventListener('DOMContentLoaded', setActiveNavButton);

// Update active state when using browser navigation
window.addEventListener('popstate', setActiveNavButton);

// Offcanvas sidenav handling
function openSidenav() {
    sidenav.classList.remove('-translate-x-full');
    sidenavOverlay.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeSidenav() {
    sidenav.classList.add('-translate-x-full');
    sidenavOverlay.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Event listeners
moreBtn?.addEventListener('click', openSidenav);
sidenavOverlay?.addEventListener('click', closeSidenav);

// Close sidenav when clicking a link
sidenav?.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', closeSidenav);
});

// Close sidenav on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeSidenav();
    }
});

// Format currency for UK pounds
function formatCurrency(amount, currency = '£') {
    return `${currency}${amount.toFixed(2)}`;
}

// Update business plan prices to use UK format
document.addEventListener('DOMContentLoaded', () => {
    const priceElements = document.querySelectorAll('[data-price]');
    priceElements.forEach(el => {
        const price = parseFloat(el.dataset.price);
        if (!isNaN(price)) {
            el.textContent = formatCurrency(price);
        }
    });
});

// Add Lorem Picsum image placeholders
function initPlaceholderImages() {
    document.querySelectorAll('[data-placeholder="image"]').forEach(img => {
        const width = img.dataset.width || 400;
        const height = img.dataset.height || 300;
        const category = img.dataset.category || '';
        img.src = `https://picsum.photos/seed/${category}${width}/${width}/${height}`;
        img.loading = 'lazy';
        if (!img.alt) {
            img.alt = 'Placeholder image';
        }
    });
}

// Initialize placeholder images
initPlaceholderImages();

// Add CSS variables for consistent styling
const styleVars = document.createElement('style');
styleVars.textContent = `
    :root {
        --tw-primary: #0066FF;
        --tw-accent: #00C7FF;
        --tw-gray-600: #4B5563;
        --tw-gray-300: #D1D5DB;
    }
    
    .dark {
        --tw-primary: #0066FF;
        --tw-accent: #00C7FF;
        --tw-gray-600: #9CA3AF;
        --tw-gray-300: #D1D5DB;
    }

    .nav-btn {
        @apply flex flex-col items-center text-gray-600 dark:text-gray-300;
    }

    .nav-btn.active {
        @apply text-primary;
    }
`;
document.head.appendChild(styleVars);

// Navigation Active State
navBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        if (btn.dataset.page) {
            navBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
    });
});

// Search functionality
searchInput.addEventListener('input', (e) => {
    // Add your search logic here
    console.log('Searching for:', e.target.value);
});

// iOS-style touch feedback
document.querySelectorAll('button').forEach(button => {
    button.addEventListener('touchstart', () => {
        button.style.opacity = '0.7';
    });
    
    button.addEventListener('touchend', () => {
        button.style.opacity = '1';
    });
});

// Prevent bounce scroll on iOS
document.body.addEventListener('touchmove', (e) => {
    if (moreMenu.classList.contains('open') || searchModal.classList.contains('open')) {
        e.preventDefault();
    }
}, { passive: false });

// Newsletter popup handling
function showNewsletter() {
    const popup = document.getElementById('newsletter-popup');
    if (popup) {
        popup.classList.remove('hidden');
    }
}

function closeNewsletter() {
    const popup = document.getElementById('newsletter-popup');
    if (popup) {
        popup.classList.add('hidden');
    }
}

// Show newsletter popup after 30 seconds if not shown before
if (!localStorage.getItem('newsletter-shown')) {
    setTimeout(showNewsletter, 30000);
    localStorage.setItem('newsletter-shown', 'true');
}
