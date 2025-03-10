import { createRouter, createWebHistory } from 'vue-router'
import PlaybackPage from '../views/PlaybackPage.vue'
import { PLAYER_PAGE } from '@/router/constants/routes.js'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			...PLAYER_PAGE,
			component: PlaybackPage
		}
	]
})

export default router
