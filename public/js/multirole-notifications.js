document.addEventListener('DOMContentLoaded', () => {
    const showUnavailableNotification = () => {
        const message = 'This notification is no longer available.';
        if (window.Swal) {
            window.Swal.fire({ icon: 'info', text: message, confirmButtonColor: '#C41E3A' });
        } else {
            window.alert(message);
        }
    };
    const destinationIsAvailable = async href => {
        try {
            const response = await fetch(href, {
                method: 'HEAD',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            return response.ok;
        } catch (_) {
            return false;
        }
    };
    document.querySelectorAll('[data-multirole-notifications]').forEach(root => {
        const items = [...root.querySelectorAll('.desk-notification-item')];
        const updateBadge = () => {
            const count = items.filter(item => item.dataset.unread === 'true').length;
            const badge = root.querySelector('[data-unread-badge]');
            if (badge) {
                badge.style.display = count ? '' : 'none';
                badge.textContent = count > 9 ? '9+' : String(count);
            }
        };
        const clearItem = item => {
            item.dataset.unread = 'false';
            item.classList.remove('bg-purple-50/50', 'border-l-4', 'border-purple-500');
            item.querySelectorAll('.bg-purple-500.rounded-full').forEach(dot => {
                dot.style.display = 'none';
            });
        };
        const markRead = async notificationId => {
            const response = await fetch(root.dataset.markReadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(notificationId
                    ? { notification_id: notificationId }
                    : { notification_ids: items.map(item => item.dataset.notificationId) }),
            });
            if (!response.ok || !(await response.json()).success) {
                throw new Error('Could not mark notification as read.');
            }
        };
        items.forEach(item => item.addEventListener('click', async event => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            const href = item.getAttribute('href');
            try {
                if (item.dataset.unread === 'true') {
                    await markRead(item.dataset.notificationId);
                    clearItem(item);
                    updateBadge();
                }
            } catch (error) {
                console.error(error);
            }
            if (href && href !== '#' && !href.startsWith('javascript:')) {
                if (await destinationIsAvailable(href)) {
                    window.location.assign(href);
                } else {
                    showUnavailableNotification();
                }
            }
        }));
        root.querySelector('#deskMarkAllReadBtn')?.addEventListener('click', async event => {
            event.preventDefault();
            event.stopPropagation();
            const button = event.currentTarget;
            button.disabled = true;
            try {
                await markRead();
                items.forEach(clearItem);
                updateBadge();
            } catch (error) {
                console.error(error);
            } finally {
                button.disabled = false;
            }
        });
    });
});
