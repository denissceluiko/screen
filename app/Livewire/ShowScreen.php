<?php

namespace App\Livewire;

use App\Models\Screen;
use Livewire\Component;

class ShowScreen extends Component
{
    public Screen $screen;
    public array $config;
    public array $slides;
    public string $time;

    public function mount(Screen $screen)
    {
        $this->screen = $screen;
        $this->slides = $this->slides();
        $this->time = date('d.m.Y H:i');
    }

    // public function boot()
    // {
    //     $this->slides = $this->slides();
    //     $this->time = date('d.m.Y H:i');
    // }

    public function render()
    {
        return view('livewire.screen.show')
            ->title($this->screen->name);
    }

    public function slides(): array
    {
        $slides = $this->screen->slideshow?->slides->pluck('path', 'id')->toArray();
        $formatted = [];

        foreach ($slides as $id => &$slide) {
            $formatted[] = [
                'id' => $id,
                'path' => asset('storage/'.$slide),
            ];
        }

        return $formatted;
    }
}
