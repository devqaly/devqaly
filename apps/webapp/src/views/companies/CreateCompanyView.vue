<template>
  <div class="p-5">
    <div class="text-3xl font-medium">Create Company</div>
    <div class="font-medium text-slate-500 mb-4">
      A company allows you to aggregate projects inside an organization
    </div>

    <Form
      :validation-schema="validationSchema"
      autofocus
      @submit="onSubmit"
    >
      <div class="bg-white shadow-md rounded-lg p-5">
        <div class="font-medium mb-5">General Information</div>

        <Field
          name="name"
          v-slot="{ field, errorMessage }"
        >
          <label for="title">Name</label>
          <InputText
            data-cy="create-company-view__company-name"
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

        <div class="flex justify-end">
          <Button
            data-cy="create-company-view__submit"
            class="!mt-4"
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

    await router.push({ name: 'listProjects', params: { companyId: appStore.activeCompany!.id } })
  } catch (e) {
    displayGeneralError(e as WrappedResponse)
  } finally {
    isCreatingCompany.value = false
  }
})
</script>
