import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { PLAYER_PAGE } from '@/router/constants/routes.js'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			...PLAYER_PAGE,
			component: HomeView
		}
	]
})

export default router
