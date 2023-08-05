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
          <div class="text-900 text-3xl font-medium mb-3">Reset Password</div>

          <div
            v-if="linkRequested"
            data-cy="request-password-reset__link-requested"
            class="mt-2 text-left"
          >
            If the email is in our database, you will receive an email.
          </div>

          <Form
            v-if="!linkRequested"
            :validation-schema="validationSchema"
            @submit="onSubmit"
          >
            <Field
              name="email"
              v-slot="{ field, errorMessage }"
            >
              <label for="email">Email</label>
              <InputText
                data-cy="request-password-reset__email"
                v-bind="field"
                id="email"
                type="text"
                autofocus
                :class="{ 'p-invalid': errorMessage, 'w-full': true }"
                aria-describedby="email-help"
              />
              <small
                id="email-help"
                class="p-error"
                >{{ errorMessage }}</small
              >
            </Field>

            <Button
              data-cy="request-password-reset__submit"
              :loading="isRequestingPasswordLink"
              label="Request Link"
              class="w-full mt-4"
              type="submit"
            ></Button>

            <div class="mx-auto block text-center mt-2 text-500">
              <div class="mt-4">
                Already Have An Account?
                <router-link
                  :to="{ name: 'authLogin' }"
                  class="font-medium no-underline text-blue-500 text-right cursor-pointer"
                >
                  Sign In!
                </router-link>
              </div>

              <!--              <div class="mt-2">-->
              <!--                Don't have an account?-->
              <!--                <router-link-->
              <!--                  :to="{ name: 'authRegister' }"-->
              <!--                  class="font-medium no-underline text-blue-500 text-right cursor-pointer"-->
              <!--                >-->
              <!--                  Create Today!-->
              <!--                </router-link>-->
              <!--              </div>-->
            </div>
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
<script setup lang="ts">
import { Field, Form } from 'vee-validate'
import { object, string } from 'yup'
import { ref } from 'vue'
import { getSubmitFn } from '@/services/validations'
import { requestPasswordResetLink } from '@/services/api/auth/actions'
import { displayGeneralError } from '@/services/ui'
import type { WrappedResponse } from '@/services/api/axios'

const validationSchema = object({
  email: string().required('Email is required').email('Invalid email')
})

const isRequestingPasswordLink = ref(false)

const linkRequested = ref(false)

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  try {
    isRequestingPasswordLink.value = true

    await requestPasswordResetLink(values)

    linkRequested.value = true
  } catch (e) {
    displayGeneralError(e as WrappedResponse)
  } finally {
    isRequestingPasswordLink.value = false
  }
})
</script>
