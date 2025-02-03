import { defineStore } from 'pinia'

interface AuthState {
  token: string | null,
}

export const useAuthStore = defineStore('auth', {
  state: () => {
    return {

    }
  },
  // could also be defined as
  // state: () => ({ count: 0 })
  actions: {
    increment() {
      this.count++
    },
  },
});
