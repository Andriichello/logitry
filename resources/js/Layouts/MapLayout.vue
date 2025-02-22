<script setup lang="ts">
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import { computed, onMounted, PropType, provide, ref } from 'vue';
  import { Bounds, Route } from '@/api';
  import CompassButton from '@/Components/Map/CompassButton.vue';
  import { useThemeStore } from "@/stores/theme";
  import { useMapStore } from '@/stores/map';
  import SideDrawer from '@/Components/Menu/SideDrawer.vue';
  import MenuButton from '@/Components/Menu/MenuButton.vue';

  const props = defineProps({
    bounds: Object as PropType<Bounds> | null,
    routes: Array as PropType<Route[]> | null,
  });

  const themeStore = useThemeStore();
  const mapStore = useMapStore();

  const map = ref(null as L.Map | null);
  // Provide map to all the pages
  provide('map', map);

  onMounted(() => {
    const p = props?.routes?.[0]?.points?.[0];
    const center = p ? [p.latitude, p.longitude] : [50.0755, 14.4378];

    map.value = L.map('map', { zoomControl: false })
      .on('click', () => {
        console.log('map click');
        mapStore.clicks++;
      })
      .setView(center, 5);

    fitBounds();

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map.value);
  });

  const bounds = computed(() => {
    if (props?.bounds) {
      console.log('has bounds...', [
        [props.bounds.southWest.latitude, props.bounds.southWest.longitude],
        [props.bounds.northEast.latitude, props.bounds.northEast.longitude],
      ]);


      return L.latLngBounds(
        [props.bounds.southWest.latitude, props.bounds.southWest.longitude],
        [props.bounds.northEast.latitude, props.bounds.northEast.longitude],
      );
    }

    return null;
  });

  function fitBounds() {
    if (map.value && bounds.value) {
        map.value.fitBounds(bounds.value);
    }
  }

  function clickDrawer() {
    document.getElementById('map-drawer')?.click();
  }

</script>

<template>
  <main class="w-full h-full overflow-hidden">
    <input type="checkbox" value="light" class="toggle theme-controller mt-1"
           :checked="!themeStore.isDark"
           @change="themeStore.toggle" hidden/>

    <div class="drawer drawer-bottom">
      <input id="map-drawer" type="checkbox" class="drawer-toggle"/>

      <SideDrawer class="z-[1000]"/>
    </div>

    <div class="w-full h-full absolute">
      <div id="map">
        <slot/>
      </div>

      <MenuButton class="absolute top-2 left-2 z-[400] text-xs"
                  @click="clickDrawer"/>

      <CompassButton class="absolute bottom-6 right-2 z-[400]"
        @click="fitBounds"/>
    </div>
  </main>
</template>

<style scoped>
  #map {
    width: 100vw;
    height: 100vh;
    min-height: 100vh;
    max-height: 100vh;
  }
</style>

