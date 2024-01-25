import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'
// @ts-ignore
import VueHighlightJS from 'vue3-highlightjs'
import InitiateDevqalyScript from './InitiateDevqalyScript.vue'

describe('InitiateDevqalyScript.vue', () => {
  it('should show which render strategy is selected', async () => {
    const projectId = '123456789'

    const wrapper = mount<(typeof InitiateDevqalyScript)['props']>(InitiateDevqalyScript, {
      props: { projectId },
      global: {
        plugins: [PrimeVue, ToastService, ConfirmationService, VueHighlightJS]
      }
    })

    expect(
      wrapper.find('[data-vitest=initiate-devqaly-script__render-strategy--spa]').classes()
    ).toContain('!border-b-blue-500')

    await wrapper
      .find('[data-vitest=initiate-devqaly-script__render-strategy--ssr]')
      .trigger('click')

    expect(
      wrapper.find('[data-vitest=initiate-devqaly-script__render-strategy--spa]').classes()
    ).not.contain('!border-b-blue-500')

    expect(
      wrapper.find('[data-vitest=initiate-devqaly-script__render-strategy--ssr]').classes()
    ).contain('!border-b-blue-500')
  })

  it('should display correct render strategy content', async () => {
    const projectId = '123456789'

    const wrapper = mount<(typeof InitiateDevqalyScript)['props']>(InitiateDevqalyScript, {
      props: { projectId },
      global: {
        plugins: [PrimeVue, ToastService, ConfirmationService, VueHighlightJS]
      }
    })

    expect(wrapper.text()).toContain(projectId)
    // The `useEffect` is being used here to display how to use the application in SPA mode
    expect(wrapper.text()).not.contain('useEffect')
    expect(wrapper.text()).toContain('devqaly.showRecordingButton()')

    await wrapper
      .find('[data-vitest=initiate-devqaly-script__render-strategy--ssr]')
      .trigger('click')

    expect(wrapper.text()).to.contain('useEffect')
  })
})
