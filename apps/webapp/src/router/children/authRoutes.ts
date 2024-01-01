import type { RouteRecordRaw } from 'vue-router'

export const authRoutes: RouteRecordRaw[] = [
  {
    path: '/auth/finishRegistration/:token',
    name: 'finishRegistration',
    component: () => import('@/views/auth/FinishRegistrationView.vue'),
    meta: {
      guestOnly: true
    }
  },
  {
    path: '/auth/register',
    name: 'authRegister',
    component: () => import('@/views/auth/RegisterView.vue'),
    meta: {
      guestOnly: true
    }
  },
  {
    path: '/auth/login',
    name: 'authLogin',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: {
      guestOnly: true
    }
  },
  {
    path: '/auth/logout',
    name: 'authLogout',
    component: () => import('@/views/auth/LogoutView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/auth/requestPasswordLink',
    name: 'authRequestPasswordLink',
    component: () => import('@/views/auth/RequestPasswordResetView.vue'),
    meta: {
      guestOnly: true
    }
  },
  {
    path: '/auth/resetPassword/:token',
    name: 'authResetPassword',
    component: () => import('@/views/auth/ResetPasswordView.vue'),
    meta: {
      guestOnly: true
    }
  }
]
