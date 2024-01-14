import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import ToastService from 'primevue/toastservice'
// @ts-ignore
import VueHighlightJS from 'vue3-highlightjs'
import InstallDevqaly from '../InstallDevqaly.vue'

describe('InstallDevqaly.vue', () => {
  it('should show which package manager is selected', async () => {
    const wrapper = mount<(typeof InstallDevqaly)['props']>(InstallDevqaly, {
      global: {
        plugins: [PrimeVue, ToastService, ConfirmationService, VueHighlightJS]
      }
    })

    expect(
      wrapper.find('[data-vitest="install-devqaly__pick-registry--npm"]').classes()
    ).to.contain('!border-b-blue-500')

    expect(
      wrapper.find('[data-vitest="install-devqaly__pick-registry--yarn"]').classes()
    ).to.not.contain('!border-b-blue-500')

    await wrapper.find('[data-vitest="install-devqaly__pick-registry--yarn"]').trigger('click')

    expect(
      wrapper.find('[data-vitest="install-devqaly__pick-registry--npm"]').classes()
    ).to.not.contain('!border-b-blue-500')

    expect(
      wrapper.find('[data-vitest="install-devqaly__pick-registry--yarn"]').classes()
    ).to.contain('!border-b-blue-500')
  })

  it('should display correct code for package manager', async () => {
    const wrapper = mount<(typeof InstallDevqaly)['props']>(InstallDevqaly, {
      global: {
        plugins: [PrimeVue, ToastService, ConfirmationService, VueHighlightJS]
      }
    })

    expect(wrapper.find('[data-vitest="install-devqaly__install-instructions"]').text()).to.contain(
      'npm install @devqaly/browser'
    )

    await wrapper.find('[data-vitest="install-devqaly__pick-registry--yarn"]').trigger('click')

    expect(wrapper.find('[data-vitest="install-devqaly__install-instructions"]').text()).to.contain(
      'yarn add @devqaly/browser'
    )
  })
})
