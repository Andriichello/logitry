<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Route, Trip } from '@/api';
  import { ChevronRight, FilterX, Route as RouteIcon } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
  import { MapFilters } from '@/stores/map';
  import { Deferred } from '@inertiajs/vue3';
  import SideViewFilters from '@/Components/Map/SideViewFilters.vue';
  import dayjs from 'dayjs';
  import { useToast } from 'vue-toastification';

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

  const toast = useToast();

  const showClearFilters = computed(() => {
    return props.filters?.from?.length
      || props.filters?.to?.length
      || props.filters?.end
      || (props.filters?.beg && !props.filters.beg.isSame(dayjs(), 'day'))
  })
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center overflow-y-auto">
    <div class="w-full flex flex-col justify-center items-center px-4 py-2">
      <div class="w-full max-w-lg flex flex-col justify-center items-center">
        <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 gap-4 font-mono">
          <div class="w-full flex flex-col justify-start items-start gap-2">
            <h3 class="text-2xl font-semibold">Contact Me</h3>
            <p class="text-md opacity-80">
              Fill out your details for our manager to contact you regarding pickup, destination, date, time, and seats
            </p>
          </div>

          <div class="btn btn-lg btn-outline flex flex-row justify-center items-center gap-1 px-3"
               @click="toast.info('Not implemented yet', {position: 'bottom-center', timeout: 2000})">
            <ChevronRight class="w-6 h-6 mb-0.5 opacity-0"/>
            <span class="w-full">Contact Me</span>
            <ChevronRight class="w-6 h-6 mb-0.5"/>
          </div>
        </div>
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center">
      <div class="w-full h-[1px]">
        <div class="w-full h-full bg-base-content opacity-10"></div>
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center bg-base-200/60 px-4 py-2">
      <div class="w-full max-w-lg flex flex-col justify-center items-center">
        <div class="w-full flex flex-col justify-between items-between rounded-right py-3 gap-4 font-mono">
          <div class="w-full flex flex-col justify-start items-start gap-2">
            <div class="w-full flex flex-row justify-between items-end gap-2">
              <h3 class="text-2xl font-semibold">Filters</h3>

              <template v-if="showClearFilters">
                <label for="clear_filters"
                       class="h-8 btn btn-md btn-error btn-outline flex flex-row cursor-pointer">
                  <span class="pt-0.5 pl-2">Clear</span>
                  <FilterX class="w-5 h-5"/>
                </label>
              </template>

              <template v-else>
                <div class="h-8 btn btn-md btn-error flex justify-center items-center cursor-pointer opacity-0">
                  <span class="pt-0.5">Clear</span>
                  <FilterX class="w-5 h-5"/>
                </div>
              </template>
            </div>

            <SideViewFilters :filters="filters"
                             :countries="countries"
                             @swap-from-and-where="emits('swap-from-and-where')"
                             @open-from="emits('open-from')"
                             @open-where="emits('open-where')"
                             @open-calendar="emits('open-calendar')"/>
          </div>

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
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center">
      <div class="w-full h-[1px]">
        <div class="w-full h-full bg-base-content opacity-10"></div>
      </div>
    </div>

    <div class="w-full grow flex flex-col justify-start items-center px-4 py-2">
      <div class="w-full h-full max-w-lg flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 gap-4 font-mono">
          <div class="w-full flex flex-col justify-start items-start gap-2">
            <h3 class="text-2xl font-semibold">Routes</h3>
            <p class="text-md opacity-80">
              Here are your search results
            </p>
          </div>

          <ul class="w-full list bg-base-200/60 border border-base-content/40 rounded shadow-md"
              v-if="props.routes?.length">

            <template v-for="(route, index) in props.routes" :key="route.id">
              <li class="list-row pt-3 pb-2 px-4">
                <div class="list-col-grow">
                  <div class="text-lg font-semibold font-mono">{{ route.name }}</div>
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
                <button class="btn btn-primary pt-0.5 opacity-80 hover:opacity-100"
                        @click="emits('route-clicked', route)">
                  View
                </button>
              </li>
            </template>
          </ul>

          <div class="w-full h-full grow flex flex-col justify-start items-center gap-5"
               v-else>
            <div class="w-full flex flex-col justify-start items-center py-4 gap-3">
              <RouteIcon class="w-8 h-8"/>

              <span class="text-xl font-bold">No routes found</span>
            </div>

            <div class="w-full btn btn-lg btn-outline btn-error flex flex-row cursor-pointer"
                 v-if="showClearFilters"
                 @click="emits('clear-filters')">
              <span class="pt-0.5 pl-2">Clear all filters</span>
              <FilterX class="w-5 h-5"/>
            </div>
          </div>

          <div class="w-full h-full grow flex flex-row justify-end items-center gap-4 pl-1 pt-1"
               v-if="props.routes?.length">
            <span class="text-md opacity-80 mt-0.5">Page: </span>

            <div class="flex flex-row justify-end items-baseline gap-2">
              <button class="h-9 btn btn-md btn-outline">1</button>
              <button class="h-9 btn btn-md btn-ghost">2</button>
              <button class="h-9 btn btn-md btn-ghost">3</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center">
      <div class="w-full h-[1px]">
        <div class="w-full h-full bg-base-content opacity-10"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .list-row::after {
    border-color: color-mix(in oklab,var(--color-base-content)30%,#0000);
  }
</style>
