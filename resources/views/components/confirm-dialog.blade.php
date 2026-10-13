@props([
    'name' => 'confirm',
    'title' => 'Are you sure?',
    'description' => 'This action cannot be undone.',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'variant' => 'danger',
])

<x-modal :name="$name" :title="$title" max-width="md">
    <p class="text-sm text-ink-muted">{{ $description }}</p>

    <x-slot:footer>
        <x-button variant="outline" @click="$dispatch('close-modal', '{{ $name }}')">
            {{ $cancelLabel }}
        </x-button>
        {{ $slot }}
    </x-slot:footer>
</x-modal>