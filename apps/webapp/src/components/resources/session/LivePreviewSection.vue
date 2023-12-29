<template>
  <section
    data-cy="project-session-view__live-preview-section"
    ref="sectionContainer"
    class="rounded-lg border border-gray-300 h-full flex flex-col"
  >
    <div
      class="bg-slate-100 p-3 shrink"
      ref="titleContainer"
    >
      <div class="font-medium text-2xl">Live Preview</div>
      <div class="text-gray-600 mt-2">
        The events that are happening at the current time in the video
      </div>
    </div>

    <ListEvents
      v-if="liveEventContainerHeight !== null"
      :style="{ height: `${liveEventContainerHeight}px` }"
      class="grow overflow-y-auto"
      :session="session"
      :events="events"
      :height="liveEventContainerHeight!"
      @see:details="onSeeDetails"
    />
  </section>
</template>

<script lang="ts" setup>
import { ListEvents } from '@/components/resources/events/listEvent/ListEvents'
import { onMounted, PropType, ref } from 'vue'
import type { EventCodec } from '@/services/api/resources/session/events/codec'
import type { SessionCodec } from '@/services/api/resources/session/codec'

defineProps({
  events: { type: Array as PropType<Array<EventCodec>>, required: true },
  session: { type: Object as PropType<SessionCodec>, required: true }
})

const emit = defineEmits<{
  'update:activeEventDetails': [event: EventCodec]
}>()

const sectionContainer = ref<HTMLElement | null>(null)

const titleContainer = ref<HTMLElement | null>(null)

const liveEventContainerHeight = ref<null | number>(null)

onMounted(() => {
  if (sectionContainer.value !== null && titleContainer.value !== null) {
    liveEventContainerHeight.value =
      sectionContainer.value!.clientHeight - titleContainer.value!.clientHeight
  }
})

function onSeeDetails(event: EventCodec) {
  emit('update:activeEventDetails', event)
}
</script>
