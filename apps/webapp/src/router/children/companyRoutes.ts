import type { RouteRecordRaw } from 'vue-router'

export const companyRoutes: RouteRecordRaw[] = [
  {
    path: '/company/:companyId/members',
    name: 'listCompanyMembers',
    component: () => import('@/views/companies/ListCompanyMembers.vue'),
    meta: {
      requiresAuth: true
    }
  }
]
