<x-guest-layout>
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2 min-h-[540px]">

        <!-- Left Side: Welcome Panel (Royal Blue) -->
        <div class="flex flex-col items-center justify-center text-center px-10 py-12 bg-[#2e3192] text-white order-2 md:order-1">
            <h2 class="text-3xl font-extrabold mb-3">Welcome Back!</h2>
            <p class="text-xs text-indigo-100 leading-relaxed max-w-[240px] mb-6">
                To keep connected with us please login with your personal info
            </p>
            <p class="text-xs text-indigo-200 mb-3">Already registered?</p>
            <a href="{{ route('login') }}"
               class="border border-white text-white hover:bg-white hover:text-[#2e3192] text-xs font-bold uppercase tracking-wider px-11 py-2.5 rounded-full transition">
                SIGN IN
            </a>
        </div>
        <!----------------------------------------------------------------------------------------->
        <!-- Right Side: Create Account Form (White) -->
        <div class="flex flex-col justify-center px-8 sm:px-12 py-10 bg-white order-1 md:order-2">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center">Create Account</h2>

            <div class="flex items-center justify-center gap-3 my-4">
                <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-700 select-none">f</span>
                <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-700 select-none">G+</span>
                <span class="w-9 h-9 rounded-full border border-gray-300 flex items-center justify-center text-xs font-bold text-gray-700 select-none">in</span>
            </div>

            <p class="text-xs text-gray-400 text-center mb-5">or use your email for registration</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf

                <!-- Full Name -->
                <div>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Full Name"
                           required
                           autofocus
                           autocomplete="name"
                           class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                </div>

                <!-- Email -->
                <div>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Email"
                           required
                           autocomplete="username"
                           class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                </div>

                <!-- Phone Number -->
                <div>
                    <input id="phone"
                           type="tel"
                           name="phone"
                           value="{{ old('phone') }}"
                           placeholder="Phone Number"
                           required
                           autocomplete="tel"
                           class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1 text-xs" />
                </div>

                <!-- Age & Gender (Dropdown List) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <input id="age"
                               type="number"
                               name="age"
                               value="{{ old('age') }}"
                               placeholder="Age"
                               min="10"
                               max="100"
                               required
                               class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                        <x-input-error :messages="$errors->get('age')" class="mt-1 text-xs" />
                    </div>

                    <div>
                        <select id="gender"
                                name="gender"
                                required
                                class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm {{ old('gender') ? 'text-gray-800' : 'text-gray-400' }} focus:text-gray-800 focus:ring-2 focus:ring-[#2e3192]">
                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-1 text-xs" />
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <input id="password"
                           type="password"
                           name="password"
                           placeholder="Password"
                           required
                           autocomplete="new-password"
                           class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           placeholder="Confirm Password"
                           required
                           autocomplete="new-password"
                           class="w-full bg-slate-100 border-0 rounded-md px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#2e3192]" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                </div>

                <div class="pt-3 flex justify-center">
                    <button type="submit"
                            class="bg-[#2e3192] hover:bg-[#232575] text-white text-xs font-bold uppercase tracking-wider px-12 py-3 rounded-full shadow-md transition">
                        SIGN UP
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-guest-layout>