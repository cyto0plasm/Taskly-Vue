import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSettingsStore = defineStore('settings', () => {
    let _fn = null
    const hasFn = ref(false)  // 👈 separate reactive flag for v-if

    function register(fn) {
        _fn = fn
        hasFn.value = !!fn
    }

    function open() {
        _fn?.()
    }

    return { hasFn, register, open }
})
