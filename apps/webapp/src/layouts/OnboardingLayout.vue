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
      <div
        v-if="isLoadingProject"
        class="mx-auto bg-white mt-4 w-8/12 rounded-lg shadow"
      >
        <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
          <Skeleton
            width="50rem"
            height="2rem"
          />

          <Skeleton
            width="4rem"
            height="2rem"
          />
        </div>

        <div class="mt-2 px-5 pt-2 pb-4">
          <Skeleton
            width="100%"
            height="30rem"
          />
        </div>
      </div>
      <RouterView v-else />
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import { isError, WrappedResponse } from '@/services/api/axios'
import { HttpStatusCode } from 'axios'
import { useProjectsStore } from '@/stores/projects'
import { onBeforeMount, ref } from 'vue'

const route = useRoute()

const router = useRouter()

const projectStore = useProjectsStore()

const isLoadingProject = ref(true)

async function fetchProject() {
  try {
    await projectStore.fetchActiveOnboardingProject(route.params.projectId as string)

    isLoadingProject.value = false
  } catch (e) {
    if (isError(e as WrappedResponse, HttpStatusCode.NotFound)) {
      await router.push({ name: 'notFound' })
    }
  }
}

onBeforeMount(fetchProject)
</script>
