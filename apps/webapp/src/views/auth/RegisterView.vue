<template>
  <div class="flex flex-col h-screen">
    <div class="shrink grow-0 border-b border-gray-300 px-6 py-2">
      <Image
        src="/logo--dark.svg"
        alt="Logo"
        width="120"
      />
    </div>

    <div class="flex grow shrink-0">
      <div
        class="w-full lg:w-1/2 p-6 md:p-8"
        style="
          display: grid;
          align-content: center;
          justify-content: center;
          grid-template-columns: minmax(300px, 550px);
        "
      >
        <div class="mb-5">
          <div class="text-3xl font-medium mb-3">Sign Up</div>

          <ChooseEmail
            v-if="!requestedToken"
            @createdRegisterToken="onCreatedRegisterToken"
          />

          <ResendEmail
            data-cy="register-view__resend-email-container"
            :email="email"
            v-if="requestedToken"
          />
        </div>
      </div>
      <div
        class="hidden lg:block w-1/2 bg-no-repeat bg-cover"
        style="
          background-image: url('https://blocks.primeng.org/assets/images/blocks/signin/signin.jpg');
        "
      ></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ChooseEmail from '@/components/pages/auth/RegisterView/ChooseEmail.vue'
import ResendEmail from '@/components/pages/auth/RegisterView/ResendEmail.vue'

const requestedToken = ref(false)

const email = ref('')

function onCreatedRegisterToken(_email: string) {
  requestedToken.value = true
  email.value = _email
}
</script>
