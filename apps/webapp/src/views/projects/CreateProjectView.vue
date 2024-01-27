<template>
  <div class="p-5">
    <div class="text-3xl font-medium">Create Project</div>
    <div class="font-medium text-slate-500 mb-4">
      A project allows you to group your sessions in a logical manner and collaborate with
      colleagues
    </div>

    <Form
      :validation-schema="validationSchema"
      @submit="onSubmit"
    >
      <div class="bg-white p-5 shadow-md rounded-lg">
        <div class="font-medium mb-5">General Information</div>

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

        <div class="flex justify-end">
          <Button
            data-cy="create-project-view__submit"
            class="!mt-4"
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

    await router.push({
      name: 'projectDashboard',
      params: { projectId: data.data.id, companyId: appStore.activeCompany!.id }
    })
  } catch (e) {
    displayGeneralError(e as WrappedResponse, { group: 'bottom-center' })
  } finally {
    isCreatingProject.value = false
  }
})
</script>
