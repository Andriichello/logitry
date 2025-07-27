<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import {ArrowLeft, ArrowLeftFromLine, ArrowRightFromLine, ChevronDown, ChevronUp, X} from 'lucide-vue-next';
  import {minutesToHumanReadable, toHumanDate, toHumanTime, toHumanWeekday} from '@/helpers';
  import { useMapStore } from '@/stores/map';
  import SideViewStops from '@/Components/Map/SideViewStops.vue';
  import SideViewPrices from '@/Components/Map/SideViewPrices.vue';

  const emits = defineEmits(['trip-clicked', 'trip-closed']);

  const props = defineProps({
    route: {
      type: Object as PropType<Route>,
      required: true,
    },
    trip: {
      type: Object as PropType<Trip>,
      required: true,
    },
    countries: {
      type: Object as PropType<Record<string, string>> | null,
    },
  });

  const mapStore = useMapStore();

  const prices = computed(() => {
    return props.route.prices;
  });

  const vehicle = computed(() => {
    return props.route.vehicle;
  });

  const pointsInOrder = computed(() => {
    if (props.trip.reversed) {
      return [...props.route.points].reverse();
    }

    return props.route.points;
  });

  function hidePoints() {
    mapStore.arePointsHidden = !mapStore.arePointsHidden;
  }
</script>

<template>
  <div class="w-full flex flex-col justify-between items-baseline pt-0 font-mono">
    <div class="w-full grow flex flex-col justify-start items-center px-4">
      <div class="w-full h-full max-w-lg flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-start items-start gap-2 pt-3.5 pb-2.5">
          <div class="w-full max-w-lg flex justify-start items-center">
            <div class="w-fit flex justify-start items-center gap-1 text-md font-weight-light cursor-pointer opacity-80"
                 @click="emits('trip-closed', trip)">
              <ArrowLeft class="w-5 h-5 pb-1"/>
              <p>Back to route details</p>
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

    <div class="w-full grow flex flex-col justify-start items-center px-4 pt-2">
      <div class="w-full h-full max-w-lg flex flex-col justify-start items-center">
        <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 gap-4 font-mono">
          <div class="w-full flex flex-col justify-between items-baseline gap-2">
            <h3 class="text-2xl font-semibold">
              {{ route.name }}
            </h3>
            <p class="text-md opacity-80">
              Trip details
            </p>

            <div class="w-full flex justify-between items-center gap-2">
              <div class="rounded flex justify-center items-center text-error tooltip tooltip-open tooltip-right tooltip-error"
                   v-if="trip?.reversed">
                <div class="tooltip-content">
                  <div class="text-md font-medium">Return</div>
                </div>

                <ArrowLeftFromLine class="w-6 h-6"/>
              </div>

              <div class="h-full rounded flex justify-center items-center text-success tooltip tooltip-open tooltip-right tooltip-success"
                   v-else>
                <div class="tooltip-content">
                  <div class="text-md font-medium">Forward</div>
                </div>

                <ArrowRightFromLine class="w-6 h-6"/>
              </div>

              <template v-if="trip.departs_at">
                <p class="font-mono italic">
                  {{ toHumanTime(trip.departs_at) }}<span class="text-sm"> ({{ toHumanWeekday(trip.departs_at) }}, {{ toHumanDate(trip.departs_at) }})</span>
                </p>

                <p class="font-mono italic"
                   v-if="trip.arrives_at">
                  {{ toHumanTime(trip?.arrives_at) }}<span class="text-sm"> ({{ toHumanWeekday(trip?.arrives_at) }}, {{ toHumanDate(trip?.arrives_at) }})</span>
                </p>
              </template>
            </div>
          </div>

          <div class="w-full h-full flex flex-col justify-start items-start pb-20">
            <div class="w-full flex flex-col justify-center items-center px-4 pt-0 bg-base-200/80 border border-base-content/60 rounded"
                 :class="{'pb-1': mapStore.arePointsHidden}"
                 v-if="pointsInOrder.length">
              <div class="w-full max-w-lg flex flex-col justify-center items-center">
                <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 font-mono">
                  <div class="w-full flex flex-col justify-start items-start">
                    <div class="w-full flex flex-row justify-between items-start gap-2">
                      <h3 class="w-full text-xl font-semibold pt-1 opacity-80"
                          :class="{'cursor-pointer': mapStore.arePointsHidden, 'opacity-100': !mapStore.arePointsHidden}"
                          @click="mapStore.arePointsHidden && hidePoints()">
                        Stops ({{ route.points.length }})
                      </h3>

                      <button class="btn btn-sm h-fit py-1 pt-1.5 text-[14px] btn-outline border-base-content/60 opacity-80 hover:opacity-100 font-semibold"
                              @click="hidePoints">
                        {{ !mapStore.arePointsHidden ? 'Hide' : 'Show' }} list
                      </button>
                    </div>

                    <p class="w-full text-md opacity-80"
                       v-if="route.travel_time"
                       :class="{'cursor-pointer': mapStore.arePointsHidden}"
                       @click="mapStore.arePointsHidden && hidePoints()">
                      {{ minutesToHumanReadable(route.travel_time) }} travel time
                    </p>
                  </div>

                  <div class="w-full flex flex-col justify-start items-start rounded pt-1"
                       v-if="!mapStore.arePointsHidden">
                    <SideViewStops :route="route"
                                   :trip="trip"
                                   :countries="countries"/>
                  </div>
                </div>
              </div>
            </div>

            <div class="w-full flex flex-col justify-end items-start"
                 v-if="prices?.length">

              <div class="w-full flex flex-col justify-between items-baseline pt-3">
                <h3 class="text-md font-semibold">
                  Prices
                </h3>
                <p class="text-sm text-gray-400">
                  for the current trip
                </p>
              </div>

              <SideViewPrices class="w-full bg-base-200"
                              :route="route"
                              :trip="trip"/>
            </div>

            <div class="w-full flex flex-col justify-end items-start"
                 v-if="false">
              <div class="w-full flex flex-col justify-between items-baseline pt-3">
                <h3 class="text-md font-semibold">
                  Vehicle
                </h3>
                <p class="text-sm text-gray-400">
                  for the current trip
                </p>
              </div>

              <table class="w-full table table-md text-md bg-base-200">
                <tbody>
                <tr>
                  <td class="text-[16px] w-full flex justify-start items-baseline gap-2">
                <span class="font-medium font-mono">
                  Renault Megan (2016)
                </span>
                  </td>
                </tr>

                <tr v-if="prices.some(p => p.unit === 'Seat')">
                  <td class="text-[16px] w-full flex justify-start items-baseline gap-2">
                <span class="font-medium font-mono">
                  4 seats
                </span>
                  </td>
                </tr>

                <template v-if="prices.some(p => p.unit === 'Weight' || p.unit === 'Volume')">
                  <tr v-if="prices.some(p => p.unit === 'Weight')">
                    <td class="text-[16px] w-full flex justify-start items-baseline gap-2">
                      <div class="w-full flex justify-start items-baseline gap-3">
                     <span class="font-medium font-mono">
                      0.308 m³
                    </span>
                        <span class="font-medium font-mono">
                      =
                    </span>
                        <span class="grow font-medium font-mono">
                      2.40 x 1.60 x 1.15 m
                    </span>
                      </div>

                    </td>
                  </tr>
                </template>

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
