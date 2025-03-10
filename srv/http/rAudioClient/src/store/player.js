import { defineStore } from 'pinia'

import apiService from '@/apiService/apiService.js'

export const usePlayerStore = defineStore('playerState', {
	state: () => {
		return {
			playerState: {
				page: false,
				player: '',
				btreceiver: false,
				card: 0,
				control: '',
				counts: {
					album: 0,
					albumartist: 0,
					artist: 0,
					composer: 0,
					conductor: 0,
					date: 0,
					genre: 0,
					latest: 0,
					playlists: 0,
					song: 0,
					webradio: 4
				},
				icon: '',
				librandom: false,
				lyrics: false,
				relays: false,
				relayson: false,
				shareddata: false,
				snapclient: false,
				stoptimer: false,
				updateaddons: false,
				updating_db: false,
				volume: 0,
				volumemax: false,
				volumemute: 0,
				webradio: false,
				display: {
					album: true,
					albumartist: true,
					albumbyartist: false,
					albumyear: false,
					artist: true,
					audiocdplclear: false,
					backonleft: false,
					bars: true,
					barsalways: false,
					buttons: true,
					composer: true,
					composername: false,
					conductor: true,
					conductorname: false,
					count: true,
					cover: true,
					covervu: false,
					date: true,
					fixedcover: true,
					genre: true,
					hidecover: false,
					label: true,
					latest: true,
					multiraudio: false,
					nas: true,
					playbackswitch: true,
					playlists: true,
					plclear: true,
					plsimilar: true,
					progress: false,
					radioelapsed: false,
					sd: true,
					tapaddplay: false,
					tapreplaceplay: false,
					time: true,
					usb: true,
					volume: true,
					vumeter: false,
					webradio: true,
					ap: false,
					apconf: false,
					audiocd: false,
					camilladsp: false,
					color: '',
					dabradio: false,
					equalizer: false,
					loginsetting: false,
					lock: false,
					relays: false,
					screenoff: false,
					snapclient: false,
					volumenone: true
				},
				repeat: false,
				random: false,
				single: false,
				consume: false,
				file: '',
				pllength: 0,
				song: 0,
				state: '',
				timestamp: 0,
				Album: '',
				Artist: '',
				booklet: false,
				Composer: '',
				Conductor: '',
				Time: 0,
				Title: '',
				ext: '',
				coverart: '',
				sampling: '',
				elapsed: 0
			}
		}
	},
	actions: {
		async getPlaybackState() {
			const data = {
				cmd: 'bash',
				filesh: 'status.sh',
				args: ['withdisplay']
			}

			try {
				const newState = await apiService.postFormData('/cmd.php', data)
				this.setPlayerState(newState)
			} catch (e) {
				console.error(e)
			}
		},

		async changePlaybackStatus(action) {
			const data = {
				cmd: 'bash',
				filesh: 'cmd.sh',
				args: ['mpcplayback', `${action}`, 'CMD ACTION', 'withdisplay']
			}

			try {
				await apiService.postFormData('/cmd.php', data)
			} catch (e) {
				console.error(e)
			}
		},

		setPlayerState(newState) {
			Object.entries(newState).forEach(([key, value]) => {
				this.playerState[key] = value
			})
		},

		seek() {
			// { "filesh": [ "cmd.sh", "mpcseek\n86\nplay\nCMD ELAPSED STATE" ] }
			// Perl to JavaScript conversion
		}
	}
})
