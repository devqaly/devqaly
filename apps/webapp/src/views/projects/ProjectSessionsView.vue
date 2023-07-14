<template>
  <div>
    <ProjectSessionFilters
      class="p-4"
      :filters="filters"
      @update:filters="onFiltersUpdate"
    />

    <Skeleton
      style="border-radius: 0"
      v-if="fetchingSessions"
      height="200px"
    />

    <DataTable
      class="border-round-lg"
      :value="projectStore.activeProjectSessionsRequest.data"
      v-if="!fetchingSessions"
    >
      <Column
        field="createdBy.fullName"
        header="Created by"
      />
      <Column
        field="os"
        header="OS"
      />
      <Column
        field="platformName"
        header="Platform"
      />
      <Column
        field="version"
        header="Version"
      />
      <Column
        field="videoStatus"
        header="Video Conversion"
      >
        <template #body="{ data }: { data: SessionCodec }">
          <span
            v-text="getVideoStatusText(data.videoStatus)"
            :style="{ color: getVideoStatusColor(data.videoStatus) }"
          />
        </template>
      </Column>
      <Column
        field="createdAt"
        header="Created"
      >
        <template v-slot:body="{ data }: { data: SessionCodec }">
          {{ formatToDateTime(data.createdAt) }}
        </template>
      </Column>
      <Column
        field="videoDurationInSeconds"
        header="Video Duration"
      >
        <template v-slot:body="{ data }: { data: SessionCodec }">
          {{
            data.videoDurationInSeconds !== null
              ? translateVideoDuration(data.videoDurationInSeconds)
              : '---'
          }}
        </template>
      </Column>
      <Column>
        <template v-slot:body="{ data }: { data: SessionCodec }">
          <router-link
            :to="{ name: 'projectSession', params: { ...route.params, sessionId: data.id } }"
          >
            <Button
              label="See Session"
              text
              icon-pos="right"
              icon="pi pi-external-link"
            />
          </router-link>
        </template>
      </Column>
    </DataTable>

    <Paginator
      v-if="!fetchingSessions"
      template="FirstPageLink PageLinks LastPageLink"
      v-bind="getPaginationPropsForMeta(projectStore.activeProjectSessionsRequest.meta, perPage)"
      @page="onPageUpdate"
    />
  </div>
</template>

<script setup lang="ts">
import { useProjectsStore } from '@/stores/projects'
import { onBeforeMount, onBeforeUnmount, ref } from 'vue'
import { useRoute } from 'vue-router'
import { formatToDateTime } from '@/services/date'
import { emptyPagination, OrderBy } from '@/services/api'
import ProjectSessionFilters from '@/components/pages/projects/ProjectSessionsView/ProjectSessionFilters.vue'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import type { GetProjectSessionsParameters } from '@/services/api/resources/project/codec'
import {
  getVideoStatusColor,
  getVideoStatusText,
  translateVideoDuration
} from '@/services/resources/SessionsService'
import { getPaginationPropsForMeta } from '@/services/ui'
import type { PageState } from 'primevue/paginator'

const route = useRoute()

const projectStore = useProjectsStore()

const fetchingSessions = ref(false)

const filters = ref<GetProjectSessionsParameters>({
  createdAtOrder: OrderBy.DESC
})

const perPage = 20

const currentPage = ref(1)

onBeforeMount(async () => {
  fetchingSessions.value = true
  await projectStore.getActiveProjectSessions({ ...filters.value, page: 1, perPage })
  fetchingSessions.value = false
})

onBeforeUnmount(() => {
  projectStore.activeProjectSessionsRequest = emptyPagination()
})

async function onFiltersUpdate(_filters: GetProjectSessionsParameters) {
  currentPage.value = 1
  filters.value = _filters

  fetchingSessions.value = true
  await projectStore.getActiveProjectSessions({
    page: currentPage.value,
    createdAtOrder: OrderBy.DESC,
    perPage,
    ...filters.value
  })
  fetchingSessions.value = false
}

async function onPageUpdate(page: PageState) {
  currentPage.value = page.page + 1

  fetchingSessions.value = true
  await projectStore.getActiveProjectSessions({
    ...filters.value,
    page: currentPage.value,
    perPage
  })
  fetchingSessions.value = false
}
</script>
