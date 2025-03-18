<script setup lang="ts">
  import { PropType } from 'vue';
  import { router } from '@inertiajs/vue3';
  import { Company, Route, Trip } from '@/api';
  import CompanyInfo from '@/Components/Map/CompanyInfo.vue';
  import { useMapStore } from '@/stores/map';
  import SideViewRoutes from '@/Components/Map/SideViewRoutes.vue';
  import SideViewRoute from '@/Components/Map/SideViewRoute.vue';
  import SideViewTrip from '@/Components/Map/SideViewTrip.vue';
  import { ChevronRight } from 'lucide-vue-next';

  const emits = defineEmits([
    'toggle-has-trips',
    'change-page',
    'open-from',
    'open-where',
    'swap-from-and-where',
    'open-calendar',
    'clear-filters',
    'route-clicked',
    'route-closed',
    'trip-clicked',
    'trip-closed',
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
    meta: {
      type: Object as PropType<Record<string, any>> | null,
    },
  });

  const mapStore = useMapStore();

  function changePage(page: number) {
    emits('change-page', page);
  }

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

  function tripClosed(trip: Trip) {
    emits('trip-closed', trip);
  }

  function toggleHasTrips(hasTrips: boolean) {
    emits('toggle-has-trips', hasTrips);
  }
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-start bg-base-100 overflow-y-auto">
    <div class="w-full flex flex-col justify-start items-start bg-base-100 sticky top-0 z-[1001]">

      <div class="w-full flex justify-between items-center px-4 py-2 shadow-lg">
        <CompanyInfo class="cursor-pointer px-0"
                     :company="props.company"
                     @click="router.visit(`/${company?.abbreviation}`)"/>
      </div>

      <div class="w-full flex flex-col justify-center items-center">
        <div class="w-full h-[1px]">
          <div class="w-full h-full bg-base-content opacity-10"></div>
        </div>
      </div>
    </div>

    <SideViewTrip v-if="mapStore.trip"
                  :route="mapStore.route"
                  :trip="mapStore.trip"
                  :countries="props.countries"
                  @trip-closed="tripClosed"/>

    <SideViewRoute v-else-if="mapStore.route"
                   :route="mapStore.route"
                   :trips="props.trips?.filter(trip => trip.route_id === mapStore.route?.id)"
                   :countries="props.countries"
                   @route-closed="routeClosed"
                   @trip-clicked="tripClicked"
                   @trip-closed="tripClosed"/>

    <SideViewRoutes v-else
                    :routes="props.routes"
                    :trips="props.trips"
                    :countries="props.countries"
                    :meta="props.meta"
                    :filters="props.company ? mapStore.filters : null"
                    @toggle-has-trips="toggleHasTrips"
                    @change-page="changePage"
                    @open-from="openFrom"
                    @open-where="openWhere"
                    @swap-from-and-where="emits('swap-from-and-where')"
                    @open-calendar="openCalendar"
                    @clear-filters="clearFilters"
                    @route-clicked="routeClicked"/>
  </div>
</template>
