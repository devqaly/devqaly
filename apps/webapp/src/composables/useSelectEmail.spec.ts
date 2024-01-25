import { defineComponent, h } from 'vue'
import { describe, it, expect } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'
import { useSelectEmail } from './useSelectEmail'
import InputText from 'primevue/inputtext'
import PrimeVue from 'primevue/config'
import { randomInteger, range } from '@/services/number'

describe('useSelectEmail', () => {
  const testComponent = defineComponent({
    template: `<InputText v-model='email'/>`,
    components: { [InputText.name]: InputText },
    setup() {
      return { ...useSelectEmail() }
    }
  })

  const wrapper = mount(testComponent, {
    global: { plugins: [PrimeVue] }
  })

  it('should contain error message when email is incorrect', async () => {
    const input = wrapper.find('input')

    await input.setValue('qowek')

    await input.trigger('change')

    await flushPromises()

    expect(wrapper.vm.errorMessage).toBeTruthy()
    expect(wrapper.vm.emails.length).toBe(0)
  })

  it('should allow to add a correct email to array of emails', async () => {
    const correctEmail = 'correct-email@devqaly.com'

    const input = wrapper.find('input')

    await input.setValue(correctEmail)

    await input.trigger('change')

    await flushPromises()

    await wrapper.vm.onSubmit()

    await flushPromises()

    expect(wrapper.vm.email).toBeFalsy()
    expect(wrapper.vm.emails.length).toBe(1)
    expect(wrapper.vm.emails[0]).toBe(correctEmail)
  })

  it('should allow to remove email', async () => {
    const emails = range(1, randomInteger(5, 10)).map((r) => `email-${r}@devqaly.com`)

    wrapper.vm.emails = emails

    const emailToBeRemoved = emails[randomInteger(1, emails.length) - 1]

    console.log('----')
    console.log(emailToBeRemoved)
    console.log('----')

    await wrapper.vm.onRemoveEmail(emailToBeRemoved)

    await flushPromises()

    expect(wrapper.vm.emails.length).toBe(emails.length - 1)
    expect(wrapper.vm.emails.find((e) => e === emailToBeRemoved)).toBeUndefined()
  })
})
