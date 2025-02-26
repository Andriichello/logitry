<script setup lang="ts">
  import { PropType } from 'vue';
  import { Company, Route } from '@/api';
  import CompanyInfo from '@/Components/Map/CompanyInfo.vue';
  import { useMapStore } from '@/stores/map';
  import SideViewRoutes from '@/Components/Map/SideViewRoutes.vue';
  import SideViewRoute from '@/Components/Map/SideViewRoute.vue';
  import ClassicCalendar from '@/Components/Date/ClassicCalendar.vue';
  import dayjs from 'dayjs';
  import BookingCalendar from '@/Components/Date/BookingCalendar.vue';

  const emits = defineEmits(['route-clicked', 'route-closed', 'trip-clicked']);

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
    }
  });

  const mapStore = useMapStore();

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
                   @route-closed="routeClosed"
                   @trip-clicked="tripClicked"/>

    <SideViewRoutes v-else
                    :routes="props.routes"
                    @route-clicked="routeClicked"/>
  </div>
</template>
