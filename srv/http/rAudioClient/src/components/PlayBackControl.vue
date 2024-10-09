<template>
	<div class="px-8 py-6 flex justify-between items-center">
		<button class="text-gray-400 hover:text-white transition-colors">
			<ShuffleIcon class="w-6 h-6" />
		</button>
		<button class="text-white">
			<SkipBackIcon class="w-8 h-8" />
		</button>
		<button
			@click="togglePlayPause"
			class="bg-green-500 hover:bg-green-600 text-white rounded-full p-4 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-300"
		>
			<PlayIcon v-if="!isPlaying" class="w-8 h-8" />
			<PauseIcon v-else class="w-8 h-8" />
		</button>
		<button class="text-white">
			<SkipForwardIcon class="w-8 h-8" />
		</button>
		<button class="text-gray-400 hover:text-white transition-colors">
			<RepeatIcon class="w-6 h-6" />
		</button>
	</div>
</template>

<script setup>
import {
	PauseIcon,
	PlayIcon,
	RepeatIcon,
	ShuffleIcon,
	SkipBackIcon,
	SkipForwardIcon
} from 'lucide-vue-next'
import { ref } from 'vue'
const isPlaying = ref(false)
const togglePlayPause = () => {
	isPlaying.value = !isPlaying.value
}
</script>

<style scoped></style>
