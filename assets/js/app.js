/**
 * MediCare Clinic - Main JavaScript
 */

const lucideScript = document.createElement('script');
lucideScript.src = 'https://unpkg.com/lucide@latest/dist/umd/lucide.min.js';
lucideScript.onload = () => { if (typeof lucide !== 'undefined') lucide.createIcons(); };
document.head.appendChild(lucideScript);

document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});

function showToast(message, type = 'success', duration = 4000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const colors = {
        success: 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800ss text-emerald-800 dark:text-emerald-200',
        error: 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
        warning: 'bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200',
        info: 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
    };

    const toast = document.createElement('div');
    toast.className = `toast-enter pointer-events-auto flex items-center gap-3 p-4 rounded-xl border shadow-lg backdrop-blur-sm ${colors[type] || colors.info}`;
    toast.innerHTML = `<p class="text-sm font-medium flex-1">${message}</p>
        <button onclick="this.parentElement.remove()" class="opacity-60 hover:opacity-100">&times;</button>`;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.add('toast-exit'); setTimeout(() => toast.remove(), 300); }, duration);
}

function confirmAction(message, callback) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
    modal.innerHTML = `<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold mb-2">Confirm Action</h3>
        <p class="text-sm text-gray-500 mb-4">${message}</p>
        <div class="flex gap-3 justify-end">
            <button class="cancel-btn px-4 py-2 text-sm rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700">Cancel</button>
            <button class="confirm-btn px-4 py-2 text-sm text-white bg-red-600 rounded-xl hover:bg-red-700">Confirm</button>
        </div></div>`;
    modal.querySelector('.cancel-btn').onclick = () => modal.remove();
    modal.querySelector('.confirm-btn').onclick = () => { modal.remove(); callback(); };
    modal.onclick = () => modal.remove();
    document.body.appendChild(modal);
}

function animateCounter(element, target, duration = 1000) {
    const startTime = performance.now();
    function update(currentTime) {
        const progress = Math.min((currentTime - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        element.textContent = Math.floor(target * eased);
        if (progress < 1) requestAnimationFrame(update);
        else element.textContent = target;
    }
    requestAnimationFrame(update);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-counter]').forEach(el => {
        const target = parseInt(el.dataset.counter, 10);
        if (!isNaN(target)) animateCounter(el, target);
    });

    // Render notifications
    renderNotifications();
});

function renderNotifications() {
    const list = document.getElementById('notifications-list');
    const badge = document.getElementById('notification-badge');
    if (!list) return;

    // Update badge
    if (window.pendingNotificationCount > 0) {
        badge.style.display = 'block';
        badge.textContent = window.pendingNotificationCount > 9 ? '9+' : window.pendingNotificationCount;
        badge.style.width = window.pendingNotificationCount > 9 ? '20px' : '12px';
        badge.style.height = '12px';
        badge.style.fontSize = '10px';
        badge.style.display = 'flex';
        badge.style.alignItems = 'center';
        badge.style.justifyContent = 'center';
    } else {
        badge.style.display = 'none';
    }

    if (!window.notifications || window.notifications.length === 0) {
        list.innerHTML = `
            <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="text-sm">No notifications</p>
            </div>
        `;
        return;
    }

    const statusColors = {
        pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
        approved: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
        rejected: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
        completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        cancelled: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
    };

    list.innerHTML = window.notifications.map(n => `
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="calendar" class="w-4 h-4 text-primary-600 dark:text-primary-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">${n.title}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">${n.message}</p>
                    <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full ${statusColors[n.status] || statusColors.pending}">
                        ${n.status.charAt(0).toUpperCase() + n.status.slice(1)}
                    </span>
                </div>
            </div>
        </div>
    `).join('');

    // Reinitialize lucide icons for notifications
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function validateForm(form) {
    let isValid = true;
    form.querySelectorAll('[required]').forEach(field => {
        const errorEl = field.parentElement.querySelector('.field-error');
        if (errorEl) errorEl.remove();
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500');
            const error = document.createElement('p');
            error.className = 'field-error text-xs text-red-500 mt-1';
            error.textContent = 'This field is required.';
            field.parentElement.appendChild(error);
        } else {
            field.classList.remove('border-red-500');
        }
    });
    return isValid;
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview || !input.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = (e) => { preview.src = e.target.result; preview.classList.remove('hidden'); };
    reader.readAsDataURL(input.files[0]);
}

function handleDelete(url, message = 'Are you sure you want to delete this item?') {
    confirmAction(message, () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf) {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'csrf_token'; input.value = csrf.content;
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    });
}

function createChart(canvasId, config) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return null;
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(0, 0, 0, 0.05)';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    if (config.options?.scales) {
        Object.values(config.options.scales).forEach(scale => {
            scale.grid = { ...scale.grid, color: gridColor };
            scale.ticks = { ...scale.ticks, color: textColor };
        });
    }
    return new Chart(canvas, config);
}
