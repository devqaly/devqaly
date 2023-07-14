import type { RouteRecordRaw } from 'vue-router'

export const projectRoutes: RouteRecordRaw[] = [
  {
    path: '/projects',
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
        path: '/projects/:projectId/dashboard',
        name: 'projectDashboard',
        component: () => import('@/views/projects/ProjectDashboard.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/projects/:projectId/sessions',
        name: 'projectSessions',
        component: () => import('@/views/projects/ProjectSessionsView.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: '/projects/:projectId/sessions/:sessionId',
        name: 'projectSession',
        component: () => import('@/views/projects/ProjectSessionView.vue'),
        meta: {
          requiresAuth: true
        }
      }
    ]
  },

  {
    path: '/projects/create',
    name: 'projectCreate',
    component: () => import('@/views/projects/CreateProjectView.vue'),
    meta: {
      requiresAuth: true
    }
  }
]
