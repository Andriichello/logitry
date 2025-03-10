<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import {
    ArrowLeftFromLine,
    ArrowRightFromLine,
    ChevronDown,
    ChevronUp,
    MapPin,
    MapPinHouse,
    X,
  } from 'lucide-vue-next';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { minutesToHumanReadable, toHumanDate, toHumanTime, toHumanWeekday, numberAsIntOrFloat } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import { useMapStore } from '@/stores/map';

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

  const durations = computed(() => {
    return props.route.points
      .map((p: Point) => p.travel_time)
      .slice(1);
  });

  const durationsInOrder = computed(() => {
    if (props.trip?.reversed) {
      return [...durations.value].reverse();
    }

    return durations.value;
  });

  const timesInOrder = computed(() => {
    if (!props.trip) {
      return [];
    }

    let beg = dayjs(props.trip.departs_at);
    let end = props.trip.arrives_at
     ? dayjs(props.trip.arrives_at) : null;

    let time = beg.clone();
    const times = [time];

    if (durationsInOrder.value.length === 0) {
      for (let i = 1; i < pointsInOrder.value.length; i++) {
        if (i === pointsInOrder.value.length - 1) {
          times.push(end);
        } else {
          times.push(null);
        }
      }

      return times;
    }

    for (const [index, d] of durationsInOrder.value.entries()) {
      if (d === null) {
        for (let i = index; i < durationsInOrder.value.length; i++) {
          times.push(null);
        }

        break;
      }

      time = time.add(d, 'minutes');
      times.push(time);
    }

    if (end) {
      times.pop();
      times.push(end);
    }

    return times;
  });

  function hidePoints() {
    mapStore.arePointsHidden = !mapStore.arePointsHidden;
  }
</script>

<template>
  <div class="w-full flex flex-col justify-between items-baseline gap-2 p-3 overflow-y-auto">
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
        <div class="w-full flex flex-row justify-between items-center pt-3 cursor-pointer"
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

        <div class="w-full flex flex-col justify-start items-start bg-base-200 rounded py-1 px-0.5"
             v-if="!mapStore.arePointsHidden">
          <ul class="timeline timeline-snap-icon timeline-compact timeline-vertical">
            <template v-for="(point, index) in pointsInOrder" :key="point.id">
              <li>
                <div class="timeline-middle">
                  <div class="px-1 pb-2">
                    <MapPinHouse class="w-6 h-6" v-if="index === 0 || index === pointsInOrder.length - 1"/>
                    <MapPin v-else/>
                  </div>
                </div>
                <div class="timeline-start"
                     :class="{'mb-4' : index !== (pointsInOrder.length - 1)}">
                  <time class="font-mono italic"
                        v-if="timesInOrder?.[index]">
                    {{ toHumanTime(timesInOrder[index]) }}<span class="text-sm"> ({{ toHumanWeekday(timesInOrder[index]) }}, {{ toHumanDate(timesInOrder[index]) }})</span>
                  </time>
                  <div class="w-full flex justify-start items-baseline"
                       v-if="point.city">
                              <span class="w-full">
                                <span class="text-lg font-semibold grow">{{ point.city }}</span><br>

                                <Deferred data="countries">
                                  <template #fallback>
                                    <span class="text-md">{{ getUnicodeFlagIcon(point.country) }} {{ point.country }} </span>
                                  </template>

                                  <span class="text-md font-light">{{ getUnicodeFlagIcon(point.country) }} {{ countries?.[point.country] ?? point.country?.toUpperCase() }} </span>
                                </Deferred>
                              </span>
                  </div>

                  <div v-else>
                    <span class="text-lg">{{ point.name }}</span>
                  </div>

                  <template v-if="trip.reversed">
                    <template v-if="index < (route.points.length - 1)">
                      <time class="font-mono italic" v-if="route.points[index + 1].travel_time">{{ minutesToHumanReadable(point.travel_time) }}</time>
                    </template>
                  </template>

                  <template v-else>
                    <time class="font-mono italic" v-if="index < (route.points?.length - 1) && route.points[index + 1].travel_time">{{ minutesToHumanReadable(route.points[index + 1].travel_time) }}</time>
                  </template>
                </div>
                <hr />
              </li>
            </template>
          </ul>
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

        <table class="table table-md bg-base-200">
          <tbody>
          <template v-for="price in prices" :key="price.id">
            <tr>
              <th class="text-start px-3 text-[16px]">
                  <span class="font-semibold">
                    <span v-if="price.unit === 'Seat'">seat</span>
                    <span v-else-if="price.unit === 'Volume'">m³</span>
                    <span v-else-if="price.unit === 'Weight'">kg</span>
                  </span>
              </th>
              <td class="text-[16px] w-full flex justify-start items-baseline gap-2">
                  <span class="font-bold font-mono">
                    {{ numberAsIntOrFloat(price.from) }}
                  </span>

                <template v-if="price.to">
                    <span class="font-medium font-mono">
                      -
                    </span>
                  <span class="font-bold font-mono">
                      {{ numberAsIntOrFloat(price.to) }}
                    </span>
                </template>

                <span class="text-[10px]">
                    {{ price.currency }}
                  </span>
              </td>
            </tr>
          </template>
          </tbody>
        </table>
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
