import type { LoggedUserCodec } from '@/services/api/resources/user/codec'
import type { PaginatableRecord } from '@/services/api'
import type { ProjectCodec } from '@/services/api/resources/project/codec'
import type { LoginBody } from '@/services/api/auth/codec'
import { defineStore } from 'pinia'
import { loginUser, logoutUser } from '@/services/api/auth/actions'
import { axios } from '@/services/api/axios'
import {
  getLoggedUserCompanies,
  getLoggedUserInformation
} from '@/services/api/resources/user/actions'
import { loggedUserCodecFactory } from '@/services/factories/userFactory'
import { emptyPagination } from '@/services/api'
import type { CompanyCodec } from '@/services/api/resources/company/codec'
import type { GetLoggedUserCompaniesParameters } from '@/services/api/resources/user/requests'
import type { AxiosError } from 'axios'
import { isAxiosError } from 'axios'
import { StatusCodes } from 'http-status-codes'

interface AppStoreState {
  isAuthenticated: boolean
  loggedUser: LoggedUserCodec
  loggedUserProjectsRequest: PaginatableRecord<ProjectCodec>
  loggedUserCompanies: PaginatableRecord<CompanyCodec>
  activeCompany: CompanyCodec | null
}

export const TOKEN_KEY = '_token'

export const useAppStore = defineStore('appStore', {
  state: (): AppStoreState => ({
    isAuthenticated: false,
    loggedUser: loggedUserCodecFactory(),
    loggedUserProjectsRequest: emptyPagination(),
    loggedUserCompanies: emptyPagination(),
    activeCompany: null
  }),
  actions: {
    async loginUser(loginBody: LoginBody) {
      const { data } = await loginUser(loginBody)

      axios.defaults.headers.common['Authorization'] = `Bearer ${data.token.plainTextToken}`
      window.localStorage.setItem(TOKEN_KEY, data.token.plainTextToken)
      this.isAuthenticated = true

      await this.getLoggedUserInformation()
      await this.fetchLoggedUserCompanies(this.loggedUser.id, { perPage: 50 })
    },

    async logoutUser() {
      await logoutUser()

      delete axios.defaults.headers.common['Authorization']
      window.localStorage.removeItem(TOKEN_KEY)
      this.isAuthenticated = false
    },

    async getLoggedUserInformation() {
      const { data: loggedUserData } = await getLoggedUserInformation()

      this.loggedUser = loggedUserData.data
    },

    async fetchLoggedUserCompanies(userId: string, params: GetLoggedUserCompaniesParameters = {}) {
      const { data } = await getLoggedUserCompanies(userId, params)

      this.loggedUserCompanies = data
    },

    async onLoadApp() {
      const token = window.localStorage.getItem(TOKEN_KEY)

      try {
        if (token) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
          this.isAuthenticated = true
          await this.getLoggedUserInformation()
          await this.fetchLoggedUserCompanies(this.loggedUser.id, { perPage: 50 })
        }
      } catch (e: Error | unknown | AxiosError) {
        if (isAxiosError(e) && e.response && e.response.status === StatusCodes.UNAUTHORIZED) {
          delete axios.defaults.headers.common['Authorization']
          window.localStorage.removeItem(TOKEN_KEY)
          this.isAuthenticated = false
          await this.$router.push({ name: 'authLogin' })
        }
      }
    }
  }
})
