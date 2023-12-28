import { createRouter, createWebHistory } from 'vue-router'
import { sessionRoutes } from '@/router/children/sessionRoutes'
import { authRoutes } from '@/router/children/authRoutes'
import { TOKEN_KEY } from '@/stores/app'
import { projectRoutes } from '@/router/children/projectRoutes'
import { companyRoutes } from '@/router/children/companyRoutes'
import { onboardingRoutes } from '@/router/children/onboardingRoutes'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: { name: 'listProjects' }
    },
    {
      path: '/',
      children: authRoutes
    },
    {
      path: '/',
      children: sessionRoutes
    },
    {
      path: '/',
      component: () => import('@/layouts/NavigationLayout.vue'),
      children: projectRoutes
    },
    {
      path: '/',
      component: () => import('@/layouts/NavigationLayout.vue'),
      children: companyRoutes
    },
    {
      path: '/',
      component: () => import('@/layouts/OnboardingLayout.vue'),
      children: onboardingRoutes
    },
    {
      path: '/',
      component: () => import('@/layouts/NavigationLayout.vue'),
      children: [
        {
          path: '/dashboard',
          name: 'dashboard',
          component: () => import('@/views/DashboardView.vue'),
          meta: {
            requiresAuth: true
          }
        }
      ]
    },
    { path: '/:pathMatch(.*)*', component: () => import('@/views/NotFound.vue') }
  ]
})

router.beforeEach((to, from, next) => {
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (localStorage.getItem(TOKEN_KEY) === null) {
      next({
        name: 'authLogin',
        query: { redirectTo: to.fullPath }
      })
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
