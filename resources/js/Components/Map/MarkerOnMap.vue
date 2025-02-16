<script setup lang="ts">
  import { onMounted, onUnmounted, ref, watch } from 'vue';

  const emits = defineEmits([
    'created',
    'removed',
    'clicked',
    'popup-closed',
  ]);

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
        .setOpacity(0.6)
        .bindPopup(props.label ?? 'default', { closeOnClick: false, autoClose: false, autoPan: false })
        .on('click', () => {
          if (marker.value) {
            emits('clicked', {
              marker: marker.value,
              isPopupOpen: marker.value.isPopupOpen()
            });
          }
        })
        .on('popupclose', () => {
          if (marker.value) {
            emits('popup-closed', {
              marker: marker.value,
              isPopupOpen: marker.value.isPopupOpen()
            });
          }
        });

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
