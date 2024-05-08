<div x-data="{
    config: null,
    currentIndex: 0,
    timer: null,
    initTimer: function() {
        this.timer = setInterval(this.nextSlide, $wire.updateInterval * 1000);
    },
    nextSlide: function() {
        $data.currentIndex = ($data.currentIndex + 1) % $wire.slides.length;
    },
    update: function() {
        clearInterval(this.timer);
        this.timer = setInterval(this.nextSlide, $wire.updateInterval * 1000);
    }
}" x-init="initTimer()">
    <div wire:poll.{{ $screen->settings['updateInterval'] }}s="update" x-effect="update()">
        <template x-for="slide in $wire.slides" :key="slide.idx">
            <img
                :src="slide.path"
                width="{{ $screen->settings['width'] }}"
                height="{{ $screen->settings['height'] }}"
                x-show="slide.idx == currentIndex" />
        </template>
    </div>
</div>
