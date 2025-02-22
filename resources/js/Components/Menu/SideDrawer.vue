<script setup lang="ts">
  import { Sun, Moon } from 'lucide-vue-next';
  import { useThemeStore } from '@/stores/theme';

  const themeStore = useThemeStore();
</script>

<template>
  <div class="drawer-side">
    <label for="map-drawer" aria-label="close sidebar" class="drawer-overlay"/>

    <ul class="menu bg-base-200 text-base-content min-h-full w-xs ">
      <!-- Sidebar content here -->
      <li></li>
      <li @click="themeStore.toggle">
        <div class="flex flex-row justify-start items-center">
          <Sun v-if="themeStore.isDark"/>
          <Moon v-else/>

          <span class="text-lg">Change theme</span>
        </div>
      </li>
    </ul>
  </div>
</template>
