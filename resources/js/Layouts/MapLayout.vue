<script setup lang="ts">
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import {onMounted, onUnmounted, PropType, provide, ref, watch} from 'vue';
  import {Bounds, Company, Route, Trip, TripHighlight} from '@/api';
  import {useThemeStore} from '@/stores/theme';
  import {useMapStore} from '@/stores/map';
  import SideView from '@/Components/Map/SideView.vue';
  import {MapPin, Menu, Moon, Sun, Truck, X} from 'lucide-vue-next';
  import dayjs from 'dayjs';
  import {Deferred, useForm} from '@inertiajs/vue3';
  import SideViewFrom from '@/Components/Map/SideViewFrom.vue';
  import {minutesToHumanReadable} from "@/helpers";
  import SideViewCalendar from "@/Components/Map/SideViewCalendar.vue";

  const props = defineProps({
    company: Object as PropType<Company> | null,
    bounds: Object as PropType<Bounds> | null,
    routes: Array as PropType<Route[]> | null,
    trips: Array as PropType<Trip[]> | null,
    trip_highlights: Array as PropType<TripHighlight[]> | null,
    countries: Object as PropType<Record<string, string>> | null,
    meta: Object as PropType<Record<string, any>>,
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
    window.addEventListener('popstate', onBack);

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
    // darkMap.value = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}.png');

    map.value.addLayer(lightMap.value);
    // map.value.addLayer(themeStore.isDark ? darkMap.value : lightMap.value);

    fitBounds(mapStore.route?.bounds ?? props.bounds);
  });

  onUnmounted(() => {
    window.removeEventListener('resize', onResize);
    window.removeEventListener('popstate', onBack);
  });

  function onBack(event: PopStateEvent) {
    event.preventDefault();

    try {
      const selections = event.state ? JSON.parse(event.state) : null;

      if (selections) {
        mapStore.setSelections(selections);
      }
    } catch {
      //
    }
  }

  function onResize() {
    console.log('onResize', window.innerWidth);

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

  function urlWithFilters(page: number = 1, size: number = null) {
    const params = [];

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

    if (mapStore.filters.route) {
      params.push(`route=${mapStore.filters.route}`);
    }

    if (mapStore.filters.trip) {
      params.push(`trip=${mapStore.filters.trip}`);
    }

    if (mapStore.filters.has_trips) {
      params.push(`has_trips=1`);
    }

    if (page > 1) {
      params.push(`page[number]=${page}`);
    }

    if (size > 0) {
      params.push(`page[size]=${size}`);
    }

    let url = '/' + props.company?.abbreviation + '/map';

    if (params.length) {
      url += `?${params.join('&')}`;
    }

    return url;
  }

  function reloadWithFilters() {
    useForm().get(urlWithFilters());
  }

  function applyCalendar({beg, end}: {beg: dayjs.Dayjs | null, end: dayjs.Dayjs | null}) {
    mapStore.filters.beg = beg;
    mapStore.filters.end = end;

    closeCalendar();
    reloadWithFilters();
  }

  function changePage(page: number) {
    useForm().get(urlWithFilters(page, props.meta?.per_page));
  }

  function toggleHasTrips(hasTrips: boolean) {
    mapStore.filters.has_trips = !hasTrips;
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

    if (mapStore.filters.has_trips) {
      mapStore.filters.has_trips = false;
      shouldReload = true;
    }

    if (props.meta?.current_page !== 1) {
      shouldReload = true;
    }

    if (shouldReload) {
      useForm().get(urlWithFilters(1));
    }
  }

  function selectRoute(route: Route | null) {
    if (mapStore.route !== route) {
      mapStore.route = route;
      mapStore.filters.route = route?.id ?? null;
      mapStore.selections.route = route;

      const params = new URLSearchParams(window.location.search);

      if (route) {
        params.set('route', route.id);
      } else {
        params.delete('route');
      }

      const url = '/' + props.company?.abbreviation + '/map' + '?' + params.toString();

      if (!window.location.href.endsWith(url)) {
        window.history.pushState(JSON.stringify(mapStore.selections), '', url);
      }
    }
  }

  function selectTrip(trip: Trip | null) {
    if (mapStore.trip !== trip) {
      mapStore.trip = trip;
      mapStore.filters.trip = trip?.id ?? null;
      mapStore.selections.trip = trip;

      const params = new URLSearchParams(window.location.search);

      if (trip) {
        params.set('trip', trip.id);
      } else {
        params.delete('trip');
      }

      const url = '/' + props.company?.abbreviation + '/map' + '?' + params.toString();

      if (!window.location.href.endsWith(url)) {
        window.history.pushState(JSON.stringify(mapStore.selections), '', url);
      }
    }
  }

  function routeClicked(route: Route) {
    console.log('route clicked', route);

    selectRoute(route);
    fitBounds(route.bounds ?? props.bounds);
  }

  function routeClosed(route: Route) {
    console.log('route closed', route);

    selectRoute(null);
    fitBounds(props.bounds);
  }

  function tripClicked(trip: Trip) {
    selectTrip(trip);
  }

  function tripClosed(trip: Trip) {
    selectTrip(null);
  }

  function toggleMap() {
    isShowingMap.value = !isShowingMap.value;

    if (map.value) {
      setTimeout(() => {
        map.value.invalidateSize();
      }, 250);
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

  // watch(
  //   () => themeStore.isDark,
  //   (newValue, oldValue) => {
  //     if (newValue !== oldValue) {
  //       if (map.value) {
  //         setTimeout(() => {
  //           map.value.removeLayer(oldValue ? darkMap.value : lightMap.value);
  //           map.value.addLayer(newValue ? darkMap.value : lightMap.value);
  //         });
  //       }
  //     }
  //   });

  watch(
    () => mapStore.routeEvents.clicks,
    (newValue, oldValue) => {
      if (newValue !== oldValue) {
        if (map.route !== mapStore.routeEvents.route) {
          selectRoute(mapStore.routeEvents.route);
        }
      }
    });
</script>

<template>
  <div id="app" class="h-screen overflow-hidden" :class="themeStore.isDark ? 'bg-gray-900' : 'bg-gray-50'">
    <!-- Header -->
    <header class="shadow-sm border-b" :class="themeStore.isDark ? 'bg-gray-900 border-gray-700' : 'bg-white border-gray-200'">
      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-purple-600 rounded flex items-center justify-center">
            <Truck class="w-5 h-5 text-white" />
          </div>
          <div>
            <h1 class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ props.company?.name || 'Haul Auto' }}</h1>
            <p class="text-xs text-gray-500">{{ props.company?.abbreviation || 'haul-auto' }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <!-- Theme toggle button -->
          <button
            @click="themeStore.toggle"
            class="p-2 rounded flex items-center gap-2 text-sm"
            :class="themeStore.isDark ? 'hover:bg-gray-800 text-gray-300' : 'hover:bg-gray-100 text-gray-700'"
          >
            <Moon v-if="!themeStore.isDark" class="w-4 h-4" />
            <Sun v-else class="w-4 h-4" />
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex h-[calc(100vh-64px)] relative">
      <!-- Side Panel -->
      <div
        :class="[
          'transition-all duration-300 flex flex-col border-r inset-y-0 left-0 z-20 overflow-auto overflow-x-hidden',
          isShowingMap ? 'w-0 overflow-hidden lg:w-0' : isNarrowScreen ? 'absolute w-full max-w-[100%] w-[100%]' : 'relative w-[400px] max-w-[400px]',
          themeStore.isDark ? 'bg-gray-900 border-gray-700' : 'bg-white border-gray-200'
        ]"
      >
        <template v-if="isShowingCalendar">
          <SideViewCalendar class="w-full h-full"
                            :months="5"
                            :beg="mapStore.filters.beg"
                            :end="mapStore.filters.end"
                            :dotted-dates="trip_highlights"
                            @apply-calendar="applyCalendar"
                            @close-calendar="closeCalendar"/>
        </template>

        <template v-else-if="isShowingFrom">
          <div class="w-full px-4 py-4">
            <SideViewFrom title="Countries"
                          :from="mapStore.filters.from?.length ? mapStore.filters.from.split(',') : []"
                          :countries="props.countries"
                          @apply-from="applyFrom"
                          @close-from="closeFrom"/>
          </div>
        </template>

        <template v-else>
          <SideView class="w-full"
                    :company="props.company"
                    :routes="props.routes"
                    :trips="props?.trips"
                    :meta="props.meta"
                    :countries="props.countries"
                    @change-page="changePage"
                    @toggle-has-trips="toggleHasTrips"
                    @open-from="openFrom"
                    @open-calendar="openCalendar"
                    @clear-filters="clearFilters"
                    @route-clicked="routeClicked"
                    @route-closed="routeClosed"
                    @trip-clicked="tripClicked"
                    @trip-closed="tripClosed"
                    @toggle-map="toggleMap"/>
        </template>
      </div>

      <!-- Map Area -->
      <div id="map" class="block flex-1 relative bg-gray-100 z-[1]">
        <slot/>

        <!-- Desktop Collapse button -->
        <button
          @click="toggleMap"
          class="absolute top-4 left-4 bg-purple-600 text-white px-3 py-2 rounded text-sm z-[1000]"
          :class="isNarrowScreen ? 'hidden' : ''"
        >
          {{ isShowingMap ? '»' : '«' }} {{ isShowingMap ? 'Expand' : 'Collapse' }}
        </button>

        <div class="left-2 right-2 bottom-2 pt-2 pl-4 pr-2 pb-18 rounded-lg absolute z-[1000]"
             :class="themeStore.isDark ? 'bg-gray-900/80 border-gray-700 text-gray-300' : 'bg-white/80 border-gray-200 text-gray-700'"
             v-if="isNarrowScreen && isShowingMap && mapStore.route">
           <div class="flex justify-between gap-4">
             <h4 class="font-semibold text-xl">{{ mapStore.route.name }}</h4>
             <X @click="routeClosed(mapStore.route)" class="w-7 h-7 p-1 pr-0 pt-0" />
           </div>

          <div class="flex items-center gap-4 mt-2 text-sm">
            <Deferred data="trips">
              <template #fallback>
                <span><span class="loading loading-dots loading-xs mr-1"/>trips</span>
              </template>

              <span v-if="trips?.filter(trip => trip.route_id === mapStore.route.id)?.length">
                {{ trips?.filter(trip => trip.route_id === mapStore.route.id)?.length }} {{ trips?.filter(trip => trip.route_id === mapStore.route.id)?.length > 1 ? 'trips' : 'trip' }}
              </span>
              <span v-else>No trips</span>
            </Deferred>
            <span v-if="mapStore.route.points?.length">{{ mapStore.route.points?.length }} stops</span>
            <span v-if="mapStore.route.travel_time">{{ minutesToHumanReadable(mapStore.route.travel_time) }}</span>
          </div>
        </div>
      </div>

      <!-- Mobile Toggle Button -->
      <button
        @click="toggleMap"
        class="fixed bottom-4 left-4 right-4 bg-purple-600 text-white px-6 py-3 rounded-lg flex items-center gap-2 shadow-lg z-50 flex justify-center"
        :class="isNarrowScreen ? '' : 'hidden'"
        v-if="!isShowingCalendar"
      >
        <Menu class="w-4 h-4" v-if="isShowingMap" />
        <MapPin class="w-4 h-4" v-else />
        {{ isShowingMap ? (mapStore.trip ? 'Trip details' : (mapStore.route ? 'Route details' : 'Routes list')) : 'View on map' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
</style>
