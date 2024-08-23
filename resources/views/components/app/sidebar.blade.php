<div class="sidebar">
    <div class="p-4">
        <h2 class="text-xl font-bold text-white mb-4">Dashboard</h2>
        <nav>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('SalesOrders.index') }}" class="{{ request()->routeIs('sales_orders.*') ? 'active' : '' }}">Sales Orders</a>
        </nav>
    </div>
</div>

<!-- Sidebar Toggle Button -->
<button id="sidebar-toggle" class="fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded-md focus:outline-none">
    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24">
        <path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>
