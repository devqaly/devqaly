import type { LoggedUserCodec } from '@/services/api/resources/user/codec'
import type { PaginatableRecord } from '@/services/api'
import type { ProjectCodec } from '@/services/api/resources/project/codec'
import type { LoginBody } from '@/services/api/auth/codec'
import { defineStore } from 'pinia'
import { loginUser, logoutUser } from '@/services/api/auth/actions'
import { axios } from '@/services/api/axios'
import { getLoggedUserInformation } from '@/services/api/resources/user/actions'
import { loggedUserCodecFactory } from '@/services/factories/userFactory'
import { emptyPagination } from '@/services/api'

interface AppStoreState {
  isAuthenticated: boolean
  loggedUser: LoggedUserCodec
  loggedUserProjectsRequest: PaginatableRecord<ProjectCodec>
}

export const TOKEN_KEY = '_token'

export const useAppStore = defineStore('appStore', {
  state: (): AppStoreState => ({
    isAuthenticated: false,
    loggedUser: loggedUserCodecFactory(),
    loggedUserProjectsRequest: emptyPagination()
  }),
  actions: {
    async loginUser(loginBody: LoginBody) {
      const { data } = await loginUser(loginBody)

      axios.defaults.headers.common['Authorization'] = `Bearer ${data.token.plainTextToken}`
      window.localStorage.setItem(TOKEN_KEY, data.token.plainTextToken)
      this.isAuthenticated = true

      await this.getLoggedUserInformation()
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

    async onLoadApp() {
      const token = window.localStorage.getItem(TOKEN_KEY)

      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.isAuthenticated = true
        await this.getLoggedUserInformation()
      }
    }
  }
})
