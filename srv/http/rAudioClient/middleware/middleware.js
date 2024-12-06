import express from 'express'
import { createProxyMiddleware } from 'http-proxy-middleware'
import cors from 'cors'

const app = express()
const PORT = process.env.PORT || 3000
const TARGET_URL = 'http://192.168.2.26/api' // Replace with your target server URL

app.use(cors()) //может быть мешает

// Proxy middleware configuration
const proxyOptions = {
	target: TARGET_URL,
	changeOrigin: true,
	pathRewrite: {
		'^/api': '' // Remove /api prefix when forwarding the request
	},
	onProxyRes: (proxyRes, req, res) => {
		console.log(`Proxied request: ${req.method} ${req.url} -> ${proxyRes.statusCode}`)
	}
}

// Apply proxy to all routes starting with /api
app.use('/api', createProxyMiddleware(proxyOptions))

// Start the server
app.listen(PORT, () => {
	console.log(`Proxy server is running on http://localhost:${PORT}`)
})

// Example usage
console.log('To use this proxy, make requests to:')
console.log(`http://localhost:${PORT}/api/your-endpoint`)
console.log('The request will be forwarded to:')
console.log(`${TARGET_URL}/your-endpoint`)
