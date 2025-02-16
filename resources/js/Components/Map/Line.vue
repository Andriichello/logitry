<script setup lang="ts">
  import { onUnmounted, PropType, ref, watch } from 'vue';

  const props = defineProps({
    map: {
      type: Object as L.Map | null,
      required: true
    },
    points: {
      type: Array as PropType<{
        latitude: number,
        longitude: number,
      }[]>,
      required: true
    },
    color: {
      type: String as string,
      default: 'black'
    },
    label: String as string | null,
  });

  const line = ref(null as L.Polyline | null);

  // Watch for `map` being set before adding the line
  watch(
    () => props.map,
    (map) => {

      if (map && !line.value) {
        const options = { color: props.color };
        const positions = props.points.map((p) => [p.latitude, p.longitude]);

        line.value = L.polyline(positions, options)
          .addTo(props.map)
          .bindPopup(props.label ?? 'default');
      }
    },
    { immediate: true } // Run this watcher immediately
  );

  onUnmounted(() => {
    if (props.map && line.value) {
      props.map.removeLayer(line.value);
      line.value = null;
    }
  });
</script>


<template>
  <!-- Should be hidden -->
  <div class="hidden"/>
</template>
