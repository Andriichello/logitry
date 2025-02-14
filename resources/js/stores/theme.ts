import { defineStore } from 'pinia'

interface ThemeState {
  isDark: boolean | null
}

export const useThemeStore = defineStore('theme', {
  state: () => {
    let theme = localStorage.getItem('data-theme');

    if (!theme) {
      theme = window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark' : 'lofi';
    }

    return {
      isDark: theme === 'dark',
    }
  },
  actions: {
    apply(isDark: boolean) {
      const theme = isDark ? 'dark' : 'lofi';

      document.body.setAttribute('data-theme', theme);
      localStorage.setItem('data-theme', theme);

      this.isDark = isDark;
    },
    toggle() {
      this.apply(!this.isDark);
    },
  },
});
