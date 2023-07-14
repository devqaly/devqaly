import { defineStore } from 'pinia'
import type {
  CreatableProject,
  CreateProjectBody,
  GetProjectSessionsParameters,
  ProjectCodec
} from '@/services/api/resources/project/codec'
import {
  createProject,
  getProject,
  getProjects,
  getProjectSessions
} from '@/services/api/resources/project/actions'
import { useAppStore } from '@/stores/app'
import type { PaginatableRecord } from '@/services/api'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import { emptyPagination } from '@/services/api'
import { creatableProjectFactory } from '@/services/factories/projectsFactory'
import type { GetProjectsParameters } from '@/services/api/resources/project/codec'

interface ProjectStoreState {
  activeProject: ProjectCodec | null
  activeProjectSessionsRequest: PaginatableRecord<SessionCodec>
  projectBeingCreated: CreatableProject
  projectsRequest: PaginatableRecord<ProjectCodec>
}

export const useProjectsStore = defineStore('projectsStore', {
  state: (): ProjectStoreState => ({
    activeProject: null,
    activeProjectSessionsRequest: emptyPagination(),
    projectBeingCreated: creatableProjectFactory(),
    projectsRequest: emptyPagination()
  }),
  actions: {
    async createProject(companyId: string, createProjectBody: CreateProjectBody) {
      const appStore = useAppStore()

      const response = await createProject(companyId, createProjectBody)

      this.activeProject = response.data.data

      appStore.loggedUserProjectsRequest.data.push(response.data.data)

      return response
    },
    async getActiveProject(projectId: string) {
      const { data } = await getProject(projectId)

      this.activeProject = data.data
    },
    async getActiveProjectSessions(params: GetProjectSessionsParameters) {
      if (this.activeProject === null) {
        throw new Error('This should only be called when `activeProject` is not null')
      }

      const { data } = await getProjectSessions(this.activeProject.id, params)

      this.activeProjectSessionsRequest.data = data.data
      this.activeProjectSessionsRequest.meta = data.meta
      this.activeProjectSessionsRequest.links = data.links
    },
    async fetchProjects(companyId: string, params: GetProjectsParameters = {}) {
      const { data } = await getProjects(companyId, params)

      this.projectsRequest = data
    }
  }
})
