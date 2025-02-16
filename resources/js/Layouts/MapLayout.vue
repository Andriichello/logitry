<script setup lang="ts">
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  import {useThemeStore} from "@/stores/theme";
  import { onMounted, provide, ref } from 'vue';

  const theme = useThemeStore();

  const map = ref(null as L.Map | null);
  // Provide map to all the pages
  provide('map', map);

  onMounted(() => {
    // const p = props?.trips?.[0]?.points?.[0];
    const p = null;
    const center = p ? [p.latitude, p.longitude] : [51.505, -0.09];

    map.value = L.map('map', {zoomControl: false})
      .setView(center, 6);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map.value);
  });
</script>

<template>
  <main>
    <article>
      <input type="checkbox" value="light" class="toggle theme-controller mt-1"
             :checked="!theme.isDark"
             @change="theme.toggle" hidden/>

      <div class="w-full h-full flex-col justify-start items-center">
        <div id="map">
          <slot/>
        </div>
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

