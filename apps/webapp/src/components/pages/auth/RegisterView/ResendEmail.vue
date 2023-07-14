<template>
  <div class="text-left">
    Instructions have been sent to your email

    <Button
      :loading="isResendingEmail"
      label="Resend Email"
      class="mt-2 block"
      type="submit"
      :disabled="isButtonDisabled"
      @click="onResendEmailClick"
    ></Button>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { resendEmailRegisterToken } from '@/services/api/auth/registerToken/actions'
import { displayGeneralError } from '@/services/ui'
import type { WrappedResponse } from '@/services/api/axios'

const isResendingEmail = ref(false)

const props = defineProps<{
  email: string
}>()

const isButtonDisabled = ref(false)

async function onResendEmailClick() {
  try {
    isResendingEmail.value = true
    await resendEmailRegisterToken({ email: props.email })

    isButtonDisabled.value = true

    setTimeout(() => {
      isButtonDisabled.value = false
    }, 60000)
  } catch (e) {
    displayGeneralError(e as WrappedResponse)
  } finally {
    isResendingEmail.value = false
  }
}
</script>
