<script lang="ts">
  import LandingLayout from '@/Layouts/LandingLayout.vue';

  export default {
    layout: LandingLayout,
  };
</script>

<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import L from 'leaflet';
  import { router } from '@inertiajs/vue3';
  import { onMounted, onUnmounted, PropType, ref } from 'vue';
  import { useMapStore } from '@/stores/map';
  import { useThemeStore } from '@/stores/theme';
  import {Bounds, Company, Route} from '@/api';
  import RouteOnMap from '@/Components/Map/RouteOnMap.vue';
  import { useToast } from 'vue-toastification';
  import { MapPin, ChevronRight } from 'lucide-vue-next';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    routes: Array as PropType<Route[]> | null,
    bounds: Object as PropType<Bounds> | null,
  });

  const toast = useToast();
  const mapStore = useMapStore();
  const themeStore = useThemeStore();

  const map = ref(null as L.Map | null);
  const darkMap = ref(null);
  const lightMap = ref(null);
  const isNarrowScreen = ref(false);
  const showMap = ref(false);

  onMounted(() => {
    onResize();

    window.addEventListener('resize', onResize);

    const center = [50.0755, 14.4378];

    map.value = L.map('map', { zoomControl: false, scrollWheelZoom: false })
      .on('click', () => {
        console.log('map click');
        mapStore.clicks++;
      })
      .on('zoomend', () => {
        mapStore.recalculateZoom(map.value ?? null)
      })
      .setView(center, 3);

    map.value.dragging.disable();
    map.value.touchZoom.disable();
    map.value.doubleClickZoom.disable();
    map.value.scrollWheelZoom.disable();
    map.value.boxZoom.disable();
    map.value.keyboard.disable();

    if (map.value.tap) {
      map.value.tap.disable();
    }

    lightMap.value = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png');
    // darkMap.value = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}.png');

    map.value.addLayer(lightMap.value);
    // map.value.addLayer(themeStore.isDark ? darkMap.value : lightMap.value);

    if (props.bounds) {
      fitBounds(props.bounds);
    }
  });

  onUnmounted(() => {
    window.removeEventListener('resize', onResize);
  });

  function onResize() {
    isNarrowScreen.value = window.innerWidth < 799;
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

  function toggleMap() {
    showMap.value = !showMap.value;
  }
</script>

<template>
  <div class="p-6 flex-1 overflow-y-auto">
    <div class="space-y-6">
      <!-- Map Preview -->
      <div class="bg-gray-100 rounded-lg p-4 h-48 flex items-center justify-center relative">
        <div id="map" class="absolute inset-0 rounded-lg">
          <template v-for="route in (props.routes ?? [])" :key="route.id" v-if="map">
            <RouteOnMap :route="route"
                        :map="map"
                        :selected="true"/>
          </template>
        </div>
        <div class="text-center">
          <MapPin class="w-8 h-8 text-purple-600 mx-auto mb-2" />
          <p class="text-sm" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-600'">Map preview of our most popular routes</p>
        </div>
      </div>

      <!-- Our Routes Section -->
      <div class="text-center">
        <h2 class="text-2xl font-bold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Our routes</h2>
        <p class="mb-4" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-600'">Map preview of our most popular routes.</p>
        <button
          @click="router.visit(`/${props.company?.abbreviation}/map`)"
          class="w-full border py-3 px-4 rounded-lg flex items-center justify-center gap-2"
          :class="themeStore.isDark
            ? 'bg-gray-800 border-gray-600 text-gray-200 hover:bg-gray-700'
            : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
        >
          View Routes
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>

      <!-- About Us -->
      <div>
        <h3 class="text-xl font-bold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">About Us</h3>
        <div class="space-y-3 text-sm" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-600'">
          <p>We are a company dedicated to providing reliable and efficient transportation services.</p>
          <p>With a focus on customer satisfaction, our goal is to offer seamless travel experiences for all our clients.</p>
          <p>From carefully planned routes to exceptional service, we strive to make your journey safe, comfortable, and enjoyable.</p>
        </div>
      </div>
    </div>
  </div>
</template>
