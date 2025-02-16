<script setup lang="ts">
  import { onMounted, onUnmounted, ref, watch } from 'vue';

  const emits = defineEmits(['created', 'removed']);

  const props = defineProps({
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

  onMounted(() => {
    if (!marker.value) {
      marker.value = L.marker([props.latitude, props.longitude])
        .bindPopup(props.label ?? 'default');

      emits('created', marker.value);
    }
  });

  onUnmounted(() => {
    if (marker.value) {
      emits('removed', marker.value);
      marker.value = null;
    }
  });
</script>


<template>
  <!-- Should be hidden -->
  <div class="hidden"/>
</template>
