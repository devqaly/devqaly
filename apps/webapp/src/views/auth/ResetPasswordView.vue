<template>
  <div class="flex flex-col h-screen">
    <div class="shrink grow-0 border-b border-gray-300 px-6 py-2">
      <Image
        src="/logo--dark.svg"
        alt="Logo"
        width="120"
      />
    </div>

    <div class="flex grow shrink-0">
      <div
        class="w-full lg:w-1/2 p-6 md:p-8"
        style="
          display: grid;
          align-content: center;
          justify-content: center;
          grid-template-columns: minmax(300px, 550px);
        "
      >
        <div class="mb-5">
          <div class="text-3xl font-medium mb-3">Reset Password</div>

          <Form
            :initial-values="initialValues"
            :validation-schema="validationSchema"
            @submit="onSubmit"
          >
            <Field
              name="email"
              v-slot="{ field, errorMessage }"
            >
              <label for="email">Email</label>
              <InputText
                data-cy="reset-password-view__email"
                v-bind="field"
                id="email"
                type="text"
                readonly
                aria-readonly
                :class="{ 'p-invalid': errorMessage, 'w-full': true }"
                aria-describedby="email-help"
              />
              <small
                id="email-help"
                class="p-error"
                >{{ errorMessage }}</small
              >
            </Field>

            <div class="mt-4">
              <Field
                name="password"
                v-slot="{ field, errorMessage }"
              >
                <label for="password">New Password</label>
                <InputText
                  data-cy="reset-password-view__new-password"
                  v-bind="field"
                  id="password"
                  type="password"
                  autofocus
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

            <Button
              data-cy="reset-password-view__submit"
              :loading="isResettingPassword"
              label="Reset Password"
              class="w-full !mt-4"
              type="submit"
            />
          </Form>
        </div>
      </div>
      <div
        class="hidden lg:block w-1/2 bg-no-repeat bg-cover"
        style="
          background-image: url('https://blocks.primeng.org/assets/images/blocks/signin/signin.jpg');
        "
      ></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Field, Form } from 'vee-validate'
import { ref } from 'vue'
import { object, string } from 'yup'
import { getSubmitFn } from '@/services/validations'
import { useRoute, useRouter } from 'vue-router'
import { displayGeneralError } from '@/services/ui'
import { resetPassword } from '@/services/api/auth/actions'
import { isError } from '@/services/api/axios'
import type { WrappedResponse } from '@/services/api/axios'
import { HttpStatusCode } from 'axios'
import { useToast } from 'primevue/usetoast'

const isResettingPassword = ref(false)

const route = useRoute()

const router = useRouter()

const toast = useToast()

const initialValues = {
  email: route.query.email as string,
  token: route.params.token as string
}

const validationSchema = object({
  email: string().required('Email is required').email('Invalid email'),
  token: string().required('Token is required'),
  password: string().required('Password is required').min(8, 'Must have at least 8 characters')
})

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  try {
    isResettingPassword.value = true

    await resetPassword(values)

    await router.push({ name: 'authLogin' })
  } catch (e) {
    if (isError(e as WrappedResponse, HttpStatusCode.BadRequest)) {
      toast.add({
        severity: 'error',
        summary: 'Invalid Token',
        detail: 'Token might have expired. Request a new password link',
        life: 3000,
        group: 'bottom-center'
      })

      return
    }

    displayGeneralError(e as WrappedResponse, { group: 'bottom-center' })
  } finally {
    isResettingPassword.value = false
  }
})
</script>
