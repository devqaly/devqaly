import type { RouteRecordRaw } from 'vue-router'

export const onboardingRoutes: RouteRecordRaw[] = [
  {
    path: '/onboarding/company/:companyId/project/:projectId/installing',
    name: 'onboardInstalling',
    component: () => import('@/views/onboarding/OnboardInstallingView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/onboarding/company/:companyId/project/:projectId/createSession',
    name: 'onboardCreateSession',
    component: () => import('@/views/onboarding/OnboardingCreateSessionView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/onboarding/company/:companyId/project/:projectId/inviteTeamMembers',
    name: 'onboardInviteTeamMembers',
    component: () => import('@/views/onboarding/OnboardingInviteTeamMembersView.vue'),
    meta: {
      requiresAuth: true
    }
  }
]
