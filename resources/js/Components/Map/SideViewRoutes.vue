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
  import ContactMe from '@/Components/Map/ContactMe.vue';

  const emits = defineEmits([
    'toggle-has-trips',
    'change-page',
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
    meta: {
      type: Object as PropType<Record<string, any>> | null,
    },
  });

  const toast = useToast();

  const showClearFilters = computed(() => {
    return props.filters?.from?.length
      || props.filters?.to?.length
      || props.filters?.has_trips
      || props.filters?.end
      || (props.filters?.beg && !props.filters.beg.isSame(dayjs(), 'day'))
  })

  /**
   * Computes the list of pages to display for pagination.
   */
  const paginationPages = computed(() => {
    if (!props.meta) return [];
    const { current_page, last_page } = props.meta;

    const pages = [];
    if (last_page <= 5) {
      // Case 1: Last page <= 5, show all pages
      for (let i = 1; i <= last_page; i++) {
        pages.push(i);
      }
    } else {
      // Case 2: More than 5 pages
      if (current_page <= 3) {
        // Show first 4 pages and last page
        pages.push(1, 2, 3, 4, '...', last_page);
      } else if (current_page >= last_page - 2) {
        // Show first page and last 4 pages
        pages.push(1, '...', last_page - 3, last_page - 2, last_page - 1, last_page);
      } else {
        // Show first page, 3 around the current, and last page
        pages.push(
          1,
          '...',
          current_page - 1,
          current_page,
          current_page + 1,
          '...',
          last_page
        );
      }
    }
    return pages;
  });

  function toggleHasTrips(hasTrips: boolean) {
    emits('toggle-has-trips', hasTrips);
  }
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center">
    <ContactMe class="shadow-sm"/>

    <div class="w-full flex flex-col justify-center items-center">
      <div class="w-full h-[1px]">
        <div class="w-full h-full bg-base-content opacity-10"></div>
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center px-4 pt-5">
      <div class="w-full max-w-lg flex flex-col justify-center items-center">
        <div class="w-full flex flex-col justify-between items-between rounded-right gap-4 font-mono">
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
                             @toggle-has-trips="toggleHasTrips"
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

    <div class="w-full grow flex flex-col justify-start items-center px-4 pt-2 pb-20">
      <div class="w-full h-full max-w-lg flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 font-mono">
          <div class="w-full flex flex-col justify-start items-start gap-2">
            <h3 class="text-2xl font-semibold font-mono">Routes</h3>
            <p class="text-md opacity-80">
              Here are your search results
            </p>
          </div>

          <ul class="w-full list bg-base-200/60 border border-base-content/50 rounded shadow-md"
              v-if="props.routes?.length">

            <template v-for="(route, index) in props.routes" :key="route.id">
              <li class="list-row pt-3 pb-2 px-4 rounded-none first:rounded-t last:rounded-b">
                <div class="list-col-grow">
                  <div class="text-xl font-semibold font-mono">{{ route.name }}</div>
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

                      <div class="flex flex-row justify-start items-center p-0.5 pr-2 rounded"
                           v-else>
                        <span class="text-md">No trips</span>
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
                <button class="btn btn-md btn-outline border-base-content/60 opacity-80 hover:opacity-100 font-semibold"
                        @click="emits('route-clicked', route)">
                  <ChevronRight class="w-5 h-5"/>
                </button>
              </li>
            </template>
          </ul>

          <div class="w-full h-full grow flex flex-col justify-start items-center"
               v-else>
            <div class="w-full flex flex-col justify-start items-center pt-8 pb-3 gap-3">
              <RouteIcon class="w-8 h-8"/>

              <span class="text-xl font-bold">No routes found</span>
            </div>

            <div class="w-full btn btn-lg btn-outline btn-error flex flex-row cursor-pointer"
                 v-if="showClearFilters || (!routes.length && meta?.total > 1)"
                 @click="emits('clear-filters')">
              <span class="pt-0.5 pl-2">Clear all filters</span>
              <FilterX class="w-5 h-5"/>
            </div>
          </div>

          <div v-if="routes.length && meta?.total > 1"
              class="w-full h-full flex flex-col justify-between items-end px-4 pt-2 gap-1">

            <span class="w-full text-sm text-center opacity-60 pr-4.5">
              Showing {{ meta.from }}-{{ meta.to }} of {{ meta.total }}
            </span>

            <div v-if="meta.last_page !== 1"
              class="w-full h-full grow flex flex-row justify-end items-center gap-4 pl-1 pt-1">
              <span class="text-md opacity-80 mt-0.5">Page: </span>

              <div class="flex flex-row justify-center items-center gap-2">
                <template v-for="page in paginationPages" :key="page">
                  <!-- Dynamic Pagination Buttons -->
                  <button v-if="page !== '...'"
                          class="btn btn-md"
                          :class="{'btn-active': page === meta.curent_page, 'btn-ghost opacity-50 hover:opacity-100': page !== meta.current_page}"
                          @click="page !== meta.current_page && emits('change-page', page)">
                    {{ page }}
                  </button>

                  <button v-else class="btn btn-ghost user-select-none w-6" disabled>
                    {{ page }}
                  </button>
                </template>
              </div>
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
