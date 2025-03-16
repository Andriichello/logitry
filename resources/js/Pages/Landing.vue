<script lang="ts">
  import LandingLayout from '@/Layouts/LandingLayout.vue';

  export default {
    layout: LandingLayout,
  };
</script>

<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import L from 'leaflet';
  import { ChevronRight } from 'lucide-vue-next';
  import { router } from '@inertiajs/vue3';
  import { onMounted, onUnmounted, PropType, ref } from 'vue';
  import { useMapStore } from '@/stores/map';
  import { Bounds, Company, Trip } from '@/api';
  import RouteOnMap from '@/Components/Map/RouteOnMap.vue';
  import { useToast } from 'vue-toastification';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    routes: Array as PropType<Route[]> | null,
    bounds: Object as PropType<Bounds> | null,
  });

  const toast = useToast();
  const mapStore = useMapStore();

  const map = ref(null as L.Map | null);

  const darkMap = ref(null);
  const lightMap = ref(null);

  const isNarrowScreen = ref(false);

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
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-start bg-base-200 pb-20">
    <div class="hero py-3">
      <div class="hero-content text-center">
        <div class="max-w-md">
          <h1 class="text-5xl font-bold">We’ll Call You!</h1>
          <p class="py-6 text-lg opacity-80">
            Leave your details, and our manager will contact you to discuss route options.
            No upfront commitment - just a quick call to find the best route for you.
          </p>
          <button class="w-full btn btn-lg btn-success"
                  @click="toast.info('Not implemented yet', { timeout: 2000 })">
            Talk to a Manager
          </button>
        </div>
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center py-6 px-4 bg-base-300">
      <div class="w-full max-w-md flex flex-col justify-center items-center">
        <div id="map" class="w-full min-h-[200px] rounded-lg"
          style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
          <template v-for="route in (props.routes ?? [])" :key="route.id" v-if="map">
            <RouteOnMap :route="route"
                        :map="map"
                        :selected="true"/>
          </template>
        </div>

        <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 gap-4 font-mono">
          <div class="w-full flex flex-col justify-start items-start gap-2">
            <h3 class="text-xl font-semibold">Our routes</h3>
            <p class="text-md opacity-80">
              Map preview of our most popular routes.
            </p>
          </div>

          <div class="btn btn-lg btn-outline flex flex-row justify-center items-center gap-1"
               @click="router.visit(`/${props.company?.abbreviation}/map`)">
            <ChevronRight class="w-5 h-5 pb-0.5 opacity-0"/>
            <span>View Routes</span>
            <ChevronRight class="w-5 h-5 pb-0.5"/>
          </div>
        </div>
      </div>

<!--      <div class="w-full max-w-2xl flex flex-row justify-start items-center rounded-xl bg-base-100 shadow-md hover:shadow-xl">-->
<!--        <div id="map" class="min-w-2/5 w-full h-full max-w-sm min-h-[160px] rounded-lg"-->
<!--             style="border-top-right-radius: 0; border-bottom-right-radius: 0;">-->
<!--          <template v-for="route in (props.routes ?? [])" :key="route.id" v-if="map">-->
<!--            <RouteOnMap :route="route"-->
<!--                        :map="map"-->
<!--                        :selected="true"/>-->
<!--          </template>-->
<!--        </div>-->

<!--        <div class="max-w-sm flex flex-col justify-between items-between rounded-right rounded-xl px-3 py-3 gap-6 font-mono">-->
<!--          <div class="w-full flex flex-col justify-start items-start gap-3">-->
<!--            <h3 class="text-xl font-semibold">Our routes</h3>-->
<!--            <p class="text-md text-gray-600">-->
<!--              Map preview of our routes. Here are only the most popular ones.-->
<!--            </p>-->
<!--          </div>-->

<!--          <div class="btn btn-soft flex flex-row justify-center items-center gap-1">-->
<!--            <span>View Routes</span>-->
<!--            <ChevronRight class="w-4 h-4 pb-0.5"/>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->


<!--      <div class="w-full max-w-2xl flex flex-row flex-wrap justify-start items-center bg-base-100 rounded-xl shadow-md hover:shadow-xl">-->
<!--        <div id="map" class="w-1/2 sm:w-full lg:w-3/5 h-full min-h-[160px] rounded-left rounded-lg rounded-r-none">-->
<!--          <template v-for="route in (props.routes ?? [])" :key="route.id" v-if="map">-->
<!--            <RouteOnMap :route="route"-->
<!--                        :map="map"-->
<!--                        :selected="true"/>-->
<!--          </template>-->
<!--        </div>-->
<!---->
<!--        <div class="w-1/2 sm:w-full lg:w-2/5 h-full flex flex-col justify-between items-between rounded-right rounded-xl px-3 py-3 font-mono">-->
<!--          <div class="w-full flex flex-col justify-start items-start gap-3">-->
<!--            <h3 class="text-xl font-semibold">Our routes</h3>-->
<!--            <p class="text-md text-gray-600">-->
<!--              Map preview of our routes. Here are only the most popular ones.-->
<!--            </p>-->
<!--          </div>-->

<!--          <div class="btn btn-soft  w-full flex flex-row justify-center items-center mt-5 gap-1">-->
<!--            <span>View Routes</span>-->
<!--            <ChevronRight class="w-4 h-4 pb-0.5"/>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
    </div>

  </div>
</template>
