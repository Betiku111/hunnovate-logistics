<!DOCTYPE html>
<html>
<head>@vite(['resources/css/app.css'])</head>
<body class="p-8 flex gap-4 text-brand">
    <x-icon name="truck" :size="32" />
    <x-icon name="package" :size="32" />
    <x-icon name="bike" :size="32" />
    <x-icon name="car" :size="32" />
    <x-icon name="check-circle" :size="32" />

    <x-button>Primary</x-button>
<x-button variant="outline">Outline</x-button>
<x-button variant="danger" icon="trash-2">Delete</x-button>
<x-button href="/" icon-after="arrow-right">Go Home</x-button>
<x-button loading>Loading…</x-button>
</body>
</html>