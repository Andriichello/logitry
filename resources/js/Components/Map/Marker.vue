<script setup lang="ts">
  import { onUnmounted, ref, watch } from 'vue';

  const props = defineProps({
    map: {
      type: Object as L.Map | null,
      required: true
    },
    latitude: {
      type: Number as number,
      required: true
    },
    longitude: {
      type: Number as number,
      required: true
    },
    radius: {
      type: Number as number,
      default: 12
    },
    color: {
      type: String as string,
      default: 'black'
    },
    label: String as string | null,
  });

  const marker = ref(null as L.Marker | null);

  // Watch for `map` being set before adding the marker
  watch(
    () => props.map,
    (map) => {
      if (map && !marker.value) {
        const position = [props.latitude, props.longitude];
        marker.value = L.marker(position)
          .addTo(map)
          .bindPopup(props.label ?? 'default');
      }
    },
    { immediate: true } // Run this watcher immediately
  );

  onUnmounted(() => {
    if (props.map && marker.value) {
      props.map.removeLayer(marker.value);
      marker.value = null;
    }
  });
</script>


<template>
  <!-- Should be hidden -->
  <div class="hidden"/>
</template>
