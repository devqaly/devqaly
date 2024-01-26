<template>
  <div class="h-screen w-screen grid justify-center items-center">
    <ProgressSpinner />
  </div>
</template>
<script lang="ts" setup>
import { onMounted } from 'vue'
import { useAppStore } from '@/stores/app'
import { useRouter } from 'vue-router'

const appStore = useAppStore()

const router = useRouter()

// This function will only run if the user is logged in
// We check if the user is logged in, in the route guards in `router/index.ts`
async function redirectToCompany() {
  await router.push({
    name: 'listProjects',
    params: { companyId: appStore.loggedUserCompanies.data[0].id }
  })
}

onMounted(redirectToCompany)
</script>
