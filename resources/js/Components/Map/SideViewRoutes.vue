<script setup lang="ts">
  import { PropType, computed } from 'vue';
  import { Route, Trip } from '@/api';
  import { Route as RouteIcon, X, Filter, FilterX } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
  import { MapFilters } from '@/stores/map';
  import { Deferred } from '@inertiajs/vue3';
  import SideViewFilters from '@/Components/Map/SideViewFilters.vue';
  import dayjs from 'dayjs';

  const emits = defineEmits([
    'open-from',
    'open-where',
    'swap-from-and-where',
    'open-calendar',
    'clear-filters',
    'route-clicked']);

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

  const showClearFilters = computed(() => {
    return props.filters?.from?.length
      || props.filters?.to?.length
      || props.filters?.end
      || (props.filters?.beg && !props.filters.beg.isSame(dayjs(), 'day'))
  })
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center p-3 overflow-y-auto pb-20">
    <div class="w-full flex flex-col justify-between items-baseline pb-2 gap-1"
         v-if="filters !== null">
      <div class="w-full flex flex-row justify-between items-baseline gap-2">
        <h3 class="text-md font-semibold">
          Filters
        </h3>

        <template v-if="showClearFilters">
          <label for="clear_filters"
                 class="btn btn-sm btn-soft btn-error flex justify-center items-center cursor-pointer p-2 gap-1">
            Clear
            <FilterX class="w-5 h-5"/>
          </label>
        </template>

        <template v-else>
          <div class="btn btn-sm flex justify-center items-center cursor-pointer p-2 gap-1 opacity-0">
            Clear
            <FilterX class="w-6 h-5"/>
          </div>
        </template>

        <!-- Put this part before </body> tag -->
        <input type="checkbox" id="clear_filters" class="modal-toggle" />
        <div class="modal" role="dialog">
          <div class="modal-box max-w-xs">
            <h3 class="text-lg font-bold">Clear all filters?</h3>
            <div class="w-full flex flex-row justify-start items-baseline gap-2 pt-5">
              <label class="flex-1 btn btn-md btn-outline btn-error"
                     for="clear_filters"
                     @click="emits('clear-filters')">
                Yes
              </label>
              <label class="flex-1 btn btn-md btn-soft"
                     for="clear_filters">
                No
              </label>
            </div>
          </div>
          <label id="close_filters" class="modal-backdrop" for="clear_filters">Close</label>
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

    <div class="w-full grow flex flex-col justify-start gap-1 items-start"
         v-if="props.routes?.length">

      <template v-for="(route, index) in props.routes" :key="route.id">
        <div class="w-full border-t-1 opacity-15" v-if="index > 0"/>
        <div class="w-full flex flex-col justify-start items-center">
          <div class="w-full flex flex-col justify-start items-center cursor-pointer px-2 pt-0 rounded hover:bg-base-300"
               @click="emits('route-clicked', route)">

            <div class="w-full flex flex-row justify-start items-center gap-2 rounded pt-1">
              <span class="text-md font-semibold">{{ route.name }}</span>
            </div>

            <div class="w-full flex flex-wrap flex-row justify-start items-baseline self-start gap-2 opacity-75 translate-y-[-4px]">
              <Deferred data="trips">
                <template #fallback>
                  <div class="flex flex-row justify-center items-center gap-2 p-0.5 pr-2 rounded">
                    <span class="text-md"><span class="loading loading-dots loading-xs mr-1"/>trips</span>
                  </div>
                </template>

                <div class="flex flex-row justify-start items-center p-0.5 pr-2 rounded"
                  v-if="trips?.filter(trip => trip.route_id === route.id)?.length">
                  <span class="text-md">{{ trips?.filter(trip => trip.route_id === route.id)?.length }} {{ trips?.filter(trip => trip.route_id === route.id)?.length > 1 ? 'trips' : 'trip' }}<span class="opacity-0" v-if="trips?.filter(trip => trip.route_id === route.id)?.length === 1">s</span> </span>
                </div>
              </Deferred>

              <div class="flex flex-row justify-start items-center p-0.5 px-2 rounded"
                   v-if="route.points?.length">
                <span class="text-md">{{ route.points?.length }} stops</span>
              </div>

              <div class="flex flex-row justify-center items-center p-0.5 px-2 rounded"
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
