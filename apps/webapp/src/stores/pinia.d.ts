import 'pinia'
import router from '@/router'

declare module 'pinia' {
  export interface PiniaCustomProperties {
    $router: typeof router
  }
}
