import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import DCode from '@/components/DCode.vue'
import { h } from 'vue'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'

describe('DCode.vue', () => {
  it('should render code correctly', () => {
    const code = '<div> this is a code </div>'

    const wrapper = mount(DCode, {
      props: { language: 'javascript' },
      global: {
        plugins: [PrimeVue, ToastService, ConfirmationService]
      },
      slots: {
        default: () => [h('div', code)]
      }
    })

    expect(wrapper.text()).toContain(code)
  })
})
