<template>
	<div class="px-3">
		<div class="relative pt-1">
			<input
				type="range"
				min="0"
				:max="duration"
				:value="currentTime"
				@input="seek"
				class="w-full h-2 rounded-lg cursor-pointer"
			/>
			<div class="progress-bar" :style="{ width: `${progress}%` }"></div>
		</div>

		<div class="flex justify-between text-gray-400 text-sm mt-2">
			<span>{{ formatTime(currentTime) }}</span>
			<span>{{ formatTime(duration) }}</span>
		</div>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { PlayIcon, PauseIcon } from 'lucide-vue-next'

// Audio state
const currentTime = ref(0)
const duration = ref(100) // Set this to the actual duration of your audio

// Track information
const currentTrack = ref('Awesome Track')
const artistName = ref('Amazing Artist')

// Computed property for progress percentage
const progress = computed(() => (currentTime.value / duration.value) * 100)

// Function to format time in MM:SS
const formatTime = (time) => {
	const minutes = Math.floor(time / 60)
	const seconds = Math.floor(time % 60)
	return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
}

// Function to handle seeking
const seek = (event) => {
	currentTime.value = Number(event.target.value)
	// Here you would also update the actual audio playback position
}

// In a real implementation, you would need to add event listeners to your audio element
// to update currentTime and handle the end of the track
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
