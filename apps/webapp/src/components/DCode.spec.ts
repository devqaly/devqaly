import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import DCode from './DCode.vue'
import { h } from 'vue'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'
// @ts-ignore
import VueHighlightJS from 'vue3-highlightjs'
import mock from 'jest-mock'

const copyFn = mock.fn(() => new Promise<void>((resolve) => resolve()))

Object.defineProperty(navigator, 'clipboard', {
  writable: true,
  value: {
    writeText: copyFn
  }
})

describe('DCode.vue', () => {
  it('should render code correctly', () => {
    const code = '<div> this is a code </div>'

    const wrapper = mount<(typeof DCode)['props']>(DCode, {
      props: { language: 'javascript' },
      global: {
        plugins: [PrimeVue, ToastService, ConfirmationService, VueHighlightJS]
      },
      slots: {
        default: () => [h('div', code)]
      }
    })

    expect(wrapper.text()).toContain(code)
  })

  it('should allow to copy code', async () => {
    const code = '<div> this is a code </div>'

    const wrapper = mount<(typeof DCode)['props']>(DCode, {
      props: { language: 'javascript' },
      global: {
        plugins: [PrimeVue, ToastService, ConfirmationService, VueHighlightJS]
      },
      slots: {
        default: () => [h('div', code)]
      }
    })

    expect(wrapper.find('[data-vitest=d-code__copy]').text()).toContain('Copy')
    await wrapper.find('[data-vitest=d-code__copy]').trigger('click')
    expect(wrapper.find('[data-vitest=d-code__copy]').text()).toContain('Copied')
    expect(copyFn).to.toHaveBeenCalledOnce()
  })
})
