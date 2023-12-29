<template>
  <Dialog
    ref="dialog"
    :pt="{
      root: { class: 'p-dialog-maximized' }
    }"
    :visible="visible"
    :draggable="false"
    modal
    header="See your first session"
    @update:visible="onHide"
  >
    <div class="grid grid-cols-12 gap-5">
      <div class="col-span-2">
        <SessionSummary :session="session" />
      </div>

      <div class="col-span-7"></div>

      <div class="col-span-3"></div>
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import { PropType, ref } from 'vue'
import Dialog from 'primevue/dialog'
import type { SessionCodec } from '@/services/api/resources/session/codec'
import SessionSummary from '@/components/resources/session/SessionSummary.vue'

const dialog = ref<InstanceType<typeof Dialog> | null>()

defineProps({
  visible: {
    type: Boolean,
    required: true
  },
  session: {
    type: Object as PropType<SessionCodec>,
    required: true
  }
})

const emit = defineEmits(['hide'])

function onHide(value: boolean) {
  emit('hide', value)
}
</script>
