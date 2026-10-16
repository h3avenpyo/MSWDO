<div class="flex items-center gap-4">
    @include('admin-tailwind.components.notifications')
    <div class="hidden sm:flex items-center gap-3">
        <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
        <div class="text-right">
            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
            <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
        </div>
    </div>
</div>
