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

    public string $slidesHash = '';

    public function mount(Screen $screen)
    {
        $this->screen = $screen;
        $this->slidesHash = $this->computeSlidesHash();
        $this->slides = $this->slides();
        $this->updateInterval = $this->updateInterval();
        $this->recordSeen();
    }

    public function render()
    {
        return view('livewire.screen.show')
            ->title($this->screen->name);
    }

    public function update()
    {
        $this->recordSeen();

        $hash = $this->computeSlidesHash();

        if ($hash === $this->slidesHash) {
            return;
        }

        $this->slidesHash = $hash;
        $this->slides = $this->slides();
        $this->updateInterval = $this->updateInterval();
    }

    private function computeSlidesHash(): string
    {
        $slideshow = $this->screen->slideShow;

        if (! $slideshow) {
            return md5((string) $this->screen->updated_at->timestamp);
        }

        $parts = $this->screen->updated_at->timestamp.'|'
            .$slideshow->updated_at->timestamp.'|'
            .$slideshow->slides->map(fn ($s) => $s->id.':'.$s->updated_at->timestamp)->join(',');

        return md5($parts);
    }

    public function slides(): array
    {
        $slideshow = $this->screen->slideShow;

        if (! $slideshow) {
            return [];
        }

        return $slideshow->slides
            ->values()
            ->map(fn ($slide, $idx) => [
                'id' => $slide->id,
                'idx' => $idx,
                'path' => route('slide.show', $slide->token),
            ])
            ->all();
    }

    private function recordSeen(): void
    {
        $this->screen->timestamps = false;
        $this->screen->last_seen_at = now();
        $this->screen->save();
        $this->screen->timestamps = true;
    }

    public function updateInterval(): int
    {
        return $this->screen->slideShow?->settings['switchInterval'] ?? 10;
    }
}
