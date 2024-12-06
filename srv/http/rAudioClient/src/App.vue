<template>
	<RouterView />
</template>
<script setup>
import { RouterView } from 'vue-router'
import { usePlayerStore } from '@/store/player.js'
const playerStore = usePlayerStore()
const ws = new WebSocket('ws://192.168.2.26:8181')

function modifyPlayer(data) {
	Object.entries(data).forEach(([key, value]) => {
		if (key === 'coverart') {
			return updateCoverArt(value)
		}

		playerStore.playerState[key] = value
	})
}

async function updateCoverArt(url) {
	playerStore.playerState.coverart = ''

	setTimeout(() => {
		playerStore.playerState.coverart = url
	}, 1000)
}

ws.onopen = () => {
	var interval = setInterval(() => {
		if (ws.readyState === 1) {
			clearInterval(interval)
			ws.send('{ "client": "add" }');
			ws.send('{ "mpd": "state" }');

			console.log('ready')

		}
	}, 100)
}

ws.onmessage = (message) => {
	var data = message.data
	var json = JSON.parse(data)
	console.log('getData', json)
	if (data === 'pong') {
		// on pageActive - reload if ws not response
		// V.timeoutreload = false
	} else {
		json = JSON.parse(data)
		// if (json?.channel === 'mpdplayer') {
		modifyPlayer(json.data)
		// }
		console.log('getData', json)
		// psOnMessage(json.channel, json.data)
	}
}

ws.addEventListener('open', () => {
	console.log('ON OPEN!')
})

ws.addEventListener('close', () => {
	console.log('ON CLOSE :((')
})

window.zaz = ws
</script>

