<script setup lang="ts">
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import { computed, onMounted, PropType, provide, ref } from 'vue';
  import { Bounds, Route } from '../api';
  import FitBoundsButton from '../Components/Map/FitBoundsButton.vue';
  import { useThemeStore } from "@/stores/theme";
  import { useMapStore } from '@/stores/map';

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

</script>

<template>
  <main>
    <article>
      <input type="checkbox" value="light" class="toggle theme-controller mt-1"
             :checked="!themeStore.isDark"
             @change="themeStore.toggle" hidden/>

      <div class="w-full h-full absolute">
        <div id="map">
          <slot/>
        </div>
        <FitBoundsButton class="absolute bottom-6 right-2 z-[400] opacity-75"
          @click="fitBounds"/>
      </div>
    </article>
  </main>
</template>

<style scoped>
  #map {
    width: 100%;
    height: 100vh;
    min-height: 100vh;
  }
</style>

