@php
    $faqs = [
        ['q' => 'How do I create an order?',
         'a' => 'Create a free account, then use the Create Order flow to enter pickup and delivery details, choose a vehicle type, review the price, and confirm. You will be matched with a verified rider immediately.'],

        ['q' => 'How can I track my package?',
         'a' => 'Every order gets a unique tracking number like HNL-2026-000245. Enter it on the Track Order page to see live rider location, status updates, and estimated arrival. You will also get SMS and email notifications at every milestone.'],

        ['q' => 'How much does delivery cost?',
         'a' => 'Pricing is calculated upfront based on distance, package weight, vehicle type, and priority. There are no hidden charges. Express options may add a small surcharge, which is displayed before you confirm.'],

        ['q' => 'Can I choose a rider?',
         'a' => 'Yes. When creating an order, you can pick a recommended rider, choose a specific rider from available options, or let our system assign any available rider in your area. All riders are verified.'],

        ['q' => 'What happens if my package is delayed?',
         'a' => 'You will be notified immediately if there is any delay. Our support team actively monitors all active deliveries and will work to resolve issues quickly. Delayed deliveries may be eligible for a partial refund.'],

        ['q' => 'How do I contact support?',
         'a' => 'Use the in-app Support section to open a ticket, or email us directly at support@hunnovate.com. We respond to all inquiries within a few hours during business hours.'],
    ];
@endphp

<section id="faq" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <x-section-title
            align="center"
            title="Frequently asked questions"
            subtitle="Everything you need to know before your first delivery."
        />

        <div class="mt-12 space-y-3" x-data="{ open: null }">
            @foreach($faqs as $i => $faq)
                <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] overflow-hidden">
                    <button
                        type="button"
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left hover:bg-slate-50 transition-colors"
                        :aria-expanded="open === {{ $i }}"
                    >
                        <span class="text-sm sm:text-base font-semibold text-ink">{{ $faq['q'] }}</span>
                        <span
                            class="shrink-0 w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-ink-muted transition-transform"
                            :class="open === {{ $i }} ? 'rotate-180' : ''"
                        >
                            <x-icon name="chevron-down" :size="16" />
                        </span>
                    </button>

                    <div
                        x-show="open === {{ $i }}"
                        x-collapse
                        class="px-5 pb-5"
                    >
                        <p class="text-sm text-ink-muted leading-relaxed pt-1">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-10 text-center text-sm text-ink-muted">
            Still have questions?
            <a href="mailto:support@hunnovate.com" class="font-medium text-brand hover:text-brand-hover">Contact our team</a>
        </p>
    </div>
</section>