import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { h } from 'vue'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'
import DCopyble from '../DCopyble.vue'
import Button from 'primevue/button'
import Ripple from 'primevue/ripple'

describe('DCopyble.vue', () => {
  it('should render code correctly', () => {
    const content = 'This is a test string'

    const wrapper = mount<(typeof DCopyble)['props']>(DCopyble, {
      props: { content },
      global: {
        directives: { ripple: Ripple },
        components: { [Button.name]: Button },
        plugins: [PrimeVue, ToastService, ConfirmationService]
      },
      slots: {
        default: () => [h('div', content)]
      }
    })

    expect(wrapper.text()).toContain(content)
  })

  it('should allow to copy content', () => {
    const content = 'This is a test string'

    const wrapper = mount<(typeof DCopyble)['props']>(DCopyble, {
      props: { content },
      global: {
        directives: { ripple: Ripple },
        components: { [Button.name]: Button },
        plugins: [PrimeVue, ToastService, ConfirmationService]
      },
      slots: {
        default: () => [h('div', content)]
      }
    })

    expect(wrapper.find('[data-vitest=d-copyble__copy]').exists()).toBeTruthy()
    // @TODO: should mock navigator.clipboard.writeText() function and assert it is called
    // wrapper.find('[data-vitest=d-copyble__copy]').trigger('click')
  })
})
