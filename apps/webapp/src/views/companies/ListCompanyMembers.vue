<template>
  <div class="p-5">
    <div class="text-3xl font-medium text-900">Members</div>
    <div class="font-medium text-500 mb-3">See the members that are part of this company</div>

    <div class="surface-card shadow-2 border-round-lg">
      <div class="p-4 flex justify-content-between align-items-center">
        <FiltersSection
          :filters="filters"
          @update:filters="onFilterUpdate"
        />

        <InviteCompanyMember
          :company-id="appStore.loggedUser.company.id"
          @invited:members="onInvitedMembers"
        />
      </div>

      <Skeleton
        height="250px"
        width="100%"
        v-if="isFetchingMembers"
      />

      <DataTable
        class="border-round-lg"
        :value="companyMemberStore.companyMembersRequest.data"
        v-if="!isFetchingMembers"
      >
        <Column
          field="member.fullName"
          header="Member"
        >
          <template #body="{ data }: { data: CompanyMemberCodec }">
            <div
              v-if="data.member"
              v-text="data.member.fullName"
            />

            <div
              v-if="data.member === null && data.registerToken"
              class="flex align-items-center gap-2"
            >
              {{ data.registerToken.email }}
              <i
                class="pi pi-info-circle"
                v-tooltip="'User have not created an account with us yet'"
              ></i>
            </div>
          </template>
        </Column>
        <Column
          field="invitedBy.fullName"
          header="Invited By"
        />
        <Column
          field="createdAt"
          header="Invited At"
        >
          <template v-slot:body="{ data }: { data: CompanyMemberCodec }">
            {{ formatToDateTime(data.createdAt) }}
          </template>
        </Column>
      </DataTable>

      <Paginator
        template="PageLinks"
        v-bind="getPaginationPropsForMeta(companyMemberStore.companyMembersRequest.meta, perPage)"
        @page="onPageUpdate"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { useCompanyMembersStore } from '@/stores/companyMembers'
import { useAppStore } from '@/stores/app'
import type { GetCompanyMembersParameters } from '@/services/api/resources/company/companyMember/requests'
import FiltersSection from '@/components/pages/companies/ListCompanyMembers/FiltersSection.vue'
import { formatToDateTime } from '@/services/date'
import type { CompanyMemberCodec } from '@/services/api/resources/company/companyMember/codec'
import InviteCompanyMember from '@/components/resources/companyMember/InviteCompanyMember.vue'
import { getPaginationPropsForMeta } from '@/services/ui'
import type { PageState } from 'primevue/paginator'
import { OrderBy } from '@/services/api'

const companyMemberStore = useCompanyMembersStore()

const appStore = useAppStore()

const filters = ref<GetCompanyMembersParameters>({
  orderByCreatedAt: OrderBy.DESC
})

const currentPage = ref(1)

const isFetchingMembers = ref(false)

const perPage = ref(50)

onMounted(() => {
  companyMemberStore.fetchCompanyMembers(appStore.loggedUser.company.id, {
    ...filters.value,
    perPage: perPage.value
  })
})

async function onInvitedMembers() {
  currentPage.value = 1
  filters.value = { orderByCreatedAt: OrderBy.DESC }

  isFetchingMembers.value = true
  await companyMemberStore.fetchCompanyMembers(appStore.loggedUser.company.id, filters.value)
  isFetchingMembers.value = false
}

async function onFilterUpdate(_filters: GetCompanyMembersParameters) {
  filters.value = _filters
  currentPage.value = 0

  isFetchingMembers.value = true
  await companyMemberStore.fetchCompanyMembers(appStore.loggedUser.company.id, {
    ...filters.value,
    perPage: perPage.value,
    page: currentPage.value
  })
  isFetchingMembers.value = false
}

async function onPageUpdate(page: PageState) {
  currentPage.value = page.page

  isFetchingMembers.value = true
  await companyMemberStore.fetchCompanyMembers(appStore.loggedUser.company.id, {
    ...filters.value,
    page: currentPage.value,
    perPage: perPage.value
  })
  isFetchingMembers.value = false
}
</script>
