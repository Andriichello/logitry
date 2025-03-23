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
  import { Bounds, Company } from '@/api';
  import RouteOnMap from '@/Components/Map/RouteOnMap.vue';
  import { useToast } from 'vue-toastification';
  import OutlineButton from '@/Components/Reusable/OutlineButton.vue';
  import TitleAndDescription from '@/Components/Landing/TitleAndDescription.vue';
  import SectionDivider from '@/Components/Landing/SectionDivider.vue';
  import Section from '@/Components/Landing/Section.vue';

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
  <div class="w-full h-full grow flex flex-col justify-start items-start pb-20">

    <Section class="shadow-sm">
      <TitleAndDescription>
        <template #title>
          Contact Me
        </template>

        <template #description>
          Fill out your details for our manager to contact you regarding pickup, destination, date, time, and seats.
        </template>
      </TitleAndDescription>

      <OutlineButton right-icon="ChevronRight" size="lg" class="w-full py-1"
                     @click="toast.info('Not implemented yet.', {timeout: 2000})">
        Contact Me
      </OutlineButton>
    </Section>

    <SectionDivider/>

    <Section class="bg-base-200/60 shadow-sm">
      <div id="map" class="w-full min-h-[200px] mb-3 rounded-lg shadow-lg"
           style="border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
        <template v-for="route in (props.routes ?? [])" :key="route.id" v-if="map">
          <RouteOnMap :route="route"
                      :map="map"
                      :selected="true"/>
        </template>
      </div>

      <TitleAndDescription>
        <template #title>
          Our routes
        </template>

        <template #description>
          Map preview of our most popular routes.
        </template>
      </TitleAndDescription>

      <OutlineButton right-icon="ChevronRight" size="lg" class="w-full py-1"
                     @click="router.visit(`/${props.company?.abbreviation}/map`)">
        View Routes
      </OutlineButton>
    </Section>

    <SectionDivider/>

    <Section>
      <TitleAndDescription>
        <template #title>
          About Us
        </template>

        <template #description>
          We are a company dedicated to providing reliable and efficient transportation services.
          <br><br>
          With a focus on customer satisfaction, our goal is to offer seamless travel experiences
          for all our clients.
          <br><br>
          From carefully planned routes to exceptional service, we strive to
          make your journey safe, comfortable, and enjoyable.
        </template>
      </TitleAndDescription>
    </Section>
  </div>
</template>
