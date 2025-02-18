<script setup lang="ts">
  import { onMounted, onUnmounted, ref, watch } from 'vue';

  const emits = defineEmits([
    'created',
    'removed',
    'clicked',
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
      default: 'blue'
    },
    label: String as string | null,
    selected: {
      type: Boolean as boolean,
      default: false,
    },
  });

  const marker = ref(null as L.Marker | null);

  watch(() => props.selected, (value, oldValue) => {
    if (value === oldValue || !marker.value) {
      return;
    }

    if (value) {
      marker.value.setOpacity(1);

      if (!marker.value.isPopupOpen()) {
        marker.value.openPopup();
      }
    } else {
      marker.value.setOpacity(0.6);

      if (marker.value.isPopupOpen()) {
        marker.value.closePopup();
      }
    }
  })

  onMounted(() => {
    if (!marker.value) {
      const label = props.label;

      marker.value = L.marker([props.latitude, props.longitude])
        .setOpacity(props.selected ? 1 : 0.6)
        .bindPopup(
          label, {
          closeOnClick: false,
          autoClose: false,
          autoPan: false,
          closeButton: false,
        })
        .on('click', () => {
          if (marker.value) {
            emits('clicked', {
              marker: marker.value,
              isPopupOpen: marker.value.isPopupOpen(),
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
