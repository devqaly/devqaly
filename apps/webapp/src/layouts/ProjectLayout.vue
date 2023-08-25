<template>
  <div>
    <div class="surface-section overflow-x-auto border-bottom-1 surface-border m-4 border-round-lg">
      <div class="mx-4 my-2">
        <div class="flex justify-content-between align-items-center">
          <div class="font-medium text-3xl mt-2 mb-1">
            <Skeleton
              v-if="isFetchingActiveProject"
              width="50px"
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

const isFetchingActiveProject = ref(false)

const route = useRoute()

const projectStore = useProjectsStore()

const projectNavigationItems = computed(() => [
  {
    label: 'Dashboard',
    icon: 'pi pi-fw pi-home',
    to: {
      name: 'projectDashboard',
      params: { projectId: projectStore.activeProject?.id }
    }
  },
  {
    label: 'Sessions',
    icon: 'pi pi-fw pi-video',
    to: {
      name: 'projectSessions',
      params: { projectId: projectStore.activeProject?.id }
    }
  },
  {
    label: 'Settings',
    icon: 'pi pi-fw pi-cog',
    to: {
      name: 'projectSettings',
      params: { projectId: projectStore.activeProject?.id }
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
