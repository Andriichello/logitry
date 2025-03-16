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
  <main class="w-full h-full overflow-auto">
    <input type="checkbox" value="light" class="toggle theme-controller mt-1"
           :checked="!themeStore.isDark"
           @change="themeStore.toggle" hidden/>

    <div class="drawer drawer-end">
      <input id="landing-drawer" type="checkbox" class="drawer-toggle"/>

      <SideDrawer class="z-[1001] min-w-[25vw]"
                  target="landing-drawer"
                  @collapse="clickDrawer"/>
    </div>

    <div id="landing-page" class="w-full h-full flex flex-col justify-start">
      <div class="w-full flex justify-between items-center px-4 py-4">
        <CompanyInfo class="px-0 py-0"
                     :company="props.company"/>

        <MenuButton id="menu-button"
                    @click="clickDrawer"/>
      </div>

      <div class="w-full h-[2px] px-2">
        <div class="w-full h-[2px]"></div>
      </div>

      <slot/>
    </div>
  </main>
</template>
