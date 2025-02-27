<script lang="ts">
  import L from 'leaflet';
  import MapLayout from '@/Layouts/MapLayout.vue';

  export default {
    layout: MapLayout,
  };
</script>

<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import { inject, onMounted, PropType, watch } from 'vue';
  import {MapFilters, useMapStore} from '@/stores/map';
  import { Company, Trip } from '@/api';
  import { Route } from '@/api';
  import RouteOnMap from '@/Components/Map/RouteOnMap.vue';
  import { POSITION, useToast } from 'vue-toastification';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    routes: Array as PropType<Route[]> | null,
    trips: Array as PropType<Trip[]> | null,
    filters: Object as PropType<MapFilters> | null,
  });

  const toast = useToast();

  const mapStore = useMapStore();
  mapStore.init(props.filters);

  const map = inject<L.Map>('map');

  function markerClicked(route: Route) {
    if (mapStore.route?.id === route.id) {
      mapStore.route = null;
    } else {
      mapStore.route = route;
    }
  }

  function lineClicked(route: Route) {
    if (mapStore.route?.id === route.id) {
      mapStore.route = null;
    } else {
      mapStore.route = route;
    }
  }

  watch(() => mapStore.clicks, (newValue, oldValue) => {
    if (newValue !== oldValue) {
      mapStore.route = null;
    }
  });

  watch(
    () => props.company, (newValue, oldValue) => {
      if (!newValue) {
        setTimeout(
          () => {
            toast.error('No such company.', {
              position: POSITION.BOTTOM_LEFT,
              timeout: 3000,
            });
          },
          200,
        );
      }
    },
    { immediate: true },
  );

  watch(
    () => props.routes, (newValue, oldValue) => {
      if (props.company && newValue?.length === 0) {
        setTimeout(
          () => {
            toast.error('No routes found.', {
              position: POSITION.BOTTOM_LEFT,
              timeout: 3000,
            });
          },
          200,
        );
      }
    },
    { immediate: true },
  );
</script>

<template>
  <div>
    <template v-for="route in routes" :key="route.id" v-if="map">
      <RouteOnMap :map="map" :route="route"
        :selected="mapStore.route?.id === route.id"
        @marker-clicked="markerClicked"
        @line-clicked="lineClicked"/>
    </template>
  </div>
</template>
