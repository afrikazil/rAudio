export default class WebSocketClient {
	constructor(url = `ws://${location.host}:8080`) {
		this.url = url
		this.channels = new Map()
		this.isReady = false
		this.connect()
	}

	broadcast(channel, data) {
		const callbacks = this.channels.get(channel) ?? []

		callbacks.forEach((cb) => cb(data))
	}

	subscribe(channel, callback) {
		if (typeof callback !== 'function') {
			console.error('Wrong callback type. It must be a function!')
			return
		}

		this.channels.has(channel)
			? this.channels.get(channel).push(callback)
			: this.channels.set(channel, [callback])
	}

	connect() {
		this.socket = new WebSocket(this.url)

		this.socket.onopen = () => {
			console.log('✅ Connected')
			this.socket.send(JSON.stringify({ client: 'add' }))
			this.isReady = true
		}

		this.socket.onmessage = (event) => {
			const data = JSON.parse(event.data)
			console.log('📩 Received message:', data)

			if (!this.channels.has(data.channel)) {
				console.log('не подписанный канал', data.channel)
				console.log(data)
				return
			}

			this.broadcast(data.channel, data.data)
		}

		this.socket.onclose = () => {
			this.isReady = false

			console.warn('⚠️ Connection lost. Reconnect in  2 seconds...')
			setTimeout(() => this.connect(), 2000)
			this.isReady = false
		}

		this.socket.onerror = (error) => {
			console.error('❌ WebSocket Error:', error)
			this.socket.close()
		}
	}

	sendMessage(message) {
		if (this.socket.readyState === WebSocket.OPEN) {
			this.socket.send(JSON.stringify(message))
		} else {
			console.warn('⚠️ Connection not ready yet')
		}
	}
}
