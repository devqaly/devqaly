<template>
  <div>
    <div
      v-if="shouldShowSubscriptionWarning()"
      class="bg-white rounded-md shadow-md p-5 flex justify-between flex-col lg:flex-row items-center m-5 no-underline"
    >
      <div>
        <div class="font-semibold mb-3 text-xl">Free Subscription</div>
        <p class="mt-0 mb-4 lg:mb-0 p-0">
          You currently have a free subscription for this company. You will be able to have active
          50 sessions for each project. On the 51st session, the first one will be archived.
        </p>
      </div>

      <a href="mailto:bruno.francisco@devqaly.com">
        <Button
          label="Request Higher Limit"
          icon="pi pi-check"
          class="ml-0 lg:ml-5"
        />
      </a>
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

function shouldShowSubscriptionWarning(): boolean {
  if (!shouldShowSubscriptionConcerns()) return false

  assertsIsCompanyCodec(appStore.activeCompany)

  // If no subscription is present, we are assuming the user is in free plan.
  if (
    appStore.activeCompany.subscription === null ||
    appStore.activeCompany.subscription === undefined
  )
    return true

  return !isActiveSubscription(appStore.activeCompany.subscription.status)
}
</script>
