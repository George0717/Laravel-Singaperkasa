<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <div class="max-w-md mx-auto bg-white p-10 rounded-lg shadow-2xl space-y-6 border-t-8 border-indigo-500">
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:ring-indigo-600 focus:border-indigo-600 rounded-lg shadow-sm transition duration-300 ease-in-out transform hover:scale-105" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:ring-indigo-600 focus:border-indigo-600 rounded-lg shadow-sm transition duration-300 ease-in-out transform hover:scale-105" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between mt-6">
                <x-primary-button class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all duration-300 transform hover:scale-105" id="submit-btn">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </div>
    </form>

    <!-- Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50 hidden">
        <div class="w-16 h-16 border-4 border-t-transparent border-blue-600 rounded-full animate-spin"></div>
    </div>

    <!-- Script to handle loading and form submission -->
    <script>
        // Ambil form dan tombol submit
        const loginForm = document.getElementById('login-form');
        const submitBtn = document.getElementById('submit-btn');
        const loadingSpinner = document.getElementById('loading-spinner');

        // Tambahkan event listener untuk menangani submit form
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();  // Mencegah form disubmit langsung

            // Tampilkan spinner loading
            loadingSpinner.classList.remove('hidden');

            // Disable tombol submit untuk mencegah klik ganda
            submitBtn.disabled = true;

            // Simulasi loading selama 2 detik sebelum melanjutkan proses
            setTimeout(() => {
                loginForm.submit();  // Kirim form setelah 2 detik
            }, 2000);  // Delay 2 detik
        });
    </script>
</x-guest-layout>
