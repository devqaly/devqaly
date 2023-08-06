<template>
  <div class="p-5">
    <div class="text-3xl font-medium text-900">My Projects</div>
    <div class="font-medium text-500 mb-3">The projects you are a member of will show up here</div>

    <SetupSDKDialog
      :project="activeProjectForIntegration"
      @close="onIntegrationDetailsModalCLose"
    />

    <div class="surface-card shadow-2 border-round-lg">
      <ProjectFilters
        class="p-4"
        :title="filters.title ?? ''"
        @update:filters="onFilterUpdate"
      />

      <Skeleton
        height="250px"
        width="100%"
        v-if="isFetchingProjects"
      />

      <DataTable
        class="border-round-lg"
        :value="projectStore.projectsRequest.data"
        v-if="!isFetchingProjects"
      >
        <Column
          field="title"
          header="Title"
        />
        <Column
          field="createdAt"
          header="Created At"
        >
          <template v-slot:body="{ data }: { data: ProjectCodec }">
            {{ formatToDateTime(data.createdAt) }}
          </template>
        </Column>
        <Column>
          <template #body="{ data }: { data: ProjectCodec }">
            <router-link
              data-cy="list-projects-view__project-dashboard"
              :data-project-id="data.id"
              :to="{ name: 'projectDashboard', params: { projectId: data.id } }"
            >
              <Button
                v-tooltip.left="'Dashboard'"
                severity="secondary"
                icon="pi pi-home"
                text
                rounded
              />
            </router-link>

            <router-link
              data-cy="list-projects-view__project-sessions"
              :data-project-id="data.id"
              :to="{ name: 'projectSessions', params: { projectId: data.id } }"
            >
              <Button
                v-tooltip.left="'Sessions'"
                severity="secondary"
                icon="pi pi-play"
                text
                rounded
              />
            </router-link>

            <Button
              data-cy="list-projects-view__project-integration-details"
              :data-project-id="data.id"
              v-tooltip.left="'Integration details'"
              severity="secondary"
              icon="pi pi-eye"
              text
              rounded
              @click="() => onIntegrationDetailsClick(data)"
            />
          </template>
        </Column>
      </DataTable>

      <Paginator
        template="PageLinks"
        v-bind="getPaginationPropsForMeta(projectStore.projectsRequest.meta, perPage)"
        @page="onPageUpdate"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'
import { useProjectsStore } from '@/stores/projects'
import { formatToDateTime } from '@/services/date'
import ProjectFilters from '@/components/pages/projects/ListProjectsView/ProjectFilters.vue'
import type { GetProjectsParameters, ProjectCodec } from '@/services/api/resources/project/codec'
import { emptyPagination } from '@/services/api'
import { getPaginationPropsForMeta } from '@/services/ui'
import type { PageState } from 'primevue/paginator'
import { useAppStore } from '@/stores/app'
import SetupSDKDialog from '@/components/resources/project/SetupSDKDialog.vue'
import { assertsIsCompanyCodec } from '@/services/resources/Company'

const isFetchingProjects = ref(false)

const projectStore = useProjectsStore()

const appStore = useAppStore()

const perPage = ref(50)

const currentPage = ref(1)

const filters = ref<GetProjectsParameters>({})

const activeProjectForIntegration = ref<ProjectCodec | null>(null)

onMounted(async () => {
  assertsIsCompanyCodec(appStore.activeCompany)

  isFetchingProjects.value = true
  await projectStore.fetchProjects(appStore.activeCompany.id, { perPage: perPage.value })
  isFetchingProjects.value = false
})

watch(filters, async (filters) => {
  assertsIsCompanyCodec(appStore.activeCompany)

  currentPage.value = 1
  projectStore.projectsRequest = emptyPagination()

  isFetchingProjects.value = true
  await projectStore.fetchProjects(appStore.activeCompany.id, {
    ...filters,
    perPage: perPage.value,
    page: currentPage.value
  })
  isFetchingProjects.value = false
})

watch(
  () => appStore.activeCompany,
  async (company) => {
    if (company === null) return

    isFetchingProjects.value = true
    await projectStore.fetchProjects(company.id, { perPage: perPage.value })
    isFetchingProjects.value = false
  }
)

function onFilterUpdate(_filters: GetProjectsParameters) {
  filters.value = _filters
}

async function onPageUpdate(page: PageState) {
  assertsIsCompanyCodec(appStore.activeCompany)

  currentPage.value = page.page + 1

  isFetchingProjects.value = true
  await projectStore.fetchProjects(appStore.activeCompany.id, {
    ...filters.value,
    page: currentPage.value,
    perPage: perPage.value
  })
  isFetchingProjects.value = false
}

async function onIntegrationDetailsClick(project: ProjectCodec) {
  activeProjectForIntegration.value = project
}

async function onIntegrationDetailsModalCLose() {
  activeProjectForIntegration.value = null
}
</script>
