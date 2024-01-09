<template>
  <div>
    <Form
      :validation-schema="validationSchema"
      @submit="onSubmit"
    >
      <Field
        name="email"
        v-slot="{ field, errorMessage }"
      >
        <label for="email">Email</label>
        <InputText
          v-bind="field"
          id="email"
          type="text"
          autofocus
          placeholder="Email address"
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
        :loading="isCreatingToken"
        label="Sign Up"
        class="w-full !mt-4"
        type="submit"
      ></Button>

      <div class="mx-auto block text-center mt-2 text-slate-400">
        <div class="mt-4">
          Already have an account?
          <router-link
            :to="{ name: 'authLogin' }"
            class="font-medium no-underline text-blue-500 text-right cursor-pointer"
          >
            Sign In!
          </router-link>
        </div>
      </div>
    </Form>
  </div>
</template>
<script setup lang="ts">
import { Field, Form } from 'vee-validate'
import { object, string } from 'yup'
import { getSubmitFn } from '@/services/validations'
import { createRegisterToken } from '@/services/api/auth/registerToken/actions'
import { ref } from 'vue'
import type { AxiosError, AxiosResponse } from 'axios'

const validationSchema = object({
  email: string().required('Email is required').email('Invalid email')
})

const isCreatingToken = ref(false)

const emit = defineEmits<{
  createdRegisterToken: [email: string]
}>()

const onSubmit = getSubmitFn(validationSchema, async (values, actions) => {
  try {
    isCreatingToken.value = true
    await createRegisterToken({ email: values.email })
    emit('createdRegisterToken', values.email)
  } catch (e) {
    const response = (e as AxiosError).response as AxiosResponse<{ message?: string }>
    if (response && response.data.message) {
      actions.setFieldError('email', response.data.message)
    }
  } finally {
    isCreatingToken.value = false
  }
})
</script>
