<template>
	<div class="audio-control">
		<!-- Track Info -->
		<div v-if="playerState.Title" class="player-info">
			<h2 class="text-2xl font-bold mb-2">{{ playerState.Title }}</h2>
			<p class="text-gray-400">{{ playerState.Artist }}</p>
		</div>

		<div class="px-3">
			<div class="progress-bar-container">
				<input
					type="range"
					min="0"
					:max="playerState.Time"
					:value="elapsed"
					@input="onInput"
					@change="onChanged"
					class="progress-range"
				/>
				<div class="progress-bar" :style="{ width: `${progress}%` }"></div>
			</div>

			<div class="flex justify-between text-gray-400 text-sm mt-2">
				<span>{{ formatTime(elapsed) }}</span>
				<span>{{ formatTime(playerState.Time) }}</span>
			</div>
		</div>
		<!-- Playback Controls -->
		<div class="px-8 py-6 flex justify-between items-center">
			<button
				class="playback-option-button"
				:class="{ active: playerStore.playerState.random }"
				@click="playbackOptionClick('random')"
			>
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
			<button
				class="playback-option-button"
				:class="{ active: playerStore.playerState.repeat }"
				@click="playbackOptionClick('repeat')"
			>
				<RepeatIcon class="w-6 h-6" />
			</button>
		</div>
		<!-- Bottom Bar -->
	</div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePlayerStore } from '@/store/player.js'
import { PLAY, PAUSE } from '@/constants.js'
import {
	PauseIcon,
	PlayIcon,
	RepeatIcon,
	ShuffleIcon,
	SkipBackIcon,
	SkipForwardIcon
} from 'lucide-vue-next'
import { websocketClient } from '@/WSocketService/index.js'
import apiService from '@/apiService/apiService.js'

const playerStore = usePlayerStore()
const elapsed = ref(0)
const newElepsed = ref(0)
let timer = null

const playerState = computed(() => playerStore.playerState)

// Computed property for progress percentage
const progress = computed(() => (elapsed.value / playerState.value.Time) * 100)

// Function to format time in MM:SS
const formatTime = (time) => {
	const minutes = Math.floor(time / 60)
	const seconds = Math.floor(time % 60)
	return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
}

// Function to handle seeking
function onInput(event) {
	// newElepsed.value = event.target.value
}

function onChanged(event) {
	const apiRequestData = {
		cmd: 'bash',
		filesh: 'cmd.sh',
		args: ['mpcseek', `${event.target.value}`, 'play', 'CMD ELAPSED STATE']
	}

	const socketMsg = { filesh: [apiRequestData.filesh, apiRequestData.args.join('\n')] }

	if (websocketClient.isReady) {
		websocketClient.sendMessage(socketMsg)
		return
	}
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

async function playbackOptionClick(type) {
	const newStatus = !playerStore.playerState[type]

	const apiRequestData = {
		cmd: 'bash',
		filesh: 'cmd.sh',
		args: ['mpcoption', type, `${newStatus}`, 'CMD OPTION ONOFF']
	}

	const socketMsg = { filesh: [apiRequestData.filesh, apiRequestData.args.join('\n')] }

	if (websocketClient.isReady) {
		websocketClient.sendMessage(socketMsg)
		return
	}

	await apiService.postFormData('/cmd.php', apiRequestData)

	await playerStore.getPlaybackState()
}

watch(
	playerState,
	() => {
		elapsed.value = playerState.value.elapsed
		if (playerState.value.state !== PLAY) {
			stopInterval()
			return
		}

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

input[type='range']::-webkit-slider-thumb,
input[type='range']::-moz-range-thumb {
	@apply relative z-20;
	-webkit-appearance: none;
	@apply w-4 h-4 bg-white rounded-full cursor-pointer border-none;
}

.progress-range {
	@apply w-full rounded-lg cursor-pointer;
	height: 4px;
}

.progress-bar {
	@apply absolute left-0 bg-indigo-500 rounded-lg top-1/2 rounded-r-none;
	height: 4px;
}

.progress-bar-container {
	@apply relative;
}

.player-info {
	@apply px-8 py-4 text-center;
}

.audio-control {
	@apply flex flex-col;
}

.playback-option-button {
	@apply text-gray-400 hover:text-white transition-colors;
	@apply cursor-pointer;
}

.playback-option-button.active {
	@apply text-indigo-500 hover:text-indigo-500;
}
</style>
