<template>
  <div class="p-4">
    <GeneralSettingsSection />
    <ClientSecuritySection class="mt-5" />
  </div>
</template>

<script setup lang="ts">
import ClientSecuritySection from '@/components/pages/projects/ProjectSettingsView/ClientSecuritySection.vue'
import GeneralSettingsSection from '@/components/pages/projects/ProjectSettingsView/GeneralSettingsSection.vue'
</script>
