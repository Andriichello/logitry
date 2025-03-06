<script setup lang="ts">
  import { Sun, Moon } from 'lucide-vue-next';
  import { useThemeStore } from '@/stores/theme';

  const themeStore = useThemeStore();
</script>

<template>
  <div class="drawer-side">
    <label for="map-drawer" aria-label="close sidebar" class="drawer-overlay"/>

    <ul class="menu bg-base-200 text-base-content min-h-full w-xs">
      <!-- Sidebar content here -->
      <li>
        <div class="flex flex-row justify-center items-center gap-3">
          <div class="flex flex-col-reverse justify-center items-center">
            <!-- Circle -->
            <div style="width:25px; height:25px"
                 class="border-2 border-white rounded-full relative opacity-75">
              <div style="background-color:blue;" class="w-full h-full opacity-60 rounded-full"></div>

              <div class="absolute w-[2px] h-[2px] bg-white rounded-full top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
            </div>

            <div style="transform: translateY(4px);">
              <!-- Marker -->
              <div style="background-color:blue; width:30px; height:30px"
                   class="border-2 border-white rounded-full rounded-br-none rotate-45">
                <span class="color-white rotate-45"></span>
              </div>
            </div>
          </div>
        </div>
      </li>
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
