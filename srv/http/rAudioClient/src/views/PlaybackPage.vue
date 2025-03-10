<template>
	<!-- Header -->
	<div class="app-header">
		<ChevronDownIcon class="w-6 h-6" />
		<span v-if="playerState.Title" class="text-sm font-medium flex gap-2">
			<span>{{ playerState.Artist }} - {{ playerState.Title }}</span>
			<span v-if="playerState.sampling">({{ playerState.sampling }})</span>
		</span>
		<ListMusicIcon class="w-6 h-6" />
	</div>

	<!-- Cover Art -->
	<div class="player-cover-art">
		<img
			v-if="playerState.coverart"
			:src="playerState.coverart"
			:key="playerState.Title"
			:alt="playerState.Title"
			class="w-full max-w-xs rounded-lg shadow-lg"
		/>
	</div>

	<AudioControl />

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
</template>

<script setup>
import {
	ChevronDownIcon,
	ListMusicIcon,
	HeartIcon,
	ShareIcon,
	MoreHorizontalIcon
} from 'lucide-vue-next'

import { usePlayerStore } from '@/store/player.js'
import { watch, computed } from 'vue'
import AudioControl from '@/components/AudioControl.vue'

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
.app-header {
	@apply p-4 flex justify-between items-center;
	@apply sticky z-10;
}

.player-cover-art {
	@apply flex-1 flex items-center justify-center;
}
</style>
