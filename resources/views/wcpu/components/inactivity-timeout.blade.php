@auth
<script id="inactivity-settings" type="application/json">{!! json_encode([
    'key' => 'portal-activity-'.auth()->id().'-'.hash('sha256', session()->getId()),
    'lastActivity' => (int) session('last_user_activity_at', now()->timestamp) * 1000,
    'serverNow' => now()->timestamp * 1000,
    'activityUrl' => route('session.activity'), 'statusUrl' => route('session.status'),
    'logoutUrl' => route('logout'), 'loginUrl' => route('login'), 'inactiveLoginUrl' => route('login', ['inactive' => 1]), 'csrf' => csrf_token(),
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script src="{{ asset('js/inactivity-timeout.js') }}"></script>
@endauth
