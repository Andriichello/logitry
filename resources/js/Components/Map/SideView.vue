<script setup lang="ts">
  import { PropType } from 'vue';
  import { Company, Route, Trip } from '@/api';
  import CompanyInfo from '@/Components/Map/CompanyInfo.vue';
  import { useMapStore } from '@/stores/map';
  import SideViewRoutes from '@/Components/Map/SideViewRoutes.vue';
  import SideViewRoute from '@/Components/Map/SideViewRoute.vue';

  const emits = defineEmits([
    'open-from',
    'open-where',
    'swap-from-and-where',
    'open-calendar',
    'clear-filters',
    'route-clicked',
    'route-closed',
    'trip-clicked',
  ]);

  const props = defineProps({
    company: {
      type: Object as PropType<Company> | null,
      required: true,
    },
    routes: {
      type: Array as PropType<Route[]> | null,
      required: true,
    },
    trips: {
      type: Array as PropType<Trip[]> | null,
    },
    countries: {
      type: Object as PropType<Record<string, string>> | null,
    },
  });

  const mapStore = useMapStore();

  function openFrom() {
    emits('open-from');
  }

  function openWhere() {
    emits('open-where');
  }

  function openCalendar() {
    emits('open-calendar');
  }

  function clearFilters() {
    emits('clear-filters');
  }

  function routeClosed(route: Route) {
    emits('route-closed', route);
  }

  function routeClicked(route: Route) {
    emits('route-clicked', route);
  }

  function tripClicked(trip: Trip) {
    emits('trip-clicked', trip);
  }
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-start gap-2 p-2">
    <CompanyInfo :company="props.company"/>

    <SideViewRoute v-if="mapStore.route"
                   :route="mapStore.route"
                   :trips="props.trips?.filter(trip => trip.route_id === mapStore.route?.id)"
                   :countries="props.countries"
                   @route-closed="routeClosed"
                   @trip-clicked="tripClicked"/>

    <SideViewRoutes v-else
                    :routes="props.routes"
                    :trips="props.trips"
                    :countries="props.countries"
                    :filters="props.company ? mapStore.filters : null"
                    @open-from="openFrom"
                    @open-where="openWhere"
                    @swap-from-and-where="emits('swap-from-and-where')"
                    @open-calendar="openCalendar"
                    @clear-filters="clearFilters"
                    @route-clicked="routeClicked"/>
  </div>
</template>
