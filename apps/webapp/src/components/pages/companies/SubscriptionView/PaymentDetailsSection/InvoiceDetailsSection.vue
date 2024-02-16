<template>
  <div class="flex justify-between mt-10">
    <h2 class="font-semibold text-xl">Invoice Details</h2>
    <div
      class="text-green-500 hover:underline px-2 py-1 hover:cursor-pointer"
      @click="isDialogOpen = true"
    >
      Edit
    </div>
  </div>
  <div class="mt-2 line-clamp-1 text-gray-500">
    {{
      appStore.activeCompany!.invoiceDetails
        ? appStore.activeCompany!.invoiceDetails
        : 'No Invoice Details'
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
    <h2 class="text-[3.4rem] font-bold text-center text-black">Invoice Details</h2>
    <div class="text-gray-400 text-[1.5rem] mt-1 text-center px-5">
      This detail will be showing in the bottom part of your invoice. This is required in certain
      jurisdictions
    </div>

    <Form
      class="mt-4"
      :initial-values="{ invoiceDetails: appStore.activeCompany!.invoiceDetails }"
      :validation-schema="validationSchema"
      @submit="onSubmit"
    >
      <Field
        name="invoiceDetails"
        v-slot="{ field, errorMessage }"
      >
        <label for="invoiceDetails">Invoice Details</label>
        <Textarea
          v-bind="field"
          id="invoiceDetails"
          type="text"
          autofocus
          :class="{ 'p-invalid': errorMessage, 'w-full': true }"
          aria-describedby="invoiceDetails-help"
        />
        <small
          id="invoiceDetails-help"
          class="p-error"
          >{{ errorMessage }}</small
        >
      </Field>

      <Button
        :loading="isUpdatingInvoiceDetails"
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
import { Field, Form } from 'vee-validate'
import { ref } from 'vue'
import { object, string } from 'yup'
import { getSubmitFn } from '@/services/validations'
import { displayGeneralError } from '@/services/ui'

const appStore = useAppStore()

const isDialogOpen = ref(false)

const validationSchema = object({
  invoiceDetails: string()
    .required('This field is required')
    .max(65532, 'Maximum of 65k characters')
})

const isUpdatingInvoiceDetails = ref(false)

const onSubmit = getSubmitFn(validationSchema, async (values) => {
  try {
    isUpdatingInvoiceDetails.value = true
    await appStore.updateActiveCompanyBillingDetails({ invoiceDetails: values.invoiceDetails })
    isDialogOpen.value = false
  } catch (e) {
    displayGeneralError(e)
  } finally {
    isUpdatingInvoiceDetails.value = false
  }
})
</script>
