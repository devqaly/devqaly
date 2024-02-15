<template>
  <div>
    <div class="m-5">
      <TrialEndingWarning />
    </div>
    <div class="bg-white overflow-x-auto m-5 rounded-md shadow-md">
      <div class="mx-4 my-2">
        <div class="flex justify-between items-center">
          <div class="font-medium text-3xl mt-3 mb-2">
            <Skeleton
              v-if="isFetchingActiveProject"
              width="90px"
              height="32px"
            />

            <span
              v-else
              v-text="projectStore.activeProject?.title"
            />
          </div>
        </div>

        <div class="flex gap-3">
          <TabMenu
            :model="projectNavigationItems"
            class="w-full"
            v-if="!isFetchingActiveProject"
          />
          <template v-else>
            <Skeleton
              width="50px"
              height="20px"
            />
            <Skeleton
              width="50px"
              height="20px"
            />
          </template>
        </div>
      </div>

      <template v-if="isFetchingActiveProject">
        <div class="p-4">
          <Skeleton
            width="80px"
            height="34px"
          />
          <Skeleton
            class="mt-1"
            width="120px"
            height="20px"
          />

          <Skeleton
            height="120px"
            class="mt-2"
          />
        </div>
      </template>
      <template v-else>
        <router-view />
      </template>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useProjectsStore } from '@/stores/projects'
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import {
  isActiveSubscription,
  shouldShowSubscriptionConcerns
} from '@/services/resources/Subscription'
import { assertsIsCompanyCodec } from '@/services/resources/Company'
import { useAppStore } from '@/stores/app'
import TrialEndingWarning from '@/components/layouts/ProjectLayout/TrialEndingWarning.vue'

const isFetchingActiveProject = ref(false)

const route = useRoute()

const projectStore = useProjectsStore()

const appStore = useAppStore()

const projectNavigationItems = computed(() => [
  {
    label: 'Dashboard',
    icon: 'pi pi-fw pi-home',
    to: {
      name: 'projectDashboard',
      params: { projectId: projectStore.activeProject?.id, companyId: route.params.companyId }
    }
  },
  {
    label: 'Sessions',
    icon: 'pi pi-fw pi-video',
    to: {
      name: 'projectSessions',
      params: { projectId: projectStore.activeProject?.id, companyId: route.params.companyId }
    }
  },
  {
    label: 'Settings',
    icon: 'pi pi-fw pi-cog',
    to: {
      name: 'projectSettings',
      params: { projectId: projectStore.activeProject?.id, companyId: route.params.companyId }
    }
  }
])

watch(
  () => route.params.projectId,
  async (projectId) => {
    if (projectId === undefined) {
      projectStore.activeProject = null

      return
    }

    isFetchingActiveProject.value = true
    await projectStore.getActiveProject(projectId as string)
    isFetchingActiveProject.value = false
  },
  {
    immediate: true
  }
)
</script>
