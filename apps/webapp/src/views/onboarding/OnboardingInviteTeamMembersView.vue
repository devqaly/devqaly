<template>
  <div class="mx-auto bg-white mt-4 w-8/12 rounded-lg shadow">
    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
      <div class="text-xl">🧑‍🤝‍🧑 Lets invite your team mates</div>

      <a
        data-cy="onboarding-invite-page__see-docs"
        target="_blank"
        href="https://docs.devqaly.com/getting-started/introduction"
      >
        <Button link> See Docs </Button>
      </a>
    </div>

    <div class="mt-2 px-5 pt-2 pb-4">
      Your team mates will only be able to see the sessions if they have an account. This is a great
      place to invite them to your project.

      <div class="text-xs text-slate-500">
        You will be able to invite team members later as well.
      </div>

      <form
        @submit="onSubmit"
        class="mt-4"
      >
        <div class="flex gap-1">
          <InputText
            data-cy="onboarding-invite-page__add-team-member-email"
            v-model="email"
            id="title"
            type="text"
            autofocus
            placeholder="Colleague Email"
            :class="{ 'p-invalid': errorMessage, grow: true }"
            aria-describedby="title-help"
          />

          <Button
            data-cy="onboarding-invite-page__invite-member"
            class="shrink"
            label="Invite Member"
            outlined
            severity="secondary"
            type="submit"
          />
        </div>

        <div
          class="overflow-y-auto max-h-52 mt-2"
          ref="emailsContainer"
        >
          <div
            class="my-1 p-2 bg-slate-50 rounded-md"
            data-cy="onboarding-invite-page__pre-invited-member"
            :data-email="email"
            v-for="email in emails"
            :key="email"
          >
            <div class="flex justify-between items-center">
              <div class="flex gap-2 items-center">
                <span class="pi pi-user"></span>

                <div v-text="email" />
              </div>

              <Button
                data-cy="onboarding-invite-page__remove-pre-invited-member"
                :data-email="email"
                text
                rounded
                severity="secondary"
                size="small"
                aria-label="Remove colleague from list"
                icon="pi pi-times"
                type="button"
                @click="() => onRemoveEmail(email)"
                :pt="{ root: { style: 'width: 16px; height: 28px' } }"
              />
            </div>
          </div>
        </div>
      </form>

      <div
        data-cy="onboarding-invite-page__no-members-invited-helper"
        class="bg-slate-100 rounded-md p-5 flex items-center gap-2"
        v-if="emails.length < 1"
      >
        <i class="pi pi-question shrink bg-red-400 p-2 rounded-full text-white"></i>

        <div>
          Session replay are useful for developers, product managers and designers. Devqaly works
          best when sharing your recordings.
        </div>
      </div>

      <div class="flex justify-between !mt-4">
        <RouterLink
          :to="{
            name: 'projectDashboard',
            query: { [SHOW_FREE_TRIAL_COMPANY_PARAMETER_NAME]: 1 },
            params: { projectId: route.params.projectId, companyId: route.params.companyId }
          }"
          data-cy="onboarding-invite-page__skip-step"
        >
          <Button
            class="!text-slate-400"
            label="Skip"
            link
          />
        </RouterLink>

        <Button
          data-cy="onboarding-invite-page__invite-members-btn"
          label="Complete Setup"
          icon="pi pi-chevron-right"
          icon-pos="right"
          :loading="isInvitingUsers"
          @click="onFinishOnboarding"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useSelectEmail } from '@/composables/useSelectEmail'
import { useRoute, useRouter } from 'vue-router'
import { ref } from 'vue'
import { addMembersToCompany } from '@/services/api/resources/company/companyMember/actions'
import { useToast } from 'primevue/usetoast'
import { SHOW_FREE_TRIAL_COMPANY_PARAMETER_NAME } from '@/services/resources/Company'

const { emails, email, errorMessage, onSubmit, onRemoveEmail } = useSelectEmail()

const router = useRouter()

const route = useRoute()

const isInvitingUsers = ref(false)

const toast = useToast()

async function onFinishOnboarding() {
  try {
    isInvitingUsers.value = true

    if (emails.value.length > 0) {
      await addMembersToCompany(route.params.companyId as string, { emails: emails.value })
    }

    await router.push({
      name: 'projectDashboard',
      query: { [SHOW_FREE_TRIAL_COMPANY_PARAMETER_NAME]: 1 },
      params: { projectId: route.params.projectId, companyId: route.params.companyId as string }
    })
  } catch (e) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'There was an error inviting members',
      life: 3000,
      group: 'bottom-center'
    })

    console.error(e)
  } finally {
    isInvitingUsers.value = false
  }
}
</script>
