<div class="sidebar" id="sidebar">
    <div class="p-4">
        <h2 class="text-xl font-bold text-white mb-5">Menu</h2>
        <nav>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('SalesOrders.index') }}" class="{{ request()->routeIs('SalesOrders.index') ? 'active' : '' }}">Sales Orders</a>
            <a href="{{ route('JadwalKirim.index') }}" class="{{ request()->routeIs('JadwalKirim.index') ? 'active' : '' }}">Jadwal Kirim</a>

            <!-- Dropdown Menu for Surat Jalan & Stock Barang -->
            <div class="relative">
                <button id="dropdown-toggle" class="flex items-center w-full text-left hover:bg-gray-700 px-4 py-2 transition-transform duration-300 ease-in-out">
                    <span>Surat Jalan</span>
                    <svg class="ml-auto fill-current text-gray-300" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="dropdown-menu" class="hidden pl-4 transition-all duration-300 ease-in-out">
                    <a href="{{ route('suratJalan.index') }}" class="block py-2 hover:bg-gray-700 transition-colors duration-200">Surat Jalan</a>
                    <a href="{{ route('stock.index') }}" class="block py-2 hover:bg-gray-700 transition-colors duration-200">Stock Barang</a>
                </div>
            </div>
        </nav>
    </div>
</div>

<!-- Sidebar Toggle Button -->
<button id="sidebar-toggle" class="fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded-md focus:outline-none transition-transform duration-300 ease-in-out">
    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24">
        <path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

<!-- Overlay for mobile -->
<div id="overlay" class="overlay fixed inset-0 bg-black opacity-0 pointer-events-none z-30 transition-opacity duration-300"></div>




<style>
/* Sidebar styles */
.sidebar {
    width: 250px;
    background-color: #2d2d2d;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    transition: transform 0.3s ease-in-out;
    transform: translateX(-100%);
}

.sidebar.active {
    transform: translateX(0);
}

/* Active link styles */
nav a.active {
    background-color: #158843;
    color: white;
    border-left: 4px solid #158843;
    padding-left: 20px;
    transition: background-color 0.3s ease;
}

nav a:hover {
    background-color: #3a3a3a;
}

/* Dropdown menu styles */
#dropdown-menu {
    display: none;
}

#dropdown-menu.show {
    display: block;
}

/* Overlay styles */
.overlay {
    background-color: rgba(0, 0, 0, 0.5);
}

.overlay.show {
    opacity: 1;
    pointer-events: auto;
}

/* Back button styles */
#back-button {
    background-color: #158843;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#back-button:hover {
    background-color: #135e43;
}
</style>

<script>
 document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const dropdownToggle = document.getElementById('dropdown-toggle');
    const dropdownMenu = document.getElementById('dropdown-menu');
    const backButton = document.getElementById('back-button');
    const sidebarToggleButton = document.getElementById('sidebar-toggle');

    // Toggle sidebar for mobile view
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
    }

    // Event listener for sidebar toggle button
    sidebarToggleButton.addEventListener('click', function () {
        toggleSidebar();
    });

    // Event listener to toggle dropdown menu
    dropdownToggle.addEventListener('click', function () {
        dropdownMenu.classList.toggle('show');
        backButton.classList.toggle('hidden');
    });

    // Event listener for clicking overlay
    overlay.addEventListener('click', function () {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
        dropdownMenu.classList.remove('show');
        backButton.classList.add('hidden');
    });

    // Event listener for back button
    backButton.addEventListener('click', function () {
        dropdownMenu.classList.remove('show');
        backButton.classList.add('hidden');
    });
});

</script>