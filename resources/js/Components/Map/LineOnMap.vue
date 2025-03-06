<script setup lang="ts">
  import L from 'leaflet';
  import { onMounted, onUnmounted, PropType, ref, watch } from 'vue';
  import {Point} from "@/api";

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
      }[]> | PropType<Point[]>,
      required: true
    },
    size: {
      type: Object as PropType<{
        width: number,
      }>,
      required: true,
    },
    color: {
      type: String as PropType<string>,
      default: 'blue'
    },
    label: String as PropType<string> | null,
    selected: {
      type: Boolean as PropType<boolean>,
      default: false,
    },
  });

  const line = ref(null as L.Polyline | null);

  watch(() => props.size, (newValue, oldValue) => {
    if (!line.value || JSON.stringify(newValue) === JSON.stringify(oldValue)) {
      return;
    }

    line.value.setStyle({ weight: newValue.width });
  });

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
          weight: props.size.width,
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
