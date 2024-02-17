import type {
  CreateProjectBody,
  GetProjectSessionsParameters,
  GetProjectsParameters,
  ProjectCodec
} from '@/services/api/resources/project/codec'
import { axios } from '@/services/api/axios'
import type { BaseSingleResource } from '@/services/api'
import type { PaginatableRecord } from '@/services/api'
import type { SessionCodec } from '@/services/api/resources/session/codec'

export const createProject = (companyId: string, body: CreateProjectBody) =>
  axios.post<BaseSingleResource<ProjectCodec>>(`/companies/${companyId}/projects`, body)

export const getProjects = (companyId: string, params: GetProjectsParameters = {}) =>
  axios.get(`/companies/${companyId}/projects`, { params })

export const getProject = (projectId: string) =>
  axios.get<BaseSingleResource<ProjectCodec>>(`/projects/${projectId}`)

export const getProjectSessions = (projectId: string, params: GetProjectSessionsParameters = {}) =>
  axios.get<PaginatableRecord<SessionCodec>>(`/projects/${projectId}/sessions`, { params })

export const updateProjectSecurityToken = (projectId: string) =>
  axios.put<BaseSingleResource<ProjectCodec>>(`/projects/${projectId}/securityToken`)

export const deleteProject = (projectId: string) => axios.delete<null>(`/projects/${projectId}`)
