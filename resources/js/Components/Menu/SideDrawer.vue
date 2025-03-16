<script setup lang="ts">
  import { Sun, Moon, ChevronsRight } from 'lucide-vue-next';
  import { useThemeStore } from '@/stores/theme';
  import { PropType } from 'vue';

  const emits = defineEmits(['collapse']);

  const props = defineProps({
    target: {
      type: String as PropType<string>,
      required: true,
    }
  });

  const themeStore = useThemeStore();

  function openGitHub() {
    window.open('https://github.com/Andriichello', '_blank');
  }
</script>

<template>
  <div class="drawer-side">
    <label :for="target" aria-label="close sidebar" class="drawer-overlay"/>
    <!-- Sidebar content here -->
    <ul class="menu bg-base-200 text-base-content min-h-full w-xs">
      <li @click="emits('collapse')"
          class="w-fit flex flex-row gap-0 mt-2.5 mb-2 text-black active:text-white">
        <div class="-translate-x-9 bg-gray-300 pl-2">
          <ChevronsRight class="w-6 h-6" />

          <span class="text-lg font-mono pt-0.5">Collapse</span>
        </div>
      </li>

      <li class="grow opacity-0"></li>

      <li @click="themeStore.toggle">
        <div class="flex flex-row justify-center items-center py-3">
          <Sun class="w-6 h-6" v-if="themeStore.isDark"/>
          <Moon class="w-6 h-6" v-else/>

          <span class="text-lg">Switch to {{ themeStore.isDark ? 'Light' : 'Dark' }}</span>

          <Sun class="w-6 h-6 opacity-0"/>
        </div>
      </li>

      <li class="m-3"></li>

      <li @click="openGitHub">
        <div class="flex flex-row justify-center items-center gap-3">
          <div class="flex flex-col-reverse justify-center items-center opacity-0">
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

          <div class="grow flex flex-col justify-center items-center">
            <span class="text-xl font-bold">Logitry</span>
            <span class="text-sm">by Andriichello</span>
          </div>

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
    </ul>
  </div>
</template>
