<div wire:poll.5s>
    <div x-data="{ slides: $wire.slides }">
        <template x-for="slide in slides" :key="slide.id">
            <img :src="slide.path" width="300px" />
        </template>
        <span x-text="time">q</span>
    </div>
</div>
