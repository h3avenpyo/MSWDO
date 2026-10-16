// Prevent redeclaration if this script is loaded multiple times
if (!window.NotificationSystem) {
    class NotificationSystem {
        constructor() {
            this.container = document.createElement('div');
            this.container.className = 'notification-container';
            document.body.appendChild(this.container);
        }

        show(message, type = 'info', duration = 5000) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            
            // Add icon based on type
            let icon = 'info-circle';
            switch(type) {
                case 'success':
                    icon = 'check-circle';
                    break;
                case 'error':
                    icon = 'exclamation-circle';
                    break;
                case 'warning':
                    icon = 'exclamation-triangle';
                    break;
            }

            notification.innerHTML = `
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-${icon} notification-icon"></i>
                    <span class="notification-message">${message}</span>
                </div>
                <button class="notification-close">&times;</button>
            `;

            // Add close button functionality
            const closeBtn = notification.querySelector('.notification-close');
            const closeNotification = () => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (this.container.contains(notification)) {
                        this.container.removeChild(notification);
                    }
                }, 300);
            };

            closeBtn.addEventListener('click', closeNotification);

            // Add to container
            this.container.appendChild(notification);

            // Trigger animation
            setTimeout(() => notification.classList.add('show'), 10);

            // Auto-remove after duration
            if (duration > 0) {
                setTimeout(closeNotification, duration);
            }

            return {
                close: closeNotification
            };
        }

        // Helper methods for different notification types
        success(message, duration = 5000) {
            return this.show(message, 'success', duration);
        }

        error(message, duration = 5000) {
            return this.show(message, 'error', duration);
        }

        info(message, duration = 5000) {
            return this.show(message, 'info', duration);
        }

        warning(message, duration = 5000) {
            return this.show(message, 'warning', duration);
        }
    }

    // Create a global notification instance
    window.NotificationSystem = NotificationSystem;
    window.notify = new NotificationSystem();
}
