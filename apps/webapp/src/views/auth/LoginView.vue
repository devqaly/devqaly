<template>
  <div class="flex flex-column h-screen">
    Testing things

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
          <div class="text-900 text-3xl font-medium mb-3">Login</div>
        </div>
        <Form
          :initial-values="initialValues"
          :validation-schema="validationSchema"
          @submit="onSubmit"
        >
          <Field
            name="email"
            v-slot="{ field, errorMessage }"
          >
            <label for="email">Email Address</label>
            <InputText
              data-cy="login-view__email"
              v-bind="field"
              id="email"
              type="text"
              placeholder="Email address"
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

          <div class="mt-4">
            <Field
              name="password"
              v-slot="{ field, errorMessage }"
            >
              <label for="password">Password</label>
              <InputText
                data-cy="login-view__password"
                v-bind="field"
                id="password"
                type="password"
                placeholder="Password"
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
            data-cy="login-view__submit"
            :loading="isLoggingIn"
            label="Sign in"
            class="w-full mt-4"
            type="submit"
          />

          <div class="mx-auto block text-center mt-2 text-500">
            <!--            <div class="mt-4">-->
            <!--              Don't have an account?-->
            <!--              <router-link-->
            <!--                :to="{ name: 'authRegister' }"-->
            <!--                class="font-medium no-underline text-blue-500 text-right cursor-pointer"-->
            <!--              >-->
            <!--                Create Today!-->
            <!--              </router-link>-->
            <!--            </div>-->

            <div class="mt-4">
              Forgot your password?
              <router-link
                data-cy="login-view__reset-password"
                :to="{ name: 'authRequestPasswordLink' }"
                class="font-medium no-underline text-blue-500 text-right cursor-pointer"
              >
                Reset It Now!
              </router-link>
            </div>
          </div>
        </Form>
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
import { getSubmitFn } from '@/services/validations'
import { createTokenName } from '@/services/resources/UserService'
import { ref } from 'vue'
import { isError } from '@/services/api/axios'
import type { WrappedResponse } from '@/services/api/axios'
import { HttpStatusCode } from 'axios'
import { useToast } from 'primevue/usetoast'
import { displayGeneralError } from '@/services/ui'
import { useAppStore } from '@/stores/app'
import { useRoute, useRouter } from 'vue-router'

const isLoggingIn = ref(false)

const toast = useToast()

const appStore = useAppStore()

const route = useRoute()

const router = useRouter()

const initialValues = {
  tokenName: createTokenName()
}

const validationSchema = object({
  email: string().required('Email is required').email('Must be a valid email'),
  password: string().required('The password is required')
})

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  try {
    isLoggingIn.value = true

    await appStore.loginUser({ ...values, ...initialValues })

    if (route.query.redirectTo) {
      await router.push(route.query.redirectTo as string)
    } else {
      await router.push({ name: 'listProjects' })
    }
  } catch (e) {
    if (isError(e as WrappedResponse, HttpStatusCode.Forbidden)) {
      toast.add({
        severity: 'error',
        summary: 'Invalid Credentials',
        detail: 'Check your email and password, and try again',
        life: 3000,
        group: 'bottom-center'
      })

      return
    }

    displayGeneralError(e as WrappedResponse, { group: 'bottom-center' })
  } finally {
    isLoggingIn.value = false
  }
})
</script>
