<template>
  <div>
    <Button
      data-cy="invite-company-member__open-invite-member-dialog"
      severity="primary"
      icon="pi pi-user"
      label="Invite Member"
      @click="isDialogOpen = true"
    />

    <Dialog
      v-model:visible="isDialogOpen"
      modal
      header="Invite Members"
      :draggable="false"
      :style="{ maxWidth: '90%', minWidth: '500px' }"
    >
      <form
        @submit="onSubmit"
        class="pt-2"
      >
        <div class="flex gap-1">
          <InputText
            data-cy="invite-company-member__input-email"
            v-model="email"
            id="title"
            type="text"
            autofocus
            placeholder="Colleague Email"
            :class="{ 'p-invalid': errorMessage, 'w-full': true }"
            aria-describedby="title-help"
          />

          <Button
            data-cy="invite-company-member__add-email-to-invited-emails"
            class="add-email-button"
            icon="pi pi-plus"
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
            v-for="email in emails"
            :key="email"
          >
            <div class="flex justify-between items-center">
              <div class="flex gap-2 items-center">
                <span class="pi pi-user"></span>

                <div v-text="email" />
              </div>

              <Button
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

        <div class="mt-4 flex justify-between gap-4">
          <Button
            data-cy="invite-company-member__close-dialog"
            @click="closeDialog"
            label="Cancel"
            text
            severity="secondary"
          />

          <Button
            data-cy="invite-company-member__invite-button"
            label="Invite Members"
            type="button"
            :disabled="emails.length < 1"
            :loading="isInvitingMembers"
            @click="onInviteMembersClick"
          />
        </div>
      </form>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useSelectEmail } from '@/composables/useSelectEmail'
import { displayGeneralError } from '@/services/ui'
import type { ResourceID } from '@/services/api'
import type { WrappedResponse } from '@/services/api/axios'
import { addMembersToCompany } from '@/services/api/resources/company/companyMember/actions'

const isDialogOpen = ref(false)

const isInvitingMembers = ref(false)

const { emails, email, errorMessage, onSubmit, onRemoveEmail } = useSelectEmail()

const props = defineProps<{
  companyId: ResourceID
}>()

const emit = defineEmits(['invited:members'])

async function onInviteMembersClick() {
  try {
    isInvitingMembers.value = true
    await addMembersToCompany(props.companyId, { emails: emails.value })
    emit('invited:members')
    closeDialog()
  } catch (e) {
    displayGeneralError(e as WrappedResponse, { group: 'bottom-center' })
  } finally {
    isInvitingMembers.value = false
  }
}

function closeDialog() {
  isDialogOpen.value = false
  emails.value = []
}
</script>
