<template>
	<div class="px-3">
		<div class="relative pt-1">
			<input
				type="range"
				min="0"
				:max="playerState.time"
				:value="elapsed"
				@input="seek"
				class="w-full h-2 rounded-lg cursor-pointer"
			/>
			<div class="progress-bar" :style="{ width: `${progress}%` }"></div>
		</div>

		<div class="flex justify-between text-gray-400 text-sm mt-2">
			<span>{{ formatTime(elapsed) }}</span>
			<span>{{ formatTime(playerState.time) }}</span>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePlayerStore } from '@/store/player.js'

const playerStore = usePlayerStore()
const elapsed = ref(0)
let timer = null

const playerState = computed(() => playerStore.playerState)

// Computed property for progress percentage
const progress = computed(() => (elapsed.value / playerState.value.time) * 100)

// Function to format time in MM:SS
const formatTime = (time) => {
	const minutes = Math.floor(time / 60)
	const seconds = Math.floor(time % 60)
	return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
}

// Function to handle seeking
const seek = (event) => {
	playerStore.setTime(Number(event.target.value))
	// Here you would also update the actual audio playback position
}

function stopInterval() {
	if (timer) {
		clearInterval(timer)
	}
}

function startInterval() {
	stopInterval()
	timer = setInterval(() => {
		elapsed.value++
		if (elapsed.value > playerState.value.Time) {
			stopInterval()
		}
	}, 1000)
}

watch(
	playerState,
	() => {
		elapsed.value = playerState.value.currentTime
		startInterval()
	},
	{ deep: true }
)
</script>

<style scoped>
/* Custom styling for the range input */
input[type='range'] {
	-webkit-appearance: none;
	@apply w-full h-2 bg-gray-700 rounded-lg outline-none;
}

input[type='range']::-webkit-slider-thumb {
	-webkit-appearance: none;
	@apply w-4 h-4 bg-white rounded-full cursor-pointer relative z-20;
}

input[type='range']::-moz-range-thumb {
	@apply w-4 h-4 bg-white rounded-full cursor-pointer border-none  relative z-20;
}

.progress-bar {
	@apply absolute left-0 top-0 h-2 bg-indigo-500 rounded-lg top-1/2 rounded-r-none;
}
</style>
