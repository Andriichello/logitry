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
  import { MapFilters, MapSelections, useMapStore } from '@/stores/map';
  import { Company, Trip } from '@/api';
  import { Route } from '@/api';
  import RouteOnMap from '@/Components/Map/RouteOnMap.vue';
  import { POSITION, useToast } from 'vue-toastification';

  const props = defineProps({
    company: Object as PropType<Company> | null,
    routes: Array as PropType<Route[]> | null,
    trips: Array as PropType<Trip[]> | null,
    filters: Object as PropType<MapFilters> | null,
    selections: Object as PropType<MapSelections> | null,
  });

  const toast = useToast();

  const mapStore = useMapStore();
  mapStore.setFilters(props.filters);
  mapStore.setSelections(props.selections);

  if (mapStore.filters.trip && !mapStore.trip) {
    setTimeout(() => toast.error('No such trip found.'), 100);
  } else if (mapStore.filters.route && !mapStore.route) {
    setTimeout(() => toast.error('No such route found.'), 100);
  } else if (mapStore.filters.trip && !mapStore.trip) {
    setTimeout(() => toast.error('No such trip found.'), 100);
  } else if (mapStore.filters.route && !mapStore.route) {
    setTimeout(() => toast.error('No such route found.'), 100);
  }

  const map = inject<L.Map>('map');

  function markerClicked(route: Route) {
    if (mapStore.route?.id === route.id) {
      // mapStore.route = null;
    } else {
      if (!mapStore.trip) {
        mapStore.routeEvents.route = route;
        mapStore.routeEvents.clicks++;
        // mapStore.route = route;
        // mapStore.filters.route = route.id;
      }
    }
  }

  function lineClicked(route: Route) {
    if (mapStore.route?.id === route.id) {
      // mapStore.route = null;
    } else {
      mapStore.routeEvents.route = route;
      mapStore.routeEvents.clicks++;
      // mapStore.route = route;
      // mapStore.filters.route = route.id;
    }
  }

  // watch(() => mapStore.clicks, (newValue, oldValue) => {
  //   if (newValue !== oldValue) {
  //     mapStore.route = null;
  //   }
  // });

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
                  :hidden="mapStore.route ? mapStore.route?.id !== route.id : false"
                  @marker-clicked="markerClicked"
                  @line-clicked="lineClicked"/>
    </template>
  </div>
</template>
