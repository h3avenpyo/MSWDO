@props(['title'])

<header class="top-header">
    <div class="header-left">
        <h2>{{ $title }}</h2>
    </div>
    <div class="user-menu">
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
            <span class="user-role">{{ Auth::user()->position }}</span>
        </div>
        {{-- Centralized notifications component so it's rendered consistently in the header --}}
        @include('components.admin-notifications')
    </div>
</header>
