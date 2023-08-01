<template>
  <div class="p-5">
    <div class="text-3xl font-medium text-900">Create Company</div>
    <div class="font-medium text-500 mb-3">
      A company allows you to aggregate projects inside an organization
    </div>

    <Form
      :validation-schema="validationSchema"
      autofocus
      @submit="onSubmit"
    >
      <div class="surface-card p-4 shadow-2 border-round">
        <div class="flex w-full relative align-items-center justify-content-start mb-4 px-2">
          <div class="border-top-1 surface-border top-50 left-0 absolute w-full"></div>
          <div class="px-2 z-1 surface-0 flex align-items-center">
            <span class="text-900 font-medium">General Information</span>
          </div>
        </div>

        <Field
          name="name"
          v-slot="{ field, errorMessage }"
        >
          <label for="title">Name</label>
          <InputText
            v-bind="field"
            id="name"
            type="text"
            autofocus
            placeholder="Company Name"
            :class="{ 'p-invalid': errorMessage, 'w-full': true }"
            aria-describedby="title-help"
          />
          <small
            id="title-help"
            class="p-error"
            >{{ errorMessage }}</small
          >
        </Field>

        <div class="flex justify-content-end">
          <Button
            class="mt-4"
            :loading="isCreatingCompany"
            icon="pi pi-chevron-right"
            icon-pos="right"
            label="Create Company"
            type="submit"
          ></Button>
        </div>
      </div>
    </Form>
  </div>
</template>

<script setup lang="ts">
import { object, string } from 'yup'
import { getSubmitFn } from '@/services/validations'
import { Field, Form } from 'vee-validate'
import { ref } from 'vue'
import { useAppStore } from '@/stores/app'
import { createCompany } from '@/services/api/resources/company/actions'
import { displayGeneralError } from '@/services/ui'
import type { WrappedResponse } from '@/services/api/axios'
import { useRouter } from 'vue-router'

const validationSchema = object({
  name: string()
    .required('Name is required')
    .min(1, 'Minimum of 1 characters')
    .max(255, 'Maximum 255 characters')
})

const appStore = useAppStore()

const isCreatingCompany = ref(false)

const router = useRouter()

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  try {
    isCreatingCompany.value = true

    const { data } = await createCompany(values)

    appStore.activeCompany = data.data
    appStore.loggedUserCompanies.data.push(data.data)

    await router.push({ name: 'listProjects' })
  } catch (e) {
    displayGeneralError(e as WrappedResponse)
  } finally {
    isCreatingCompany.value = false
  }
})
</script>
