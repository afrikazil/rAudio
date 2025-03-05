<template>
	<RouterView />
</template>
<script setup>
import { RouterView } from 'vue-router'
import { usePlayerStore } from '@/store/player.js'
import { websocketClient } from '@/WSocketService/index.js'

const playerStore = usePlayerStore()
websocketClient.subscribe('mpdplayer', playerStore.setPlayerState)
websocketClient.subscribe('airplay', playerStore.setPlayerState)
</script>
