<?php

namespace App\Livewire;

use App\Models\Screen;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.screen')]
class ShowScreen extends Component
{
    public Screen $screen;
    public int $updateInterval;
    public array $slides;

    public function mount(Screen $screen)
    {
        $this->screen = $screen;
        $this->slides = $this->slides();
        $this->updateInterval = $this->updateInterval();
    }

    public function render()
    {
        return view('livewire.screen.show')
            ->title($this->screen->name);
    }

    public function update()
    {
        $this->slides = $this->slides();
        $this->updateInterval = $this->updateInterval();
    }

    public function slides(): array
    {
        $slides = $this->screen->slideshow?->slides->pluck('path', 'id')->toArray();
        $formatted = [];

        if (empty($slides)) {
            return $formatted;
        }

        $i=0;
        foreach ($slides as $id => &$slide) {
            $formatted[] = [
                'id' => $id,
                'idx' => $i++,
                'path' => asset('storage/'.$slide),
            ];
        }

        return $formatted;
    }

    public function updateInterval(): int
    {
        return $this->screen->slideshow?->settings['switchInterval'] ?? 10;
    }

}
