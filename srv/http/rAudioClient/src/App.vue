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
		var json = JSON.parse(data)
		// if (json?.channel === 'mpdplayer') {
		modifyPlayer(json.data)
		// }
		console.log('getData', json)
		// psOnMessage(json.channel, json.data)
	}
}

ws.addEventListener('open', (event) => {
	console.log('ON OPEN!')
})

ws.addEventListener('close', (event) => {
	console.log('ON CLOSE :((')
})

window.zaz = ws
</script>
<style scoped>
header {
	line-height: 1.5;
	max-height: 100vh;
}

.logo {
	display: block;
	margin: 0 auto 2rem;
}

nav {
	width: 100%;
	font-size: 12px;
	text-align: center;
	margin-top: 2rem;
}

nav a.router-link-exact-active {
	color: var(--color-text);
}

nav a.router-link-exact-active:hover {
	background-color: transparent;
}

nav a {
	display: inline-block;
	padding: 0 1rem;
	border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
	border: 0;
}

@media (min-width: 1024px) {
	header {
		display: flex;
		place-items: center;
		padding-right: calc(var(--section-gap) / 2);
	}

	.logo {
		margin: 0 2rem 0 0;
	}

	header .wrapper {
		display: flex;
		place-items: flex-start;
		flex-wrap: wrap;
	}

	nav {
		text-align: left;
		margin-left: -1rem;
		font-size: 1rem;

		padding: 1rem 0;
		margin-top: 1rem;
	}
}
</style>
