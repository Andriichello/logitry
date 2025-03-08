<script setup lang="ts">
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import { onMounted, onUnmounted, PropType, provide, ref, watch, computed } from 'vue';
  import { Bounds, Company, Route, RoutePrice, Trip, TripHighlight } from '@/api';
  import CompassButton from '@/Components/Map/CompassButton.vue';
  import { useThemeStore } from '@/stores/theme';
  import { useMapStore } from '@/stores/map';
  import SideDrawer from '@/Components/Menu/SideDrawer.vue';
  import MenuButton from '@/Components/Menu/MenuButton.vue';
  import SideView from '@/Components/Map/SideView.vue';
  import { MapPinned, Route as RouteIcon } from 'lucide-vue-next';
  import dayjs from 'dayjs';
  import BookingCalendar from '@/Components/Date/BookingCalendar.vue';
  import {useForm} from "@inertiajs/vue3";
  import SideViewFrom from "@/Components/Map/SideViewFrom.vue";
  import { toast } from '@lucide/lab';
  import { useToast } from 'vue-toastification';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    bounds: Object as PropType<Bounds> | null,
    routes: Array as PropType<Route[]> | null,
    trips: Array as PropType<Trip[]> | null,
    trip_highlights: Array as PropType<TripHighlight[]> | null,
    countries: Object as PropType<Record<string, string>> | null,
  });

  const themeStore = useThemeStore();
  const mapStore = useMapStore();

  const map = ref(null as L.Map | null);
  // Provide map to all the pages
  provide('map', map);

  const darkMap = ref(null);
  const lightMap = ref(null);

  const isShowingMap = ref(false);
  const isShowingFrom = ref(false);
  const isShowingCalendar = ref(false);
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
      .on('zoomend', () => {
        mapStore.recalculateZoom(map.value ?? null)
      })
      .setView(center, 5);

    lightMap.value = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png');
    darkMap.value = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}.png');

    map.value.addLayer(themeStore.isDark ? darkMap.value : lightMap.value);

    fitBounds(mapStore.route?.bounds ?? props.bounds);
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

  function reloadWithFilters() {
    const params = [];

    if (mapStore.filters.abbreviation) {
      params.push(`abbreviation=${mapStore.filters.abbreviation}`);
    }

    if (mapStore.filters.beg) {
      params.push(`beg=${mapStore.filters.beg.format('YYYY-MM-DD')}`);
    }

    if (mapStore.filters.end) {
      params.push(`end=${mapStore.filters.end.format('YYYY-MM-DD')}`);
    }

    if (mapStore.filters.from) {
      params.push(`from=${mapStore.filters.from}`);
    }

    if (mapStore.filters.to) {
      params.push(`to=${mapStore.filters.to}`);
    }

    let url = '/map';

    if (params.length) {
      url += `?${params.join('&')}`;
    }

    useForm().get(url);
  }

  function applyCalendar({beg, end}: {beg: dayjs.Dayjs | null, end: dayjs.Dayjs | null}) {
    mapStore.filters.beg = beg;
    mapStore.filters.end = end;

    closeCalendar();
    reloadWithFilters();
  }

  function openFrom() {
    isShowingFrom.value = true;
  }

  function closeFrom() {
    isShowingFrom.value = false;
  }

  function applyFrom(from) {
    mapStore.filters.from = from?.join(',');
    isShowingFrom.value = false;

    reloadWithFilters();
  }

  function swapFromAndTo() {
    if (!mapStore.filters.from && !mapStore.filters.to) {
      return;
    }

    if (mapStore.filters.from === mapStore.filters.to) {
      return;
    }

    let temp = mapStore.filters.from;

    mapStore.filters.from = mapStore.filters.to;
    mapStore.filters.to = temp;

    reloadWithFilters();
  }


  function openCalendar() {
    isShowingCalendar.value = true;
  }

  function closeCalendar() {
    isShowingCalendar.value = false;
  }

  function clearFilters() {
    let shouldReload = false

    if (mapStore.filters.from?.length) {
      mapStore.filters.from = null;
      shouldReload = true;
    }

    if (mapStore.filters.to?.length) {
      mapStore.filters.to = null;
      shouldReload = true;
    }

    if (mapStore.filters.beg && !mapStore.filters.beg.isSame(dayjs(), 'day')) {
      mapStore.filters.beg = null;
      shouldReload = true;
    }

    if (mapStore.filters.end) {
      mapStore.filters.end = null;
      shouldReload = true;
    }

    if (shouldReload) {
      reloadWithFilters();
    }
  }

  function routeClicked(route: Route) {
    mapStore.route = route;

    fitBounds(route.bounds ?? props.bounds);
  }

  function routeClosed(route: Route) {
    mapStore.route = null;

    fitBounds(props.bounds);
  }

  function tripClicked(trip: Trip) {
    mapStore.trip = trip;
  }

  function tripClosed(trip: Trip) {
    mapStore.trip = null;
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
    () => themeStore.isDark,
    (newValue, oldValue) => {
      if (newValue !== oldValue) {
        if (map.value) {
          setTimeout(() => {
            map.value.removeLayer(oldValue ? darkMap.value : lightMap.value);
            map.value.addLayer(newValue ? darkMap.value : lightMap.value);
          });
        }
      }
    });

  watch(
    () => mapStore.route,
    (newValue, oldValue) => {
      if (newValue && newValue.bounds && newValue !== oldValue) {
        fitBounds(newValue.bounds);
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

    <div id="map-page" class="w-full h-full flex flex-row-reverse relative">
      <div id="map" class="h-[100vh] relative">
        <slot/>
      </div>

      <template v-if="isShowingCalendar">
        <div class="side w-full px-4 py-4">
          <BookingCalendar :months="5"
                           :beg="mapStore.filters.beg"
                           :end="mapStore.filters.end"
                           :dotted-dates="trip_highlights"
                           @apply-calendar="applyCalendar"
                           @close-calendar="closeCalendar"/>
        </div>
      </template>

      <template v-else-if="isShowingFrom">
        <div class="side w-full px-4 py-4">
          <SideViewFrom title="Countries"
                        :from="mapStore.filters.from?.length ? mapStore.filters.from.split(',') : []"
                        :countries="props.countries"
                        @apply-from="applyFrom"
                        @close-from="closeFrom"/>
        </div>
      </template>

      <template v-else>
        <SideView class="side" id="side"
                  v-if="!isShowingMap || !isNarrowScreen"
                  :company="props.company"
                  :routes="props.routes"
                  :trips="props.trips"
                  :countries="props.countries"
                  @open-from="openFrom"
                  @open-calendar="openCalendar"
                  @clear-filters="clearFilters"
                  @route-clicked="routeClicked"
                  @route-closed="routeClosed"
                  @trip-clicked="tripClicked"
                  @trip-closed="tripClosed"/>

        <MenuButton id="menu-button" class="absolute top-4 right-4 z-[400] text-xs"
                    @click="clickDrawer"/>

        <CompassButton id="compass" class="absolute bottom-6 right-4 z-[400] hidden"
                       @click="fitBounds(mapStore.route?.bounds ?? props.bounds)"/>

        <template v-if="isNarrowScreen">
          <div id="map-switcher" class="flex btn btn-md absolute bottom-6 right-4 z-[400] bg-base-100"
               v-if="isShowingMap"
               @click="toggleMap">
            <span class="text-md">{{ mapStore.route ? mapStore.trip ? 'Trip Details' : 'Route Details' : 'Routes List' }}</span>
            <RouteIcon class="w-5 h-5"/>
          </div>

          <div id="map-switcher" class="flex btn btn-md absolute bottom-6 right-4 z-[400] bg-gray-200"
               v-else
               @click="toggleMap">
            <span class="text-md text-neutral">View on Map</span>
            <MapPinned class="w-6 h-6" color="black"/>
          </div>
        </template>
      </template>
    </div>
  </main>
</template>

<style scoped>
  .side {
    @apply h-[100vh] max-h-[100vh];

    min-width: 35vw;
    max-width: 500px;
  }

  #side {
    min-width: 35vw;
    max-width: 500px;
  }

  #map {
    @apply flex-1;
  }

  @media (max-width: 800px) {
    .side {
      @apply h-[100vh] max-h-[100vh];

      min-width: 35vw;
      max-width: 100%;
    }

    #side {
      min-width: 35vw;
      max-width: 100%;
    }

    #map {
      @apply w-full;
    }
  }
</style>
