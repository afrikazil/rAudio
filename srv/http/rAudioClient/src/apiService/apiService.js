class HttpService {
	constructor(baseUrl = 'http://raudio.local/api') {
		this.baseUrl = baseUrl
	}

	/**
	 * Send a POST request with form data
	 * @param {string} endpoint - The API endpoint
	 * @param {FormData|Object} data - The form data to send
	 * @param {Object} options - Additional options for the fetch request
	 * @returns {Promise<any>} - The response data
	 */
	async postFormData(endpoint, data, options = {}) {
		const url = this.baseUrl + endpoint

		const defaultOptions = {
			method: 'POST',
			body: data
		}

		// Merge default options with user-provided options
		const fetchOptions = { ...defaultOptions, ...options }

		try {
			const response = await fetch(url, fetchOptions)

			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`)
			}

			// Try to parse the response as JSON, if it fails, return the raw response
			try {
				return await response.json()
			} catch (e) {
				return await response.text()
			}
		} catch (error) {
			console.error('There was a problem with the fetch operation:', error)
			throw error
		}
	}

	/**
	 * Send a POST request with JSON data
	 * @param {string} endpoint - The API endpoint
	 * @param {Object} data - The JSON data to send
	 * @param {Object} options - Additional options for the fetch request
	 * @returns {Promise<any>} - The response data
	 */
	async postJson(endpoint, data, options = {}) {
		const url = this.baseUrl + endpoint

		const defaultOptions = {
			method: 'POST',
			body: JSON.stringify(data),
			headers: {
				'Content-Type': 'application/json'
			}
		}

		// Merge default options with user-provided options
		const fetchOptions = { ...defaultOptions, ...options }

		try {
			const response = await fetch(url, fetchOptions)

			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`)
			}

			return await response.json()
		} catch (error) {
			console.error('There was a problem with the fetch operation:', error)
			throw error
		}
	}
}

const apiService = new HttpService()
export default apiService
