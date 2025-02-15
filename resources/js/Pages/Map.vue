<script lang="ts">
  import MapLayout from "@/Layouts/MapLayout.vue";

  export default {
    layout: MapLayout,
  };
</script>

<script setup lang="ts">
  import L from "leaflet";
  import 'leaflet/dist/leaflet.css';
  import {onMounted, PropType} from "vue";
  import {Company} from "@/api";

  const props = defineProps({
    company: Object as PropType<Company> | null,
    trips: Array as PropType<Object[]> | null,
  });

  function scaleIconForMarker(marker, factor) {
    const iconOptions = marker.options.icon.options;

    function multiplyPointBy(factor, point) {
      return L.point(
        point[0] * factor,
        point[1] * factor
      );
    }

    return L.icon({
      iconUrl: iconOptions.iconUrl,
      iconSize: multiplyPointBy(factor, iconOptions.iconSize),
      iconAnchor: multiplyPointBy(factor, iconOptions.iconAnchor),
    });
  }

  onMounted(() => {
    const map = L.map('map', {zoomControl: false});

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const p = props.trips?.[0]?.points?.[0];

    const center = p
      ? [p.latitude, p.longitude]
      : [51.505, -0.09];

    map.setView(center, 6);

    for (const trip of props.trips ?? []) {
      const line = trip?.points.map((p) => [p.latitude, p.longitude]);
      L.polyline(line).addTo(map)

      for (const point of trip?.points ?? []) {
        const coordinates = [point.latitude, point.longitude];
        const marker = L.marker(coordinates).addTo(map);
        // marker.setIcon(scaleIconForMarker(marker, 0.8));
      }
    }
  });
</script>


<template>
  <div class="w-full h-full">
    <div id="map" class="w-full h-full"></div>
  </div>
</template>

<style scoped>
  #map {
    width: 100%;
    height: 100vh;
    min-height: 100vh;
  }
</style>
