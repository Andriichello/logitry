<script lang="ts">
  import MapLayout from "@/Layouts/MapLayout.vue";

  export default {
    layout: MapLayout,
  };
</script>

<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import { ref, inject, PropType, watch } from 'vue';
  import { Company } from "@/api";
  import Marker from '../Components/Map/Marker.vue';
  import { Trip } from '../api';
  import Line from '../Components/Map/Line.vue';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    trips: Array as PropType<Trip[]> | null,
  });

  const map = inject('map', ref(null));
</script>

<template>
  <div>
    <template v-for="trip in trips" :key="trip.id" v-if="map">
      <Line :map="map"
            :points="trip.points"
            :color="'black'"/>

      <template v-for="point in trip.points" :key="point.id">
        <Marker :map="map"
                :latitude="point.latitude"
                :longitude="point.longitude"
                :label="point?.city ?? ('Point: ' + point.id)"/>
      </template>
    </template>
  </div>
</template>
