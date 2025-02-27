<script setup lang="ts">
  import { PropType, ref } from 'vue';
  import { Route, Trip } from '@/api';
  import { Route as RouteIcon, X, Search, ArrowRightLeft, MapPin, Calendar, Circle } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
  import { useMapStore, MapFilters } from "@/stores/map";
  import { Deferred } from '@inertiajs/vue3';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';

  const emits = defineEmits(['open-calendar', 'route-clicked']);

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
  });

  const mapStore = useMapStore();
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

      <div class="w-full flex flex-col justify-center items-center rounded">
        <div class="w-full flex justify-start items-center gap-2">
          <div class="w-full flex flex justify-center items-center">
            <div class="w-full flex flex-col justify-start items-start gap-1">


              <div class="w-full min-h-full flex justify-start items-center gap-2 font-bold text-md">
                <div class="w-full flex flex-col justify-start items-start px-2 py-1 rounded cursor-pointer hover:bg-gray-100">
                  <div class="text-xs font-bold text-gray-500">
                    From?
                  </div>

                  <div class="w-full min-h-full flex justify-start items-center gap-2 text-md translate-y-[-4px]">
                    <div>{{ filters?.from ?? 'Origin' }}</div>
                    <div class="flex justify-center items-center" v-if="filters?.from">{{ getUnicodeFlagIcon(filters?.from === 'Ukraine' ? 'UA' : filters?.from === 'Slovakia' ? 'SK' : filters?.from) }}</div>
                  </div>
                </div>
              </div>
              <div class="w-full border-t-1 opacity-15" />
              <div class="w-full min-h-full flex justify-start items-center gap-2 font-bold text-md">
                <div class="w-full flex flex-col justify-start items-start px-2 py-1 rounded cursor-pointer hover:bg-gray-100">
                  <div class="text-xs font-bold text-gray-500">
                    Where?
                  </div>

                  <div class="w-full min-h-full flex justify-start items-center gap-2 text-md translate-y-[-4px]">
                    <div>{{ filters?.to ?? 'Destination' }}</div>
                    <div class="flex justify-center items-center" v-if="filters?.to">{{ getUnicodeFlagIcon(filters?.to === 'Ukraine' ? 'UA' : filters?.to === 'Slovakia' ? 'SK' : filters?.to) }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex p-3 hover:bg-gray-100 rounded cursor-pointer ml-2">
              <ArrowRightLeft class="w-4 h-4 rotate-90"/>
            </div>
          </div>
        </div>

        <div class="w-full flex justify-start items-center gap-2 pt-1">
            <div class="w-full flex flex-col justify-start items-start gap-1">
              <div class="w-full border-t-1 opacity-15"/>
              <div class="w-full min-h-full flex justify-start items-center gap-3 font-bold text-md">
                <div class="w-full flex flex-col justify-start items-start px-2 py-1 rounded cursor-pointer hover:bg-gray-100"
                  @click="emits('open-calendar')">
                  <div class="text-xs font-bold text-gray-500">
                    Departure
                  </div>

                  <div class="w-full min-h-full flex justify-start items-center gap-2 text-md">
                    <template v-if="filters.beg && filters.end">
                      <div class="w-full flex justify-start items-center gap-2">
                        <div>{{ filters.beg?.format('ddd, DD MMM') ?? 'Start date' }}</div>

                        <template v-if="!filters.beg?.isSame(filters.end, 'day')">
                          <div>-</div>
                          <div>{{ filters.end?.format('ddd, DD MMM') ?? 'End Date' }}</div>
                        </template>
                      </div>
                    </template>

                    <template v-else-if="filters.beg">
                      <div class="w-full flex justify-start items-center gap-2">
                        <div>{{ filters.beg?.format('ddd, DD MMM') ?? 'Start date' }}</div>
                        <div>-</div>
                        <div>Future</div>
                      </div>
                    </template>

                    <template v-else>
                      <div class="w-full flex justify-start items-center gap-2">
                        <div>Start date</div>
                        <div>-</div>
                        <div>End date</div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
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
