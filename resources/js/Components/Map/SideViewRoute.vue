<script setup lang="ts">
  import { computed, PropType, ref } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import { ArrowLeftFromLine, ArrowRightFromLine, Car, ChevronDown, ChevronUp, X } from 'lucide-vue-next';
  import { minutesToHumanReadable, numberAsIntOrFloat, toHumanDate, toHumanTime } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import { useMapStore } from '@/stores/map';
  import dayjs from 'dayjs';
  import SideViewStops from '@/Components/Map/SideViewStops.vue';
  import SideViewPrices from '@/Components/Map/SideViewPrices.vue';

  const emits = defineEmits(['route-closed', 'trip-clicked', 'trip-closed']);

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

  const filteredTrips = computed((): Trip[] => {
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

  const forwardTrips = computed((): Trip[] => {
    if (!props.trips) {
      return [];
    }

    return props.trips.filter((t: Trip) => !t.reversed);
  });

  const returnTrips = computed((): Trip[] => {
    if (!props.trips) {
      return [];
    }

    return props.trips.filter((t: Trip) => t.reversed);
  });

  const durations = computed((): number | null => {
    return props.route.points
      .map((p: Point) => p.travel_time)
      .slice(1);
  });

  const arrivesAtsFromDurations = computed((): object => {
    const arrivesAts = {};

    (props.trips ?? [])
      .forEach(t => {
        let time = null;

        if (durations.value?.length && !durations.value.includes(null)) {
          time = dayjs(t.departs_at);

          durations.value
            .forEach(d => time = time.add(d, 'minutes'));
        }

        arrivesAts[t.id] = time;
      });

    for (const t of (props.trips ?? [])) {
      if (durations.value?.length === 0 || durations.value.includes(null)) {
        arrivesAts[t.id] = null;
        continue;
      }

      let beg = dayjs(t.departs_at);

      for (const d of durations.value) {
        if (d === null) {
          beg = null;
          break;
        }

        beg = beg.add(d, 'minutes');
      }

      arrivesAts[t.id] = beg;
    }

    return arrivesAts;
  });

  function hidePoints() {
    mapStore.arePointsHidden = !mapStore.arePointsHidden;
  }
</script>

<template>
  <div class="w-full flex flex-col justify-between items-baseline gap-2 p-3 pt-0 overflow-y-auto">
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

    <div class="w-full h-full flex flex-col justify-start items-start overflow-y-auto pb-20">
      <div class="w-full flex flex-col justify-start items-start"
           v-if="route.points?.length">
        <div class="w-full flex flex-row justify-between items-center pt-3 px-2 cursor-pointer"
             @click="hidePoints">
          <div class="w-full flex flex-col justify-between items-baseline">
            <h3 class="text-md font-semibold">
              Stops ({{route.points.length }})
            </h3>
            <p class="text-sm text-gray-400" v-if="route.travel_time">
              <span class="text-md">{{ minutesToHumanReadable(route.travel_time) }} travel time</span>
            </p>
          </div>

          <div>
            <ChevronDown class="w-6 h-6"
                         v-if="mapStore.arePointsHidden"/>

            <ChevronUp class="w-6 h-6"
                       v-else/>
          </div>
        </div>

        <div class="w-full flex flex-col justify-start items-start rounded pl-1"
             v-if="!mapStore.arePointsHidden">
          <SideViewStops :route="route"
                         :countries="countries"/>
        </div>
      </div>

      <div class="w-full flex flex-col justify-end items-start px-2"
           v-if="route.prices?.length">

        <div class="w-full flex flex-col justify-between items-baseline pt-3">
          <h3 class="text-md font-semibold">
            Prices
          </h3>
          <p class="text-sm text-gray-400">
            may vary depending on the trip
          </p>
        </div>

        <SideViewPrices class="w-full bg-base-200"
                        :route="route"/>
      </div>

      <Deferred data="trips">
        <template #fallback>
          <div class="w-full flex flex-col justify-center items-center gap-2 p-4 mt-4">
            <Car class="w-8 h-8"/>

            <span class="text-lg font-bold">Loading trips</span>
            <span class="loading loading-dots loading-md"></span>
          </div>
        </template>

        <div class="w-full flex flex-col justify-start items-center pt-5 pr-2"
             v-if="trips?.length">
          <div class="w-full flex flex-col justify-start items-start">

            <div class="w-full flex flex-wrap flex-row justify-between items-end pb-2">
              <h3 class="text-md font-semibold">
                Trips
              </h3>

              <div class="w-full flex flex-row justify-start items-end gap-2" v-if="forwardTrips.length && returnTrips.length">
                <span class="text-sm font-medium">Direction: </span>
                <div class="w-full flex flex-row justify-end items-center gap-2">
                  <div class="filter">
                    <input class="btn btn-sm btn-success" type="radio" value="forward" name="trips_mode" :aria-label="'Forward ' + '(' + forwardTrips.length + ')'"
                           :class="{'btn-outline': tripsMode !== 'forward'}"
                           @click="tripsMode = 'forward'"/>
                    <input class="btn btn-sm btn-error" type="radio" value="backward" name="trips_mode" :aria-label="'Return ' + '(' + returnTrips.length + ')'"
                           :class="{'btn-outline': tripsMode !== 'backward'}"
                           @change="$event.target.checked && (tripsMode = 'backward')"/>
                    <input class="btn btn-sm filter-reset ml-1" type="radio" value="all" name="trips_mode" aria-label="All"
                           @change="$event.target.checked && (tripsMode = 'all')"/>
                  </div>
                </div>
              </div>
            </div>

            <template v-for="(trip, index) in filteredTrips" :key="trip.id">
              <div class="w-full flex flex-col justify-start items-center pb-1">
                <div class="w-full border-t-1 opacity-15 pt-1" v-if="index > 0"/>

                <div class="w-full flex flex-row justify-start items-center cursor-pointer gap-2 rounded hover:bg-base-300 px-2">

                  <div class="rounded flex justify-center items-center text-error tooltip tooltip-right tooltip-error"
                       v-if="trip.reversed">
                    <div class="tooltip-content">
                      <div class="text-md font-medium">Return</div>
                    </div>

                    <ArrowLeftFromLine class="w-6 h-6"/>
                  </div>

                  <div class="h-full rounded flex justify-center items-center text-success tooltip tooltip-right tooltip-success"
                       v-else>
                    <div class="tooltip-content">
                      <div class="text-md font-medium">Forward</div>
                    </div>

                    <ArrowRightFromLine class="w-6 h-6"/>
                  </div>

                  <div class="w-full flex flex-row justify-start items-baseline gap-2 p-1 pb-2"
                       @click="emits('trip-clicked', trip)">
                    <div class="flex flex-col justify-start items-baseline">
                      <span class="text-lg font-semibold">{{ toHumanTime(trip.departs_at) }}</span>
                      <span class="text-md">{{ toHumanDate(trip.departs_at) }}</span>
                    </div>

                    <template v-if="trip.arrives_at">
                      <span class="text-2xl"> - </span>

                      <div class="flex flex-col justify-start items-baseline">
                        <span class="text-lg font-semibold">{{ toHumanTime(trip.arrives_at) }}</span>
                        <span class="text-md">{{ toHumanDate(trip.arrives_at) }}</span>
                      </div>
                    </template>

                    <template v-else-if="arrivesAtsFromDurations?.[trip.id]">
                      <span class="text-2xl"> - </span>

                      <div class="flex flex-col justify-start items-baseline">
                        <span class="text-lg font-semibold">{{ toHumanTime(arrivesAtsFromDurations[trip.id]) }}</span>
                        <span class="text-md">{{ toHumanDate(arrivesAtsFromDurations[trip.id]) }}</span>
                      </div>
                    </template>
                  </div>

                  <template v-if="route.base_price">
                    <div class="flex flex-col justify-start items-baseline"
                         @click="emits('trip-clicked', trip)">
                      <span class="w-full text-lg font-semibold text-end">{{ numberAsIntOrFloat(route.base_price.from) }}</span>
                      <div class="w-full flex flex-row justify-end items-baseline gap-0.5">
                        <span class="w-full text-xs font-bold text-end">
                          <span v-if="route.base_price.unit === 'Seat'">seat</span>
                          <span v-else-if="route.base_price.unit === 'Weight'">kg</span>
                          <span v-else-if="route.base_price.unit === 'Volume'">m³</span>
                        </span>
                        <span>/</span>
                        <span class="w-full text-xs font-semibold text-end">{{ route.base_price.currency }}</span>
                      </div>
                    </div>
                  </template>
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
