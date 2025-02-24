<script setup lang="ts">
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import { onMounted, PropType, provide, ref, watch } from 'vue';
  import { Bounds, Company, Route } from '@/api';
  import CompassButton from '@/Components/Map/CompassButton.vue';
  import { useThemeStore } from '@/stores/theme';
  import { useMapStore } from '@/stores/map';
  import SideDrawer from '@/Components/Menu/SideDrawer.vue';
  import MenuButton from '@/Components/Menu/MenuButton.vue';
  import SideView from '@/Components/Map/SideView.vue';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    bounds: Object as PropType<Bounds> | null,
    routes: Array as PropType<Route[]> | null,
    trips: Array as PropType<Trip[]> | null,
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

    fitBounds(mapStore.route?.bounds ?? props.bounds);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map.value);
  });

  function toBounds(given) {
    if (!given || !given.southWest || !given.northEast) {
      return null;
    }

    return L.latLngBounds(
      [given.southWest.latitude, given.southWest.longitude],
      [given.northEast.latitude, given.northEast.longitude],
    );
  }

  function fitBounds(given = null) {
    if (!map.value) {
      return;
    }

    let b = null;

    if (given) {
      b = toBounds(given);
    }

    if (!b) {
      return;
    }

    map.value.fitBounds(b);
  }

  function clickDrawer() {
    document.getElementById('map-drawer')?.click();
  }

  function routeClicked(route: Route) {
    mapStore.route = route;
  }

  watch(
    () => mapStore.route,
    (newValue) => {
      if (newValue) {
        fitBounds(newValue.bounds);
      } else {
        fitBounds(props.bounds)
      }
    },
    { immediate: true },
  );
</script>

<template>
  <main class="w-full h-full overflow-hidden">
    <input type="checkbox" value="light" class="toggle theme-controller mt-1"
           :checked="!themeStore.isDark"
           @change="themeStore.toggle" hidden/>

    <div class="drawer drawer-end">
      <input id="map-drawer" type="checkbox" class="drawer-toggle"/>

      <SideDrawer class="z-[1000] min-w-[25vw]"/>
    </div>

    <div id="map-page" class="w-full h-full flex relative">
      <SideView id="side" :company="props.company" :routes="props.routes" :trips="props.trips"
                class="max-w-sm h-[100vh] max-h-[100vh]"
                @route-clicked="routeClicked"/>

      <div id="map" class="h-[100vh] flex-1 relative">
        <slot/>
      </div>

      <MenuButton class="absolute top-2 right-2 z-[400] text-xs"
                  @click="clickDrawer"/>

      <CompassButton class="absolute bottom-6 right-2 z-[400]"
                     @click="fitBounds(mapStore.route?.bounds ?? props.bounds)"/>
    </div>
  </main>
</template>
