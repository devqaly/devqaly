<template>
  <div class="p-5">
    <div class="text-3xl font-medium">Members</div>
    <div class="font-medium text-slate-500 mb-4">See the members that are part of this company</div>

    <div class="bg-white shadow-md rounded-lg">
      <div class="p-5 flex justify-between items-center">
        <FiltersSection
          :filters="filters"
          @update:filters="onFilterUpdate"
        />

        <InviteCompanyMember
          :company-id="appStore.activeCompany!.id"
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
              class="flex items-center gap-2"
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
        <Column headerStyle="width: 1%;white-space: nowrap;">
          <template #body="{ data }: { data: CompanyMemberCodec }">
            <div class="min-h-[42px]">
              <Button
                data-cy="list-company-members-view__remove-user"
                :data-member-id="data.id"
                v-if="appStore.loggedUser.id !== data.member?.id"
                @click="onClickRemoveUserFromCompany(data)"
                icon="pi pi-trash"
                link
                rounded
                :loading="isRemovingMember"
              />
            </div>
          </template>
        </Column>
      </DataTable>

      <Paginator
        template="PageLinks"
        v-bind="getPaginationPropsForMeta(companyMemberStore.companyMembersRequest.meta, perPage)"
        @page="onPageUpdate"
      />

      <ConfirmDialog group="confirmRemoveUserFromCompany">
        <template #message>
          <div class="flex flex-col items-center w-full gap-3">
            <i class="text-6xl text-primary-500"></i>
            <p>Are you sure you would like to remove user from company?</p>
          </div>
        </template>
      </ConfirmDialog>
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
import { displayGeneralError, getPaginationPropsForMeta } from '@/services/ui'
import type { PageState } from 'primevue/paginator'
import { OrderBy } from '@/services/api'
import { assertsIsCompanyCodec } from '@/services/resources/Company'
import { useConfirm } from 'primevue/useconfirm'
import type { WrappedResponse } from '@/services/api/axios'

const companyMemberStore = useCompanyMembersStore()

const appStore = useAppStore()

const filters = ref<GetCompanyMembersParameters>({
  orderByCreatedAt: OrderBy.DESC
})

const currentPage = ref(1)

const isFetchingMembers = ref(false)

const perPage = ref(50)

const isRemovingMember = ref(false)

const confirm = useConfirm()

onMounted(() => {
  assertsIsCompanyCodec(appStore.activeCompany)

  companyMemberStore.fetchCompanyMembers(appStore.activeCompany.id, {
    ...filters.value,
    perPage: perPage.value
  })
})

async function onInvitedMembers() {
  assertsIsCompanyCodec(appStore.activeCompany)

  currentPage.value = 1
  filters.value = { orderByCreatedAt: OrderBy.DESC }

  isFetchingMembers.value = true
  await companyMemberStore.fetchCompanyMembers(appStore.activeCompany.id, filters.value)
  isFetchingMembers.value = false
}

async function onFilterUpdate(_filters: GetCompanyMembersParameters) {
  assertsIsCompanyCodec(appStore.activeCompany)

  filters.value = _filters
  currentPage.value = 1

  isFetchingMembers.value = true
  await companyMemberStore.fetchCompanyMembers(appStore.activeCompany.id, {
    ...filters.value,
    perPage: perPage.value,
    page: currentPage.value
  })
  isFetchingMembers.value = false
}

async function onPageUpdate(page: PageState) {
  assertsIsCompanyCodec(appStore.activeCompany)

  currentPage.value = page.page + 1

  isFetchingMembers.value = true
  await companyMemberStore.fetchCompanyMembers(appStore.activeCompany.id, {
    ...filters.value,
    page: currentPage.value,
    perPage: perPage.value
  })
  isFetchingMembers.value = false
}

function onClickRemoveUserFromCompany(member: CompanyMemberCodec) {
  confirm.require({
    group: 'confirmRemoveUserFromCompany',
    header: 'Destructive action',
    acceptLabel: 'Remove Member',
    accept: () => onConfirmRemoveUserFromCompany(member),
    blockScroll: true
  })
}

async function onConfirmRemoveUserFromCompany(member: CompanyMemberCodec) {
  assertsIsCompanyCodec(appStore.activeCompany)

  isRemovingMember.value = true

  try {
    await companyMemberStore.removeUsersFromCompany(appStore.activeCompany.id, member.id)
  } catch (e) {
    displayGeneralError(e as WrappedResponse, { group: 'bottom-center' })
  } finally {
    isRemovingMember.value = false
  }
}
</script>
