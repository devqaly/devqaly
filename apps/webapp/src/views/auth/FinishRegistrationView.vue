<template>
  <div class="flex flex-column h-screen">
    <div class="flex-shrink-1 flex-grow-0 border-bottom-1 border-200 px-6 py-2">
      <Image
        src="/logo--dark.svg"
        alt="Logo"
        width="250"
      />
    </div>

    <div class="flex flex-grow-1 flex-shrink-0">
      <div
        class="surface-section w-full lg:w-6 p-6 md:p-8"
        style="
          display: grid;
          align-content: center;
          justify-content: center;
          grid-template-columns: minmax(300px, 550px);
        "
      >
        <div class="mb-5">
          <div class="text-900 text-3xl font-medium mb-3">Let's get your started</div>

          <Form
            :initial-values="initialValues"
            :validation-schema="validationSchema"
            @submit="onSubmit"
          >
            <Field
              name="firstName"
              v-slot="{ field, errorMessage }"
            >
              <label for="firstName">First Name</label>
              <InputText
                v-bind="field"
                id="firstName"
                placeholder="First Name"
                type="text"
                :class="{ 'p-invalid': errorMessage, 'w-full': true }"
                aria-describedby="firstName-help"
              />
              <small
                id="firstName-help"
                class="p-error"
                >{{ errorMessage }}</small
              >
            </Field>

            <div class="mt-4">
              <Field
                name="lastName"
                v-slot="{ field, errorMessage }"
              >
                <label for="lastName">Last Name</label>
                <InputText
                  v-bind="field"
                  placeholder="Last Name"
                  id="lastName"
                  type="text"
                  :class="{ 'p-invalid': errorMessage, 'w-full': true }"
                  aria-describedby="lastName-help"
                />
                <small
                  id="lastName-help"
                  class="p-error"
                  >{{ errorMessage }}</small
                >
              </Field>
            </div>

            <div class="mt-4">
              <Field
                name="password"
                v-slot="{ field, errorMessage }"
              >
                <label for="password">Password</label>
                <InputText
                  v-bind="field"
                  placeholder="Password"
                  id="password"
                  type="password"
                  :class="{ 'p-invalid': errorMessage, 'w-full': true }"
                  aria-describedby="password-help"
                />
                <small
                  id="password-help"
                  class="p-error"
                  >{{ errorMessage }}</small
                >
              </Field>
            </div>

            <div class="mt-4">
              <Field
                name="currentPosition"
                v-slot="{ value, errorMessage, handleChange }"
              >
                <div class="field">
                  <label for="position">Current Position</label>
                  <Dropdown
                    :class="{ 'p-invalid': errorMessage, 'w-full': true }"
                    @update:model-value="handleChange"
                    :model-value="value"
                    :options="options"
                    id="position"
                    optionLabel="name"
                    optionValue="value"
                    placeholder="Select your position"
                  />

                  <small
                    id="position-help"
                    class="p-error"
                    >{{ errorMessage }}</small
                  >
                </div>
              </Field>
            </div>

            <Button
              :loading="isCreatingUser"
              label="Create project"
              icon="pi pi-chevron-right"
              iconPos="right"
              class="w-full mt-4"
              type="submit"
            />
          </Form>
        </div>
      </div>
      <div
        class="hidden lg:block w-6 bg-no-repeat bg-cover"
        style="
          background-image: url('https://blocks.primeng.org/assets/images/blocks/signin/signin.jpg');
        "
      ></div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { object, string } from 'yup'
import { Field, Form } from 'vee-validate'
import { getSubmitFn } from '@/services/validations'
import { ref } from 'vue'
import { createTokenName, systemTimezone } from '@/services/resources/UserService'
import { updateRegisterToken } from '@/services/api/auth/registerToken/actions'
import { useRoute, useRouter } from 'vue-router'
import { displayGeneralError } from '@/services/ui'
import { isError } from '@/services/api/axios'
import type { WrappedResponse } from '@/services/api/axios'
import { useToast } from 'primevue/usetoast'
import { HttpStatusCode } from 'axios'
import { useAppStore } from '@/stores/app'

const isCreatingUser = ref(false)

const toast = useToast()

const initialValues = {
  timezone: systemTimezone()
}

const appStore = useAppStore()

const route = useRoute()

const router = useRouter()

const options = [
  { name: 'Developer', value: 'developer' },
  { name: 'QA', value: 'qa' },
  { name: 'Manager', value: 'manager' },
  { name: 'Project Manager', value: 'project-manager' },
  { name: 'Other', value: 'other' }
]

const validationSchema = object({
  firstName: string()
    .required('First name is required')
    .min(2, 'Must have at least 2 letters')
    .max(50, 'Maximum number of 55 characters'),

  lastName: string()
    .required('Last name is required')
    .min(2, 'Must have at least 2 letters')
    .max(50, 'Maximum number of 55 characters'),

  password: string().required('Password is required').min(8, 'Minimum 8 characters for password'),

  currentPosition: string().required('Current position is required')
})

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  try {
    isCreatingUser.value = true

    const { data } = await updateRegisterToken(route.params.token as string, {
      ...values,
      timezone: initialValues.timezone
    })

    await appStore.loginUser({
      email: data.data.user.email as string,
      password: values.password,
      tokenName: createTokenName()
    })

    if (route.query.redirectTo) {
      router.push(route.query.redirectTo as string)
    } else {
      router.push({ name: 'listProjects' })
    }
  } catch (e) {
    if (isError(e as WrappedResponse, HttpStatusCode.NotFound)) {
      toast.add({
        severity: 'error',
        summary: 'Invalid Token',
        detail: 'This token was not found in our servers. Please, try registering again',
        life: 3000,
        group: 'bottom-center'
      })

      return
    }

    displayGeneralError(e as WrappedResponse, { group: 'bottom-center' })
  } finally {
    isCreatingUser.value = false
  }
})
</script>
