<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import { PropType } from 'vue';
  import { Company } from '@/api';
  import { useThemeStore } from '@/stores/theme';
  import SideDrawer from '@/Components/Menu/SideDrawer.vue';
  import MenuButton from '@/Components/Menu/MenuButton.vue';
  import CompanyInfo from '@/Components/Map/CompanyInfo.vue';

  const props = defineProps({
    company: Object as PropType<Company> | null,
  });

  const themeStore = useThemeStore();

  function clickDrawer() {
    document.getElementById('landing-drawer')?.click();
  }
</script>

<template>
  <main class="w-full h-full">
    <input type="checkbox" value="light" class="toggle theme-controller mt-1"
           :checked="!themeStore.isDark"
           @change="themeStore.toggle" hidden/>

    <div class="drawer drawer-end">
      <input id="landing-drawer" type="checkbox" class="drawer-toggle"/>

      <SideDrawer class="z-[1005] min-w-[25vw]"
                  target="landing-drawer"
                  @collapse="clickDrawer"/>
    </div>

    <div class="w-full h-full flex flex-col justify-start overflow-auto"
         id="landing-page">

      <div class="w-full flex flex-col justify-start items-start bg-base-100 sticky top-0 z-[1001]">
        <div class="w-full flex justify-between items-center px-4 py-4 shadow-lg">
          <CompanyInfo class="max-w-3/4 px-0 py-0"
                       :company="props.company"/>

          <MenuButton id="menu-button"
                      @click="clickDrawer"/>
        </div>

        <div class="w-full flex flex-col justify-center items-center">
          <div class="w-full h-[1px]">
            <div class="w-full h-full bg-base-content opacity-10"></div>
          </div>
        </div>
      </div>

      <div class="w-full h-full flex flex-col justify-start">
        <slot/>
      </div>

    </div>
  </main>
</template>
