<script setup lang="ts">
  import { onMounted, onUnmounted, ref, PropType, nextTick } from 'vue';
  import L from "leaflet";
  import { Trip } from "@/api";
  import LineOnMap from "./LineOnMap.vue";
  import MarkerOnMap from "./MarkerOnMap.vue";

  const props = defineProps({
    map: {
      type: Object as PropType<L.Map>,
      required: true,
    },
    trip: {
      type: Object as PropType<Trip>,
      required: true,
    },
  });

  const group = ref<L.LayerGroup | null>(null);
  const line = ref<L.Polyline | null>(null);
  const markers = ref<L.Marker[]>([]);

  const ignoreMarkerClick = ref(false);
  const ignoreMarkerPopup = ref(false);

  onMounted(() => {
    props.map.on("click", () => {
      console.log("Map clicked");

      ignoreMarkerClick.value = true;
      ignoreMarkerPopup.value = true;

      for (const m of markers.value) {
        m.setOpacity(0.6);
        m.closePopup();
      }

      ignoreMarkerClick.value = false;
      ignoreMarkerPopup.value = false;
    });

    props.map.on("zoomend", () => {
      markers.value?.forEach((marker) => {
        if (marker.isPopupOpen()) {
          marker.setLatLng(marker.getLatLng()); // Force popup to reposition
        }
      });
    });

    props.map.on("moveend", () => {
      markers.value?.forEach((marker) => {
        if (marker.isPopupOpen()) {
          nextTick();
          marker.update(); // Update the popup position
        }
      });
    });

    console.log("TripOnMap.onMounted:", props.trip);

    if (!props.map || !props.trip) return;

    group.value = L.layerGroup().addTo(props.map);

    console.log("TripOnMap: Group added to map");

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
      console.log("TripOnMap.onUnmounted: Removing group from map");
      props.map.removeLayer(group.value);
    }

    if (line.value) {
      line.value = null;
    }

    if (markers.value) {
      markers.value = [];
    }
  });

  function lineCreated(l: L.Polyline) {
    console.log("lineCreated:", l);
    line.value = l;

    if (group.value) {
      group.value.addLayer(l);
    }
  }

  function lineRemoved(l: L.Polyline) {
    console.log("lineRemoved:", l);

    if (group.value) {
      group.value.removeLayer(l);
    }

    line.value = null;
  }

  function markerCreated(m: L.Marker) {
    console.log("markerCreated:", m);
    markers.value.push(m);

    if (group.value) {
      group.value.addLayer(m);
    }
  }

  function markerRemoved(m: L.Marker) {
    console.log("markerRemoved:", m);

    const index = markers.value.indexOf(m);
    if (index !== -1) {
      markers.value.splice(index, 1);

      if (group.value) {
        group.value.removeLayer(m);
      }
    }
  }

  function markerClicked(
    { marker, isPopupOpen }:
    { marker: L.Marker, isPopupOpen: boolean }
  ) {
    if (ignoreMarkerClick.value) {
      return;
    }

    ignoreMarkerClick.value = true;
    ignoreMarkerPopup.value = true;

    console.log('Marker clicked');
    const opacity = isPopupOpen ? 1 : 0.6;

    marker.setOpacity(opacity);

    for (const m of markers.value) {
      m.setOpacity(opacity);

      if (m === marker) {
        continue;
      }

      if (isPopupOpen) {
        m.openPopup();
      } else {
        m.closePopup();
      }
    }

    ignoreMarkerClick.value = false;
    ignoreMarkerPopup.value = false;
  }

  function markerPopupClosed(
    { marker, isPopupOpen }:
    { marker: L.Marker, isPopupOpen: boolean }
  ) {
    if (ignoreMarkerPopup.value) {
      return;
    }

    ignoreMarkerClick.value = true;
    ignoreMarkerPopup.value = true;

    console.log('Marker popup closed')
    const opacity = isPopupOpen ? 1 : 0.6;

    marker.setOpacity(opacity);

    for (const m of markers.value) {
      m.setOpacity(opacity);

      if (m === marker) {
        continue;
      }

      if (isPopupOpen) {
        m.openPopup();
      } else {
        m.closePopup();
      }
    }

    ignoreMarkerClick.value = false;
    ignoreMarkerPopup.value = false;
  }
</script>

<template>
  <div>
    <template v-if="group">
      <LineOnMap
        :points="trip.points"
        color="black"
        @created="lineCreated"
        @removed="lineRemoved"
        @clicked="console.log('Line clicked', $event)"
      />

      <template v-for="point in trip.points" :key="point.id">
        <MarkerOnMap
          :latitude="point.latitude"
          :longitude="point.longitude"
          :label="point?.city ?? ('Point: ' + point.id)"
          @created="markerCreated"
          @removed="markerRemoved"
          @clicked="markerClicked"
          @popup-closed="markerPopupClosed"
        />
      </template>
    </template>
  </div>
</template>
