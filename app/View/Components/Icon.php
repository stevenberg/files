<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    public function __construct(
        public string $name,
        public ?string $size = null,
        public ?string $color = null,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function additionalAttributes(): array
    {
        $class = collect(['svg-inline--fa', 'fa-fw']);

        if (! is_null($this->size)) {
            $class->push("fa-{$this->size}");
        }

        if (! is_null($this->color)) {
            $class->push("color-{$this->color}");
        }

        return [
            'class' => $class->join(' '),
            'fill' => 'currentColor',
            'aria-hidden' => 'true',
        ];
    }

    public function url(): string
    {
        // In development, the SVG sprite is embedded in the HTML.
        // It can't be loaded through the Vite development server because
        // cross-domain loading is not allowed with the SVG use element.
        if (is_file(public_path('/hot'))) {
            return "#{$this->name}";
        }

        // In production, the SVG sprite is bundled into the public build directory
        // by Vite.
        $manifest = cache()->remember('asset_manifest', 60, function () {
            $json = (string) file_get_contents(public_path('build/manifest.json'));

            return json_decode($json, true);
        });
        $file = $manifest['node_modules/@fortawesome/fontawesome-pro/sprites/duotone.svg']['file'];

        return asset("/build/{$file}#{$this->name}");
    }

    public function render(): View
    {
        return view('components.icon');
    }
}
