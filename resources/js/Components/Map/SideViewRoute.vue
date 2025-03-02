<script setup lang="ts">
  import { computed, ref, PropType } from 'vue';
  import { Route, Trip } from '@/api';
  import { Calendar1, Car, ChevronDown, ChevronUp, MapPin, MapPinHouse, X, ArrowLeftFromLine, ArrowRightFromLine } from 'lucide-vue-next';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { minutesToHumanReadable, toHumanDate, toHumanTime } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import { useToast } from 'vue-toastification';
  import { useMapStore } from '@/stores/map';

  const emits = defineEmits(['route-closed', 'trip-clicked']);

  const props = defineProps({
    route: {
      type: Object as PropType<Route>,
      required: true,
    },
    trips: {
      type: Array as PropType<Trip[]> | null,
      required: true,
    },
    countries: {
      type: Object as PropType<Record<string, string>> | null,
    },
  });

  const mapStore = useMapStore();

  const tripsMode = ref('all');

  const filteredTrips = computed(() => {
    if (!props.trips) {
      return [];
    }

    if (tripsMode.value === 'all') {
      return props.trips;
    }

    return props.trips.filter(trip => {
      return trip.reversed ? tripsMode.value === 'backward' : tripsMode.value === 'forward';
    })
  });

  function hidePoints() {
    mapStore.arePointsHidden = !mapStore.arePointsHidden;
  }
</script>

