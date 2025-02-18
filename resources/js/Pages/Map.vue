<script lang="ts">
  import MapLayout from '@/Layouts/MapLayout.vue';

  export default {
    layout: MapLayout,
  };
</script>

<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import { inject, onMounted, PropType, watch } from 'vue';
  import { useMapStore } from '@/stores/map';
  import { Company } from "@/api";
  import { Trip } from '@/api';
  import TripOnMap from '../Components/Map/TripOnMap.vue';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    trips: Array as PropType<Trip[]> | null,
  });

  const mapStore = useMapStore();

  const map = inject<L.Map>('map');

  function markerClicked(trip: Trip) {
    if (mapStore.trip?.id === trip.id) {
      mapStore.trip = null;
    } else {
      mapStore.trip = trip;
    }
  }

  function lineClicked(trip: Trip) {
    if (mapStore.trip?.id === trip.id) {
      mapStore.trip = null;
    } else {
      mapStore.trip = trip;
    }
  }

  watch(() => mapStore.clicks, (newValue, oldValue) => {
    if (newValue !== oldValue) {
      mapStore.trip = null;
    }
  })
</script>

<template>
  <div>
    <template v-for="trip in trips" :key="trip.id" v-if="map">
      <TripOnMap :map="map" :trip="trip"
        :selected="mapStore.trip?.id === trip.id"
        @marker-clicked="markerClicked"
        @line-clicked="lineClicked"/>
    </template>
  </div>
</template>
