import { defineStore } from 'pinia'

import apiService from '@/apiService/apiService.js'

export const usePlayerStore = defineStore('playerState', {
	state: () => {
		return {
			playerState: {
				"artist": "",
				"title": "",
				"album": "",
				"track": 0,
				"playListLength": 0,
				"volume": 100,
				"volumemax": true,
				"volumemute": 0,
				"repeat": 0,
				"time": 0,
				"currentTime": 0,
				"file": "",
				"cover": "",
				"playbackStatus": ""
			}
		}
	},
	actions: {
		async getPlaybackState() {
			try {
				this.playerState = await apiService.postJson('/status',)
			} catch (e) {
				console.error(e)
			}
		},
		setVolume(value) {
			//websocket send with debonuce
			this.volume = value
		},
		updateVolume() {
			// after ws return lastvalue, write it into store
		},
		setTime(value) {
			//websocket send with debonuce
			this.time = value
		},
		updateTime() {
			// after ws return lastvalue, write it into store
		},
		async changePlaybackStatus(params={}){
			this.playerState = await apiService.postJson('/command',params)

			console.log('ee',this.playerState)
		}
	}
})