<template>
  <div class="w-full flex flex-col justify-between items-baseline gap-2 p-3 overflow-y-auto">
    <div class="w-full flex flex-row justify-between items-baseline gap-2">
      <h3 class="text-md font-semibold">
        Route
      </h3>

      <div class="rounded flex justify-center items-center cursor-pointer p-2 translate-x-[8px]"
           @click="emits('route-closed', route)">
        <X class="w-5 h-5"/>
      </div>
    </div>

    <div class="w-full flex flex-row justify-between items-baseline gap-2 px-2">
      <h3 class="text-xl font-semibold">
        {{ route.name ?? 'Route' }}
      </h3>
    </div>

    <div class="w-full h-full flex flex-col justify-start items-center overflow-y-auto pb-20">
      <div class="w-full flex flex-col justify-start items-start gap-1"
           v-if="route.points?.length">

        <div class="w-full flex flex-row justify-between items-end gap-2 p-2 cursor-pointer"
             @click="hidePoints">

          <div class="flex flex-row justify-start items-center gap-2">
            <ChevronDown class="w-5 h-5 mt-0.5"
                         v-if="mapStore.arePointsHidden"/>

            <ChevronUp class="w-5 h-5 mt-0.5"
                       v-else/>

            <h3 class="text-md">
              Stops ({{route.points?.length }})
            </h3>
          </div>

          <div class="flex flex-row justify-end items-center"
               v-if="route.travel_time">
            <span class="text-md">{{ minutesToHumanReadable(route.travel_time) }}</span>
          </div>
        </div>

        <template v-for="(point, index) in route.points" :key="point.id">
          <div class="w-full flex flex-col justify-end items-center px-2" v-if="!mapStore.arePointsHidden"
            :class="{'pb-5': index === route.points.length - 1}">
            <div class="w-full flex flex-row justify-start items-center pl-2.5">
              <div class="w-full flex flex-col justify-center items-start border-l-2 border-opacity-20 pl-5 py-2"
                   v-if="index > 0">
                <div class="flex flex-row justify-start items-center"
                     v-if="point.travel_time">
                  <span>{{ minutesToHumanReadable(point.travel_time) }}</span>
                </div>
              </div>
            </div>

            <div class="w-full flex flex-col justify-start items-center">
              <div class="w-full flex flex-row justify-start items-center gap-2 rounded">
                <div class="rounded flex justify-center items-center">
                  <MapPinHouse class="w-6 h-6" v-if="index === 0 || index === route.points.length - 1"/>
                  <MapPin class="w-6 h-6" v-else/>
                </div>

                <div class="flex justify-start items-baseline"
                     v-if="point.city">
                <span>
                  <span class="text-lg font-semibold">{{ point.city }}</span><br>
                  <Deferred data="countries">
                    <template #fallback>
                      <span class="text-md">{{ getUnicodeFlagIcon(point.country) }} {{ point.country }} </span>
                    </template>

                    <span class="text-md font-light">{{ getUnicodeFlagIcon(point.country) }} {{ props.countries?.[point.country] ?? point.country?.toUpperCase() }} </span>
                  </Deferred>

                </span>
                </div>

                <div v-else>
                  <span class="text-lg">{{ point.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>

      <Deferred data="trips">
        <template #fallback>
          <div class="flex flex-col justify-center items-center gap-2 p-4 mt-4">
            <Car class="w-8 h-8"/>

            <span class="text-lg font-bold">Loading trips</span>
            <span class="loading loading-dots loading-md"></span>
          </div>
        </template>

        <div class="w-full flex flex-col justify-start items-center pt-4 pr-2"
             v-if="trips?.length">
          <div class="w-full flex flex-col justify-start items-start">

            <div class="w-full flex flex-wrap flex-row justify-between items-end">
              <h3 class="text-md font-semibold">
                Trips
              </h3>

              <div class="w-full flex flex-row justify-between items-end gap-2">
                <span class="text-sm font-medium grow">Direction: </span>
                <div class="self-center filter" v-if="trips?.filter(t => !t.reversed).length && trips?.filter(t => t.reversed).length">
                  <input class="btn btn-sm btn-success" type="radio" value="forward" name="trips_mode" :aria-label="'Forward ' + '(' + trips?.filter(t => !t.reversed)?.length + ')'"
                         :class="{'btn-outline': tripsMode !== 'forward'}"
                         @click="tripsMode = 'forward'"/>
                  <input class="btn btn-sm btn-error" type="radio" value="backward" name="trips_mode" :aria-label="'Back ' + '(' + trips?.filter(t => t.reversed)?.length + ')'"
                         :class="{'btn-outline': tripsMode !== 'backward'}"
                         @change="$event.target.checked && (tripsMode = 'backward')"/>
                  <input class="btn btn-sm filter-reset ml-1" type="radio" value="all" name="trips_mode" aria-label="All"
                         @change="$event.target.checked && (tripsMode = 'all')"/>
                </div>
              </div>
            </div>


            <template v-for="(trip, index) in filteredTrips" :key="trip.id">
              <div class="w-full flex flex-col justify-start items-center">
                <div class="w-full border-t-1 opacity-15" v-if="index > 0"/>

                <div class="w-full flex flex-row justify-start items-center cursor-pointer gap-2"
                     @click="emits('trip-clicked', trip)">

                  <div class="rounded flex justify-center items-center pl-2 text-error tooltip tooltip-right tooltip-error"
                       v-if="trip.reversed">
                    <div class="tooltip-content">
                      <div class="text-md font-medium">Back</div>
                    </div>

                    <ArrowLeftFromLine class="w-6 h-6"/>
                  </div>

                  <div class="h-full rounded flex justify-center items-center pl-2 text-success tooltip tooltip-right tooltip-success"
                       v-else>
                    <div class="tooltip-content">
                      <div class="text-md font-medium">Forward</div>
                    </div>

                    <ArrowRightFromLine class="w-6 h-6"/>
                  </div>

                  <div class="w-full flex flex-row justify-start items-baseline gap-2 p-1 pb-2">
                    <div class="flex flex-col justify-start items-baseline">
                      <span class="text-lg font-semibold">{{ toHumanTime(trip.departs_at) }}</span>
                      <span class="text-md">{{ toHumanDate(trip.departs_at) }}</span>
                    </div>

                    <span class="text-2xl"> - </span>

                    <div class="flex flex-col justify-start items-baseline">
                      <span class="text-lg font-semibold">{{ toHumanTime(trip.arrives_at) }}</span>
                      <span class="text-md">{{ toHumanDate(trip.arrives_at) }}</span>
                    </div>
                  </div>

                  <div class="flex flex-col justify-start items-baseline">
                    <span class="w-full text-lg font-semibold text-end">price.</span>
                    <span class="w-full text-xs font-semibold text-end">USD</span>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div class="flex flex-col justify-center items-center gap-2 p-4"
             v-else>
          <Car class="w-8 h-8"/>

          <span class="text-lg font-bold">No trips found</span>
        </div>
      </Deferred>
    </div>
  </div>
</template>
