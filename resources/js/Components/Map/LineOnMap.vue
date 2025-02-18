<script setup lang="ts">
  import L from 'leaflet';
  import { onMounted, onUnmounted, PropType, ref, watch } from 'vue';

  const emits = defineEmits([
    'created',
    'removed',
    'clicked',
  ]);

  const props = defineProps({
    points: {
      type: Array as PropType<{
        latitude: number,
        longitude: number,
      }[]>,
      required: true
    },
    color: {
      type: String as string,
      default: 'blue'
    },
    label: String as string | null,
    selected: {
      type: Boolean as boolean,
      default: false,
    },
  });

  const line = ref(null as L.Polyline | null);

  watch(() => props.selected, (value, oldValue) => {
    if (value === oldValue || !line.value) {
      return;
    }

    if (value) {
      line.value.setStyle({ opacity: 1 });
    } else {
      line.value.setStyle({ opacity: 0.4 });
    }
  })

  onMounted(() => {
    if (!line.value) {
      const options = {  };
      const positions = props.points.map((p) => [p.latitude, p.longitude]);

      line.value = L.polyline(positions, options)
        .setStyle({
          weight: 6,
          opacity: props.selected ? 1 : 0.4,
        })
        .on('click', (event) => {
          L.DomEvent.stopPropagation(event);

          if (line.value) {
            emits('clicked', {
              line: line.value,
              isPopupOpen: line.value.isPopupOpen()
            });
          }
      });

      emits('created', line.value);
    }
  });

  onUnmounted(() => {
    if (line.value) {
      emits('removed', line.value);
      line.value = null;
    }
  });
</script>


<template>
  <!-- Should be hidden -->
  <div class="hidden"/>
</template>
