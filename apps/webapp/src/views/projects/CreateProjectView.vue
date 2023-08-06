<template>
  <div class="p-5">
    <div class="text-3xl font-medium text-900">Create Project</div>
    <div class="font-medium text-500 mb-3">
      A project allows you to group your sessions in a logical manner and collaborate with
      colleagues
    </div>

    <Form
      :validation-schema="validationSchema"
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
          name="title"
          v-slot="{ field, errorMessage }"
        >
          <label for="title">Title</label>
          <InputText
            data-cy="create-project-view__title"
            v-bind="field"
            id="title"
            type="text"
            autofocus
            placeholder="Project Name"
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
            data-cy="create-project-view__submit"
            class="mt-4"
            :loading="isCreatingProject"
            icon="pi pi-chevron-right"
            icon-pos="right"
            label="Create Project"
            type="submit"
          ></Button>
        </div>
      </div>
    </Form>
  </div>
</template>

<script lang="ts" setup>
import { object, string } from 'yup'
import { getSubmitFn } from '@/services/validations'
import { displayGeneralError } from '@/services/ui'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useProjectsStore } from '@/stores/projects'
import { Field, Form } from 'vee-validate'
import { useAppStore } from '@/stores/app'
import type { WrappedResponse } from '@/services/api/axios'
import { assertsIsCompanyCodec } from '@/services/resources/Company'

const isCreatingProject = ref(false)

const router = useRouter()

const projectStore = useProjectsStore()

const appStore = useAppStore()

const validationSchema = object({
  title: string()
    .required('Title is required')
    .min(2, 'Minimum of 2 characters')
    .max(255, 'Maximum 255 characters')
})

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  assertsIsCompanyCodec(appStore.activeCompany)

  try {
    isCreatingProject.value = true

    const { data } = await projectStore.createProject(appStore.activeCompany.id, values)

    await router.push({ name: 'projectDashboard', params: { projectId: data.data.id } })
  } catch (e) {
    displayGeneralError(e as WrappedResponse, { group: 'bottom-center' })
  } finally {
    isCreatingProject.value = false
  }
})
</script>
