import { defineStore } from 'pinia'

import apiService from '@/apiService/apiService.js'

export const usePlayerStore = defineStore('playerState', {
	state: () => {
		return {
			playerState: {
				time: 0,
				Album: '',
				Artist: '',
				Composer: '',
				Conductor: '',
				Time: 234,
				Title: '',
				booklet: false,
				btreceiver: false,
				card: 0,
				consume: false,
				control: '',
				counts: {
					album: 14,
					albumartist: 9,
					artist: 10,
					composer: 0,
					conductor: 0,
					dabradio: 0,
					date: 14,
					genre: 7,
					latest: 0,
					playlists: 0,
					song: 290,
					webradio: 4
				},
				coverart: '',
				display: {
					album: false,
					albumartist: false,
					albumbyartist: false,
					albumyear: false,
					artist: false,
					audiocdplclear: false,
					backonleft: false,
					bars: false,
					barsalways: false,
					buttons: false,
					composer: false,
					composername: false,
					conductor: false,
					conductorname: false,
					count: false,
					cover: false,
					covervu: false,
					date: false,
					fixedcover: false,
					genre: false,
					hidecover: false,
					label: false,
					latest: false,
					multiraudio: false,
					nas: false,
					playbackswitch: false,
					playlists: false,
					plclear: false,
					plsimilar: false,
					progress: false,
					radioelapsed: false,
					sd: false,
					tapaddplay: false,
					tapreplaceplay: false,
					time: false,
					usb: false,
					volume: false,
					vumeter: false,
					webradio: false,
					ap: false,
					apconf: false,
					audiocd: false,
					camilladsp: false,
					color: '',
					dabradio: false,
					equalizer: false,
					loginsetting: false,
					logout: false,
					relays: false,
					screenoff: false,
					snapclient: false,
					volumenone: false
				},
				elapsed: 0,
				ext: '',
				file: '',
				icon: '',
				librandom: false,
				lyrics: false,
				page: false,
				player: '',
				pllength: 0,
				random: false,
				relays: false,
				relayson: false,
				repeat: false,
				sampling: '',
				shareddata: false,
				single: false,
				snapclient: false,
				song: 0,
				state: '',
				stoptimer: false,
				timestamp: 1728662173505,
				updateaddons: false,
				updating_db: false,
				updatingdab: false,
				volume: 0,
				volumemax: false,
				volumemute: 0,
				webradio: false
			}
		}
	},
	actions: {
		async getPlaybackState() {
			try {
				const formData = new FormData()
				formData.append('cmd', 'bash')
				formData.append('filesh', 'status.sh')
				formData.append('args[]', 'withdisplay')
				this.playerState = await apiService.postFormData('/cmd.php', formData)
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
		}
	}
})
