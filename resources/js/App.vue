<script setup>
import { useUIStore } from '@/stores/ui'
import { useRouter } from 'vue-router'

const router = useRouter()
const uiStore = useUIStore()

// Configurar observadores de navegación
router.beforeEach(() => {
    uiStore.isLoading = true
})

router.afterEach(() => {
    uiStore.isLoading = false
})
</script>

<template>
    <AppNavigation />

    <div v-if="uiStore.isLoading">
        <AppLoader/>
    </div>
    <router-view v-else />
</template>