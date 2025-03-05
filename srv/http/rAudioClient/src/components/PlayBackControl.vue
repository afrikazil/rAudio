<template>
	<div class="px-8 py-6 flex justify-between items-center">
		<button class="text-gray-400 hover:text-white transition-colors">
			<ShuffleIcon class="w-6 h-6" />
		</button>
		<button class="text-white" @click="changeTrack('prev')">
			<SkipBackIcon class="w-8 h-8" />
		</button>
		<button
			@click="togglePlayPause"
			class="bg-green-500 hover:bg-green-600 text-white rounded-full p-4 focus:outline-none focus:ring-2 focus:ring-green-400 transition-colors duration-300"
		>
			<PauseIcon v-if="playerStore.playerState.state === PLAY" class="w-8 h-8" />
			<PlayIcon v-else class="w-8 h-8" />
		</button>
		<button class="text-white" @click="changeTrack('next')">
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
import { usePlayerStore } from '@/store/player.js'
import { PAUSE, PLAY } from '@/constants.js'
import { websocketClient } from '@/WSocketService/index.js'
import apiService from '@/apiService/apiService.js'

const playerStore = usePlayerStore()

async function togglePlayPause() {
	const newStatus = (playerStore.playerState.state = PLAY ? PAUSE : PLAY)
	const apiRequestData = {
		cmd: 'bash',
		filesh: 'cmd.sh',
		args: ['mpcplayback', `${newStatus}`, 'CMD ACTION', 'withdisplay']
	}

	const socketMsg = { filesh: [apiRequestData.filesh, apiRequestData.args.join('\n')] }

	if (websocketClient.isReady) {
		websocketClient.sendMessage(socketMsg)
		return
	}

	await playerStore.changePlaybackStatus(newStatus)

	if (!websocketClient.isReady) {
		await playerStore.getPlaybackState()
	}
}

//
async function changeTrack(type = 'prev') {
	if (!playerStore.playerState.pllength) {
		return
	}

	let nextSong = type === 'prev' ? playerStore.playerState.song : playerStore.playerState.song + 2

	if (nextSong >= playerStore.playerState.pllength || nextSong < 0) {
		nextSong = 0
	}

	const apiRequestData = {
		cmd: 'bash',
		filesh: 'cmd.sh',
		args: ['mpcskippl', `${nextSong}`, 'play', 'CMD POS ACTION']
	}

	const socketMsg = { filesh: [apiRequestData.filesh, apiRequestData.args.join('\n')] }

	if (websocketClient.isReady) {
		websocketClient.sendMessage(socketMsg)
		console.log('???????', playerStore.playerState)
		// await playerStore.getPlaybackState()
		return
	}

	await apiService.postFormData('/cmd.php', apiRequestData)

	await playerStore.getPlaybackState()
}
</script>

<style scoped></style>
