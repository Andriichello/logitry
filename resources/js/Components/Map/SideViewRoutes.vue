<script setup lang="ts">
  import { PropType, ref } from 'vue';
  import { Route, Trip } from '@/api';
  import { Route as RouteIcon, X, Search, ArrowRightLeft, MapPin, Calendar, Circle } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
  import { useMapStore, MapFilters } from "@/stores/map";
  import { Deferred } from '@inertiajs/vue3';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import SideViewFilters from "@/Components/Map/SideViewFilters.vue";

  const emits = defineEmits(['open-from', 'open-where', 'swap-from-and-where', 'open-calendar', 'route-clicked']);

  const props = defineProps({
    routes: {
      type: Array as PropType<Route[]> | null,
      required: true,
    },
    trips: {
      type: Array as PropType<Trip[]> | null,
      required: false,
      default: null,
    },
    filters: {
      type: Object as PropType<MapFilters> | null,
      required: true,
    },
    countries: {
      type: Object as PropType<Record<string, string>> | null,
    },
  });
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center p-3 overflow-y-auto pb-10">

    <div class="w-full flex flex-col justify-between items-baseline pb-4"
         v-if="filters !== null">
      <div class="w-full flex flex-row justify-between items-baseline gap-2 pb-2">
        <h3 class="text-md font-semibold">
          Filters
        </h3>

        <div class="rounded flex justify-center items-center cursor-pointer p-2 opacity-0">
          <X class="w-5 h-5"/>
        </div>
      </div>

      <SideViewFilters :filters="filters"
                       :countries="countries"
                       @swap-from-and-where="emits('swap-from-and-where')"
                       @open-from="emits('open-from')"
                       @open-where="emits('open-where')"
                       @open-calendar="emits('open-calendar')"/>
    </div>

    <div class="w-full flex flex-row justify-between items-baseline gap-2">
      <h3 class="text-md font-semibold">
        Routes
      </h3>

      <div class="rounded flex justify-center items-center cursor-pointer p-2 opacity-0">
        <X class="w-5 h-5"/>
      </div>
    </div>

    <div class="w-full grow flex flex-col justify-start items-start"
         v-if="props.routes?.length">

      <template v-for="(route, index) in props.routes" :key="route.id">
        <div class="w-full flex flex-col justify-start items-center">
          <div class="w-full border-t-2 opacity-15" v-if="index > 0"/>

          <div class="w-full flex flex-col justify-start items-center cursor-pointer p-2 pt-0"
               @click="emits('route-clicked', route)">

            <div class="w-full flex flex-row justify-start items-center gap-2 rounded pt-2">
              <span class="text-xl font-semibold">{{ route.name }}</span>
            </div>

            <div class="w-full flex flex-row justify-start items-baseline self-start gap-4">
              <div class="flex flex-row justify-start items-center"
                   v-if="route.points?.length">
                <span class="text-md">{{ route.points?.length }} stops</span>
              </div>

              <div class="flex flex-row justify-center items-center"
                   v-if="route.travel_time">
                <span class="text-md">{{ minutesToHumanReadable(route.travel_time) }}</span>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <div class="flex flex-col justify-center items-center gap-2 p-4"
         v-else>
      <RouteIcon class="w-8 h-8"/>

      <span class="text-lg font-bold">No routes found</span>
    </div>
  </div>
</template>
