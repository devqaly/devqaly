import type { RouteRecordRaw } from 'vue-router'

export const sessionRoutes: RouteRecordRaw[] = [
  {
    path: '/sessions/:sessionId/assign',
    name: 'assignSessionToLoggedUser',
    component: () => import('@/views/sessions/AssignSessionCurrentlyLoggedUserView.vue'),
    meta: {
      requiresAuth: true
    }
  }
]
