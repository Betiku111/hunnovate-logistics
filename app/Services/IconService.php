<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class IconService
{
    /**
     * Resolve a Lucide icon SVG by name.
     * Strips outer <svg> attrs so the caller controls class/size.
     */
    public function svg(string $name, array $attrs = []): string
    {
        $name = $this->normalize($name);
        $path = base_path("node_modules/lucide-static/icons/{$name}.svg");

        if (! file_exists($path)) {
            return $this->fallback($attrs);
        }

        $raw = Cache::rememberForever("icon:{$name}", fn () => file_get_contents($path));

        // Inject our attributes onto <svg ...>
        $attrString = $this->buildAttrs($attrs);

        return preg_replace(
            '/<svg\b([^>]*)>/',
            '<svg$1 '.$attrString.'>',
            $raw,
            1
        );
    }

    protected function normalize(string $name): string
    {
        $name = strtolower(trim($name));
        $name = str_replace([' ', '_'], '-', $name);

        return preg_replace('/[^a-z0-9\-]/', '', $name);
    }

    protected function buildAttrs(array $attrs): string
    {
        $defaults = [
            'xmlns' => 'http://www.w3.org/2000/svg',
            'width' => '20',
            'height' => '20',
            'viewBox' => '0 0 24 24',
            'fill' => 'none',
            'stroke' => 'currentColor',
            'stroke-width' => '2',
            'stroke-linecap' => 'round',
            'stroke-linejoin' => 'round',
        ];

        $merged = array_merge($defaults, $attrs);

        return collect($merged)
            ->map(fn ($v, $k) => $k.'="'.e($v).'"')
            ->implode(' ');
    }

    protected function fallback(array $attrs): string
    {
        $size = $attrs['width'] ?? 20;

        return '<svg xmlns="http://www.w3.org/2000/svg" width="'.e($size).'" height="'.e($size).'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
    }
}
