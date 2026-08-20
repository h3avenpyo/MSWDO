{{-- Account deactivation watchdog.
     Polls /admin/check-account-status on an interval. When the backend reports
     the session account has been deactivated, stops polling, shows a modal and
     redirects to the login page. The session is invalidated server-side by the
     poll endpoint / middleware, so this script only handles presentation. --}}
<script>
(function () {
    if (window.__mswdoAccountStatusStarted) return;
    window.__mswdoAccountStatusStarted = true;

    var POLL_MS = 15000;        // check every 15 seconds
    var AUTO_LOGOUT_MS = 10000; // force redirect 10s after the modal appears

    var modalShown = false;
    var loggedOut = false;

    function logoutAndRedirect() {
        if (loggedOut) return;
        loggedOut = true;
        window.location.href = '/admin';
    }

    function showDeactivationModal() {
        if (modalShown) return;
        modalShown = true;

        // SweetAlert2's own backdrop blocks the page underneath (allowOutsideClick:false).
        if (typeof Swal === 'undefined') {
            window.setTimeout(logoutAndRedirect, 300);
            return;
        }

        // Auto-logout fallback even if the user never clicks OK.
        var autoTimer = window.setTimeout(logoutAndRedirect, AUTO_LOGOUT_MS);

        Swal.fire({
            title: 'Account Deactivated',
            text: 'Your account has been deactivated by an administrator. You will be logged out for security reasons.',
            icon: 'error',
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'OK',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' },
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false
        }).then(function () {
            window.clearTimeout(autoTimer);
            logoutAndRedirect();
        });
    }

    function checkStatus() {
        if (modalShown || loggedOut) return;

        var csrf = document.querySelector('meta[name="csrf-token"]');
        fetch('/admin/check-account-status', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf ? csrf.content : ''
            },
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, data: data };
            });
        })
        .then(function (result) {
            if (!result.ok && result.data && result.data.error === 'account_deactivated') {
                showDeactivationModal();
                return;
            }
            if (result.ok && result.data && result.data.deactivated) {
                showDeactivationModal();
            }
        })
        .catch(function () {
            // Transient network error — retry on the next interval.
        });
    }

    function start() {
        checkStatus();
        window.setInterval(checkStatus, POLL_MS);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();

// Show session flash messages
@if(session('account_deactivated'))
    (function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Account Deactivated',
                text: 'Your account has been deactivated by an administrator. You have been logged out.',
                icon: 'error',
                confirmButtonColor: '#DC2626',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' },
                allowOutsideClick: false
            }).then(function() {
                window.location.href = '/admin';
            });
        }
    })();
@endif

@if(session('success'))
    (function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Success',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#16A34A',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        }
    })();
@endif
</script>