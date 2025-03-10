<template>
	<div class="raudio-app">
		<RouterView />
	</div>
</template>
<script setup>
import { RouterView } from 'vue-router'
import { usePlayerStore } from '@/store/player.js'
import { websocketClient } from '@/WSocketService/index.js'

const playerStore = usePlayerStore()
websocketClient.subscribe('mpdplayer', playerStore.setPlayerState)
websocketClient.subscribe('option', playerStore.setPlayerState)
websocketClient.subscribe('airplay', playerStore.setPlayerState)
</script>

<style scoped>
.raudio-app {
	@apply min-h-[100dvh] overflow-hidden;
	@apply bg-gradient-to-b from-gray-900 to-black text-white flex flex-col;
}
</style>
