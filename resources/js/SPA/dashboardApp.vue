<script setup>
import { ref, onMounted, onBeforeUnmount, defineAsyncComponent } from 'vue'
import { useSettingsStore } from './store/useSettingsStore.js'

const DashboardAuth = defineAsyncComponent(() => import('./dashboard/dashboard-auth.vue'))
const DashboardSettings = defineAsyncComponent(() => import('./dashboard/dashboard-settings.vue'))

const settings = useSettingsStore()
const settingsRef = ref(null)

onMounted(() => {
    settings.register(() => settingsRef.value?.open())
})

onBeforeUnmount(() => {
    settings.register(null)
})
</script>

<template>
    <DashboardSettings ref="settingsRef" />
    <DashboardAuth />
</template>
