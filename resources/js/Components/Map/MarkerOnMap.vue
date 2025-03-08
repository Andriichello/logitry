<script setup lang="ts">
  import L from 'leaflet';
  import {computed, onMounted, onUnmounted, PropType, ref, watch} from 'vue';

  const emits = defineEmits([
    'created',
    'removed',
    'clicked',
  ]);

  const props = defineProps({
    latitude: {
      type: Number as PropType<number>,
      required: true
    },
    longitude: {
      type: Number as PropType<number>,
      required: true
    },
    size: {
      type: Object as PropType<{
        width: number,
        height: number,
        radius: number,
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

  function makeDivIcon(width: number, height: number, radius: number, color: string) {
    const dotRadius = 2;
    const markerTipOffset = height / 2 - Math.sqrt(2) * (height / 2);

    let markerTranslateY =
      markerTipOffset +
      (radius - dotRadius) -
      Math.max(Math.floor(radius * 0.25), dotRadius);

    const iconWidth = Math.max(width, radius * 2);
    const iconHeight = height + radius * 2;

    const anchorX = width / 2;
    const anchorY = height + radius;

    return L.divIcon({
      iconSize: [iconWidth, iconHeight],
      iconAnchor: [anchorX, anchorY],
      className: 'bg-none',
      html: `
      <div class="flex flex-col-reverse justify-center items-center">
         <!-- Circle -->
        <div style="width:${radius * 2}px; height:${radius * 2}px"
             class="border-2 border-white rounded-full relative opacity-75">
          <div style="background-color:${color};" class="w-full h-full opacity-60 rounded-full"></div>
          <div class="absolute w-[${dotRadius}px] h-[${dotRadius}px] bg-white rounded-full top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
        </div>

        <div style="transform: translateY(${markerTranslateY}px);">
           <!-- Marker -->
          <div style="width:${width}px; height:${height}px; background-color:${color};"
               class="border-2 border-white rounded-full rounded-br-none rotate-45">
            <span class="color-white rotate-45"></span>
          </div>
        </div>
      </div>
    `,
    });
  }

  const marker = ref(null as L.Marker | null);
  const divIcon = ref(
    makeDivIcon(
      props.size.width,
      props.size.height,
      props.size.radius,
      props.color,
    )
  );

  watch(() => props.size, (newValue, oldValue) => {
    if (!marker.value || JSON.stringify(newValue) === JSON.stringify(oldValue)) {
      return;
    }

    divIcon.value = makeDivIcon(
      newValue.width,
      newValue.height,
      newValue.radius,
      props.color,
    );

    marker.value.setIcon(divIcon.value);
  });

  watch(() => props.selected, (value, oldValue) => {
    if (value === oldValue || !marker.value) {
      return;
    }

    if (value) {
      marker.value.setOpacity(1);

      if (!marker.value.isPopupOpen()) {
        // marker.value.openPopup();
      }
    } else {
      marker.value.setOpacity(0.4);

      if (marker.value.isPopupOpen()) {
        marker.value.closePopup();
      }
    }
  })

  onMounted(() => {
    if (!marker.value) {
      const label = props.label;

      marker.value = L.marker(
        [props.latitude, props.longitude],
        {icon: divIcon.value}
      )
        .setOpacity(props.selected ? 1 : 0.4)
        // .bindPopup(label, {
        //   closeOnClick: false,
        //   autoClose: false,
        //   autoPan: false,
        //   closeButton: false,
        // })
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
