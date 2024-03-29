import type { RouteRecordRaw } from 'vue-router'

export const projectRoutes: RouteRecordRaw[] = [
  {
    path: 'company/:companyId/projects',
    name: 'listProjects',
    component: () => import('@/views/projects/ListProjectsView.vue'),
    meta: {
      requiresAuth: true
    }
  },
  {
    path: '/',
    component: () => import('@/layouts/ProjectLayout.vue'),
    children: [
      {
        path: '/company/:companyId/projects/:projectId/dashboard',
        name: 'projectDashboard',
        component: () => import('@/views/projects/ProjectDashboard.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/company/:companyId/projects/:projectId/sessions',
        name: 'projectSessions',
        component: () => import('@/views/projects/ProjectSessionsView.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/company/:companyId/projects/:projectId/sessions/:sessionId',
        name: 'projectSession',
        component: () => import('@/views/projects/ProjectSessionView.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/company/:companyId/projects/:projectId/settings',
        name: 'projectSettings',
        component: () => import('@/views/projects/ProjectSettingsView.vue'),
        meta: {
          requiresAuth: true
        }
      }
    ]
  },

  {
    path: 'company/:companyId/projects/create',
    name: 'projectCreate',
    component: () => import('@/views/projects/CreateProjectView.vue'),
    meta: {
      requiresAuth: true
    }
  }
]
