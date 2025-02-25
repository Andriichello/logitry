<script setup lang="ts">
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import { onMounted, onUnmounted, PropType, provide, ref, watch, computed } from 'vue';
  import { Bounds, Company, Route } from '@/api';
  import CompassButton from '@/Components/Map/CompassButton.vue';
  import { useThemeStore } from '@/stores/theme';
  import { useMapStore } from '@/stores/map';
  import SideDrawer from '@/Components/Menu/SideDrawer.vue';
  import MenuButton from '@/Components/Menu/MenuButton.vue';
  import SideView from '@/Components/Map/SideView.vue';
  import { MapPinned, Route as RouteIcon } from 'lucide-vue-next';

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

  const isShowingMap = ref(false);
  const isNarrowScreen = ref(false);

  onMounted(() => {
    onResize();
    window.addEventListener('resize', onResize);

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

  onUnmounted(() => {
    window.removeEventListener('resize', onResize);
  });

  function onResize() {
    isNarrowScreen.value = window.innerWidth < 800;
  }

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

  function toggleMap() {
    isShowingMap.value = !isShowingMap.value;

    if (map.value) {
      setTimeout(() => {
        map.value.invalidateSize();

        fitBounds(mapStore.route?.bounds ?? props.bounds);
      }, 100);
    }
  }

  watch(
    isNarrowScreen,
    (newValue, oldValue) => {
      if (newValue !== oldValue) {
        if (map.value) {
          setTimeout(() => {
            map.value.invalidateSize();

            fitBounds(mapStore.route?.bounds ?? props.bounds);
          }, 100);
        }
      }
    },
  );

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
                class="h-[100vh] max-h-[100vh]"
                v-if="!isShowingMap || !isNarrowScreen"
                @route-clicked="routeClicked"/>

      <div id="map" class="h-[100vh] relative">
        <slot/>
      </div>

      <MenuButton id="menu-button" class="absolute top-4 right-4 z-[400] text-xs"
                  @click="clickDrawer"/>

      <CompassButton id="compass" class="absolute bottom-6 right-4 z-[400] hidden"
                     @click="fitBounds(mapStore.route?.bounds ?? props.bounds)"/>

      <template v-if="isNarrowScreen">
        <div id="map-switcher" class="flex btn p-2 absolute bottom-6 right-4 z-[400] bg-base-100
"
             v-if="isShowingMap"
             @click="toggleMap">
          <span class="text-md">Open List</span>
          <RouteIcon class="w-5 h-5"/>
        </div>

        <div id="map-switcher" class="flex btn p-2 absolute bottom-6 right-4 z-[400] bg-gray-200"
             v-else
             @click="toggleMap">
          <span class="text-md text-neutral">Open Map</span>
          <MapPinned class="w-5 h-5" color="black"/>
        </div>
      </template>
    </div>
  </main>
</template>

<style scoped>
  #side {
    min-width: 35vw;
    max-width: 500px;
  }

  #map {
    @apply flex-1;
  }

  @media (max-width: 800px) {
    #side {
      min-width: 35vw;
      max-width: 100%;
    }

    #map {
      @apply w-full;
    }
  }
</style>
