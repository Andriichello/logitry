<script setup lang="ts">
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
      default: 'black'
    },
    label: String as string | null,
  });

  const line = ref(null as L.Polyline | null);

  onMounted(() => {
    if (!line.value) {
      const options = { color: props.color };
      const positions = props.points.map((p) => [p.latitude, p.longitude]);

      line.value = L.polyline(positions, options)
        .bindPopup(props.label ?? 'default')
        .on('click', () => {
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
