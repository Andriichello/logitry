<script setup lang="ts">
  import { onMounted, onUnmounted, ref, PropType, nextTick } from 'vue';
  import L from "leaflet";
  import { Route, Point } from '@/api';
  import LineOnMap from "./LineOnMap.vue";
  import MarkerOnMap from "./MarkerOnMap.vue";
  import {MapSizes, useMapStore} from "@/stores/map";

  const emits = defineEmits(['line-clicked', 'marker-clicked']);

  const props = defineProps({
    map: {
      type: Object as PropType<L.Map>,
      required: true,
    },
    route: {
      type: Object as PropType<Route>,
      required: true,
    },
    selected: {
      type: Boolean as PropType<boolean>,
      default: false,
    },
  });

  const mapStore = useMapStore();

  const group = ref<L.LayerGroup | null>(null);
  const line = ref<L.Polyline | null>(null);
  const markers = ref<L.Marker[]>([]);

  function lineCreated(l: L.Polyline) {
    // console.log("lineCreated:", l);
    line.value = l;

    if (group.value) {
      group.value.addLayer(l);
    }
  }

  function lineRemoved(l: L.Polyline) {
    // console.log("lineRemoved:", l);

    if (group.value) {
      group.value.removeLayer(l);
    }

    line.value = null;
  }

  function markerCreated(m: L.Marker) {
    // console.log("markerCreated:", m);
    markers.value.push(m);

    if (group.value) {
      group.value.addLayer(m);
    }
  }

  function markerRemoved(m: L.Marker) {
    // console.log("markerRemoved:", m);

    const index = markers.value.indexOf(m);
    if (index !== -1) {
      markers.value.splice(index, 1);

      if (group.value) {
        group.value.removeLayer(m);
      }
    }
  }

  function lineClicked(
    { line, isPopupOpen }:
    { line: L.Polyline, isPopupOpen: boolean }
  ) {
    console.log('Line clicked');
    emits('line-clicked', props.route);
  }

  function markerClicked(
    { marker, isPopupOpen }:
    { marker: L.Marker, isPopupOpen: boolean }
  ) {
    console.log('Marker clicked');
    emits('marker-clicked', props.route);
  }

  function labelForPoint(point: Point) {
    const parts = [];

    if (point.city) {
      parts.push(
        `<span class="text-xl font-bold">${point.city}</span>`,
      );
    }

    return parts.join('<br>');
  }

  onMounted(() => {
    // props.map.on("zoomend", () => {
    //   markers.value?.forEach((marker) => {
    //     if (marker.getPopup()?.isOpen()) {
    //       marker.getPopup().update(); // Ensures the popup stays correctly positioned
    //     }
    //   });
    // });

    // props.map.on("moveend", () => {
    //   markers.value?.forEach((marker) => {
    //     if (marker.getPopup()?.isOpen()) {
    //       marker.getPopup().update();
    //     }
    //   });
    // });

    // console.log("TripOnMap.onMounted:", props.trip);

    if (!props.map || !props.route) return;

    group.value = L.layerGroup().addTo(props.map);

    // console.log("TripOnMap: Group added to map");

    // Add line if already created
    if (line.value) {
      group.value.addLayer(line.value);
    }

    // Add markers if already created
    for (const marker of markers.value) {
      group.value.addLayer(marker);
    }
  });

  onUnmounted(() => {
    if (group.value && props.map) {
      // console.log("TripOnMap.onUnmounted: Removing group from map");
      props.map.removeLayer(group.value);
    }

    if (line.value) {
      line.value = null;
    }

    if (markers.value) {
      markers.value = [];
    }
  });
</script>

<template>
  <div>
    <template v-if="group">
      <LineOnMap
        :size="{width: mapStore.scaledSizes.lineWidth}"
        :points="route.points"
        :selected="selected"
        color="blue"
        @created="lineCreated"
        @removed="lineRemoved"
        @clicked="lineClicked"
      />

      <template v-for="point in route.points" :key="point.id">
        <MarkerOnMap
          :size="{
            width: mapStore.scaledSizes.markerWidth,
            height: mapStore.scaledSizes.markerHeight,
            radius: mapStore.scaledSizes.circleRadius,
          }"
          :latitude="point.latitude"
          :longitude="point.longitude"
          :label="labelForPoint(point)"
          :selected="selected"
          @created="markerCreated"
          @removed="markerRemoved"
          @clicked="markerClicked"
        />
      </template>
    </template>
  </div>
</template>
