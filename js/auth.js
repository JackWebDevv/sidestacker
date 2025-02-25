// DOM Elements
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

// Tab Switching
tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const tab = btn.dataset.tab;
        
        // Update button states
        tabBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // Update content visibility
        tabContents.forEach(content => {
            content.classList.add('hidden');
            if (content.id === `${tab}-tab`) {
                content.classList.remove('hidden');
            }
        });
    });
});

// Tab switching functionality
const loginTab = document.getElementById('login-tab');
const registerTab = document.getElementById('register-tab');
const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');

// Check URL parameters for initial tab
const urlParams = new URLSearchParams(window.location.search);
const initialTab = urlParams.get('tab') || 'login';

if (initialTab === 'register') {
    showRegisterForm();
} else {
    showLoginForm();
}

function showLoginForm() {
    loginTab.classList.add('bg-primary', 'text-white');
    loginTab.classList.remove('bg-gray-100', 'text-gray-600');
    registerTab.classList.remove('bg-primary', 'text-white');
    registerTab.classList.add('bg-gray-100', 'text-gray-600');
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
}

function showRegisterForm() {
    registerTab.classList.add('bg-primary', 'text-white');
    registerTab.classList.remove('bg-gray-100', 'text-gray-600');
    loginTab.classList.remove('bg-primary', 'text-white');
    loginTab.classList.add('bg-gray-100', 'text-gray-600');
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
}

loginTab.addEventListener('click', showLoginForm);
registerTab.addEventListener('click', showRegisterForm);

// Form Validation
const loginEmail = document.getElementById('login-email');
const loginPassword = document.getElementById('login-password');
const registerEmail = document.getElementById('register-email');
const registerPassword = document.getElementById('register-password');
const registerConfirmPassword = document.getElementById('register-confirm-password');

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (form === loginForm) {
            // Add your login logic here
        } else if (form === registerForm) {
            if (registerPassword.value !== registerConfirmPassword.value) {
                alert('Passwords do not match');
                return;
            }
            // Add your registration logic here
        }
    });
});

// Social login buttons
document.querySelectorAll('.social-login-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const provider = btn.dataset.provider;
        // Add your social login logic here
        console.log(`Logging in with ${provider}`);
    });
});

// Password Visibility Toggle
document.querySelectorAll('input[type="password"]').forEach(input => {
    const wrapper = document.createElement('div');
    wrapper.className = 'relative';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-gray-500';
    toggleBtn.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
    `;
    
    wrapper.appendChild(toggleBtn);
    
    toggleBtn.addEventListener('click', () => {
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});
