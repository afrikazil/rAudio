<template>
	<div class="min-h-screen bg-gradient-to-b from-gray-900 to-black text-white flex flex-col">
		<!-- Header -->
		<div class="p-4 flex justify-between items-center">
			<ChevronDownIcon class="w-6 h-6" />
			<span class="text-sm font-medium">Now Playing</span>
			<ListMusicIcon class="w-6 h-6" />
		</div>

		<!-- Cover Art -->
		<div class="flex-1 flex items-center justify-center p-8">
			<img
				v-if="playerState.cover"
				:src="`http://192.168.2.26${playerState.cover}`"
				:key="playerState.title"
				:alt="playerState.title"
				class="w-full max-w-xs rounded-lg shadow-lg"
			/>
		</div>

		<!-- Track Info -->
		<div class="px-8 py-4 text-center" v-if="playerState.title">
			<h2 class="text-2xl font-bold mb-2">{{ playerState.title }}</h2>
			<p class="text-gray-400">{{ playerState.artist }}</p>
		</div>

		<AudioProgressBar />

		<!-- Playback Controls -->
		<PlayBackControl />
		<!-- Bottom Bar -->
		<div class="bg-gray-800 p-4 flex justify-between items-center">
			<button class="text-gray-400 hover:text-white transition-colors">
				<HeartIcon class="w-6 h-6" />
			</button>
			<button class="text-gray-400 hover:text-white transition-colors">
				<ShareIcon class="w-6 h-6" />
			</button>
			<button class="text-gray-400 hover:text-white transition-colors">
				<MoreHorizontalIcon class="w-6 h-6" />
			</button>
		</div>
	</div>
</template>

<script setup>
import {
	ChevronDownIcon,
	ListMusicIcon,
	HeartIcon,
	ShareIcon,
	MoreHorizontalIcon
} from 'lucide-vue-next'
import AudioProgressBar from '@/components/AudioProgressBar.vue'
import PlayBackControl from '@/components/PlayBackControl.vue'
import { usePlayerStore } from '@/store/player.js'
import { watch, computed } from 'vue'

const playerStore = usePlayerStore()

playerStore.getPlaybackState()

const playerState = computed(() => playerStore.playerState)

watch(
	playerStore,
	() => {
		console.log(playerStore.playerState)
	},
	{ deep: true }
)
</script>

<style scoped>
/* Add any additional component-specific styles here */
</style>
