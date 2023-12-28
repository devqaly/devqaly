<template>
  <div class="bg-slate-50 min-h-screen p-5">
    <div class="mx-auto flex justify-center my-8">
      <Image
        class="max-w-[14rem] mx-auto"
        src="/logo--dark.svg"
        alt="Logo"
        height="45"
      />
    </div>

    <div
      class="bg-white flex rounded-md w-fit shadow mx-auto"
      aria-label="Tabs"
    >
      <RouterLink
        active-class="!border-b-blue-500"
        :to="{ name: 'onboardInstalling', params: route.params }"
        class="border-b border-transparent px-5 py-4 border-r-2 border-r-slate-50 cursor-pointer"
      >
        1. Installing Devqaly
      </RouterLink>

      <RouterLink
        active-class="!border-b-blue-500"
        :to="{ name: 'onboardCreateSession', params: route.params }"
        class="border-b border-transparent px-5 py-4 border-r-2 border-r-slate-50 cursor-pointer"
      >
        2. Create First Session
      </RouterLink>

      <RouterLink
        active-class="!border-b-blue-500"
        :to="{ name: 'onboardInviteTeamMembers', params: route.params }"
        class="border-b border-transparent px-5 py-4 border-r-2 border-r-slate-50 cursor-pointer"
      >
        3. Invite Team Members
      </RouterLink>
    </div>

    <main>
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'

const route = useRoute()
</script>
