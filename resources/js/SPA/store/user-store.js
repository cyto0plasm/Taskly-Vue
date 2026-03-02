import { defineStore } from 'pinia'
import * as UserApi from '../../domain/tasks/user-api.js'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
        initialized: false,
    }),

    getters: {
        isLoggedIn: (state) => !!state.user,
        userName: (state) => state.user?.name ?? '',
        userEmail: (state) => state.user?.email ?? '',
        userRole: (state) => state.user?.role ?? '',
        preferences: (state) => state.user?.preferences ?? {},
        profilePhoto: (state) =>
    state.user?.profile_photo_path
        ? `/storage/profile_photos/${state.user.profile_photo_path}`
        : null,
    },

    actions: {

        async fetchUser() {
            if (this.initialized) return

            this.loading = true
            try {
                const res = await UserApi.fetchUser()
                this.user = res
            } catch (e) {
                this.user = null
            } finally {
                this.loading = false
                this.initialized = true
            }
        },

        async savePreferences(prefs) {
            const res = await UserApi.updateUserPreferences(prefs)
            if (this.user) {
                this.user.preferences = res.preferences
            }
        },

        async updateProfile(data) {
            const res = await UserApi.updateUser(data)
            this.user = res.user
        },

        logout() {
            this.user = null
            this.initialized = false
        }
    }
})
