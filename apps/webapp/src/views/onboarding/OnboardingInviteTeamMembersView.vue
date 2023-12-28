<template>
  <div class="mx-auto bg-white mt-4 w-8/12 rounded-lg shadow">
    <div class="px-5 py-2 border-b border-slate-100 flex justify-between items-center">
      <div class="text-xl">🧑‍🤝‍🧑 Lets invite your team mates</div>

      <a
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
            v-model="email"
            id="title"
            type="text"
            autofocus
            placeholder="Colleague Email"
            :class="{ 'p-invalid': errorMessage, grow: true }"
            aria-describedby="title-help"
          />

          <Button
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
      </form>

      <div
        class="bg-slate-100 rounded-md p-5 flex items-center gap-2"
        v-if="emails.length < 1"
      >
        <i class="pi pi-question shrink bg-red-400 p-2 rounded-full text-white"></i>

        <div>
          Session replay are useful for developers, product managers and designers. Devqaly works
          best when sharing your recordings.
        </div>
      </div>

      <div class="flex justify-end">
        <Button
          class="!mt-4"
          label="Complete Setup"
          icon="pi pi-chevron-right"
          icon-pos="right"
          @click="onFinishOnboarding"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useSelectEmail } from '@/composables/useSelectEmail'
import { useRoute, useRouter } from 'vue-router'

const { emails, email, errorMessage, onSubmit, onRemoveEmail } = useSelectEmail()

const router = useRouter()

const route = useRoute()

function onFinishOnboarding() {
  router.push({
    name: 'projectDashboard',
    params: { projectId: route.params.projectId }
  })
}
</script>
