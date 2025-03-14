<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import { ArrowLeftFromLine, ArrowRightFromLine, ChevronDown, ChevronUp, X } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
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
  <div class="w-full flex flex-col justify-between items-baseline gap-2 p-3 pt-0 overflow-y-auto">
    <div class="w-full flex flex-row justify-between items-baseline gap-2">
      <h3 class="text-md font-semibold">
        Trip
      </h3>

      <div class="rounded flex justify-center items-center cursor-pointer p-2 translate-x-[8px]"
           @click="emits('trip-closed', trip)">
        <X class="w-5 h-5"/>
      </div>
    </div>

    <div class="w-full flex flex-col justify-between items-baseline gap-2 px-2">
      <h3 class="text-xl font-semibold">
        {{ route.name }}
      </h3>

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
    </div>

    <div class="w-full h-full flex flex-col justify-start items-start overflow-y-auto pb-20">
      <div class="w-full flex flex-col justify-start items-start"
           v-if="pointsInOrder.length">
        <div class="w-full flex flex-row justify-between items-center pt-3 px-2 cursor-pointer"
             @click="hidePoints">
          <div class="w-full flex flex-col justify-between items-baseline">
            <h3 class="text-md font-semibold">
              Stops ({{pointsInOrder.length }})
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
                         :trip="trip"
                         :countries="countries"/>
        </div>
      </div>

      <div class="w-full flex flex-col justify-end items-start px-2"
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
</template>
