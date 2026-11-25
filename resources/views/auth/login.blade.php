<x-guest-layout>
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2 min-h-[500px]">

        <!-- Left Side: Sign In Form (White) -->
        <div class="flex flex-col justify-between px-8 sm:px-12 py-10 bg-white">
            <div class="my-auto">
                <h2 class="text-3xl font-extrabold text-gray-900 text-center">Sign in</h2>

                <!-- Decorative Social Icons matching the design -->
                <div class="flex items-center justify-center gap-3 my-4">
                    <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-700 select-none">f</span>
                    <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-700 select-none">G+</span>
                    <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-700 select-none">in</span>
                </div>

                <p class="text-xs text-gray-400 text-center mb-5">or use your account</p>

                <x-auth-session-status class="mb-4 text-xs text-green-600 text-center" :status="session('status')" />
                <!-- form for login -->
                <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-3.5">
                    @csrf

                    <div>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Email"
                               required
                               autofocus
                               autocomplete="username"
                               class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                    </div>

                    <div class="relative">
                        <input id="login_password"
                               type="password"
                               name="password"
                               placeholder="Password"
                               required
                               autocomplete="current-password"
                               class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 pr-10 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                        <button type="button"
                                onclick="const p = document.getElementById('login_password'); p.type = p.type === 'password' ? 'text' : 'password';"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700"
                                title="Show/Hide Password">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center pt-1">
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-rose-400 hover:text-rose-500">
                                Forgot your password?
                            </a>
                        </div>
                    @endif

                    <div class="pt-2 flex justify-center">
                        <button type="submit"
                                class="bg-[#2e3192] hover:bg-[#232575] text-white text-xs font-bold uppercase tracking-wider px-12 py-3 rounded-full shadow-md transition">
                            SIGN IN
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bottom Left: Remember Me -->
            <div class="pt-6">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me"
                           type="checkbox"
                           form="loginForm"
                           name="remember"
                           class="rounded border-gray-300 text-[#2e3192] shadow-sm focus:ring-[#2e3192]">
                    <span class="ms-2 text-xs text-gray-600">Remember me</span>
                </label>
            </div>
        </div>
        <!----------------------------------------------------------------------------------------->
        <!-- Right Side: Welcome Panel (Royal Blue) -->
        <div class="flex flex-col items-center justify-center text-center px-10 py-12 bg-[#2e3192] text-white">
            <h2 class="text-3xl font-extrabold mb-3">Welcome Back!</h2>
            <p class="text-xs text-indigo-100 leading-relaxed max-w-[240px] mb-6">
                To keep connected with us please login with your personal info
            </p>
            <p class="text-xs text-indigo-200 mb-3">No account yet?</p>
            <a href="{{ route('register') }}"
               class="border border-white text-white hover:bg-white hover:text-[#2e3192] text-xs font-bold uppercase tracking-wider px-11 py-2.5 rounded-full transition">
                SIGN UP
            </a>
        </div>

    </div>
</x-guest-layout>