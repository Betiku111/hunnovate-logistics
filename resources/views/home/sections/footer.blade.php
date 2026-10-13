<footer class="bg-navy text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Main --}}
        <div class="py-14 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 lg:gap-12">

            {{-- Brand --}}
            <div class="col-span-2 lg:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-[10px] bg-brand text-white flex items-center justify-center font-bold text-sm">HL</span>
                    <span class="font-bold text-lg text-white tracking-tight">
                        Hunnovate<span class="text-brand">.</span>
                    </span>
                </a>

                <p class="mt-5 text-sm text-slate-400 leading-relaxed max-w-sm">
                    Fast, reliable, tracked delivery services across Nigeria. Motorcycle, Car, and Truck logistics — built for customers and businesses.
                </p>

                {{-- Social --}}
                <div class="mt-6 flex items-center gap-3">
                    @foreach(['facebook', 'twitter', 'instagram', 'linkedin'] as $social)
                        <a href="#" aria-label="{{ $social }}"
                           class="w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 hover:text-white transition-colors">
                            <x-icon :name="$social" :size="16" />
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Company --}}
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Company</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="#how-it-works" class="hover:text-white transition-colors">About</a></li>
                    <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                    <li><a href="#service-areas" class="hover:text-white transition-colors">Coverage</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Services</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="#services" class="hover:text-white transition-colors">Same-Day Delivery</a></li>
                    <li><a href="#services" class="hover:text-white transition-colors">Package Delivery</a></li>
                    <li><a href="#services" class="hover:text-white transition-colors">Business Logistics</a></li>
                    <li><a href="#services" class="hover:text-white transition-colors">Intercity Delivery</a></li>
                </ul>
            </div>

            {{-- Support --}}
            <div>
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Support</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('track') }}" class="hover:text-white transition-colors">Track Order</a></li>
                    <li><a href="mailto:support@hunnovate.com" class="hover:text-white transition-colors">Contact</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="py-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <p>© {{ date('Y') }} Hunnovate Logistics. All rights reserved.</p>
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
                <a href="#" class="hover:text-white transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>