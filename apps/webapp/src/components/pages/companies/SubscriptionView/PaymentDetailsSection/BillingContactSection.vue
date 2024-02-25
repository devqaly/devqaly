<template>
  <div class="flex justify-between mt-10">
    <h2 class="font-semibold text-xl">Billing Contact</h2>
    <div
      data-cy="company-subscription-view__open-billing-contact-dialog"
      class="text-green-500 hover:underline hover:cursor-pointer px-2 py-1"
      @click="isDialogOpen = true"
    >
      Edit
    </div>
  </div>
  <div
    class="mt-2 line-clamp-1 text-gray-500"
    data-cy="company-subscription-view__billing-contact"
  >
    {{
      appStore.activeCompany?.billingContact
        ? appStore.activeCompany?.billingContact
        : 'No Billing Contact'
    }}
  </div>

  <Dialog
    v-model:visible="isDialogOpen"
    modal
    :show-header="false"
    :draggable="false"
    :style="{ maxWidth: '580px' }"
    :pt="{ content: { class: 'rounded-lg !p-5' } }"
  >
    <h2 class="text-[3.4rem] font-bold text-center text-black">Billing contact</h2>
    <div class="text-gray-400 text-[1.5rem] mt-1 text-center px-5">
      This email address will be used to deliver invoices, receipts, and notifications about any
      payment problems.
    </div>

    <Form
      class="mt-4"
      :validation-schema="validationSchema"
      @submit="onSubmit"
    >
      <Field
        name="email"
        v-slot="{ field, errorMessage }"
      >
        <label for="email">Email</label>
        <InputText
          data-cy="company-subscription-view__billing-contact-input"
          v-bind="field"
          id="email"
          type="text"
          autofocus
          :placeholder="appStore.activeCompany!.billingContact ?? 'Billing Email Contact'"
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
        data-cy="company-subscription-view__billing-contact-submit"
        :loading="isUpdatingBillingContact"
        label="Update"
        class="w-full !mt-4"
        type="submit"
      />

      <Button
        label="Cancel"
        text
        class="w-full !mt-4 !text-gray-500"
        type="submit"
        @click="isDialogOpen = false"
      />
    </Form>
  </Dialog>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { ref } from 'vue'
import { Field, Form } from 'vee-validate'
import { object, string } from 'yup'
import { getSubmitFn } from '@/services/validations'
import { displayGeneralError } from '@/services/ui'
import type { WrappedResponse } from '@/services/api/axios'

const appStore = useAppStore()

const isDialogOpen = ref(false)

const isUpdatingBillingContact = ref(false)

const validationSchema = object({
  email: string().required('Email is required').email('Invalid email')
})

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  try {
    isUpdatingBillingContact.value = true
    await appStore.updateActiveCompanyBillingDetails({ billingContact: values.email })
    isDialogOpen.value = false
  } catch (e) {
    displayGeneralError(e as WrappedResponse)
  } finally {
    isUpdatingBillingContact.value = false
  }
})
</script>
