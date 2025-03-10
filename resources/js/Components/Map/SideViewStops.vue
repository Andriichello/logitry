<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import { MapPin, MapPinHouse } from 'lucide-vue-next';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { minutesToHumanReadable, toHumanDate, toHumanTime, toHumanWeekday } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import dayjs from 'dayjs';

  const emits = defineEmits([]);

  const props = defineProps({
    route: {
      type: Object as PropType<Route>,
      required: true,
    },
    trip: {
      type: Array as PropType<Trip[]> | null,
      default: null,
    },
    countries: {
      type: Object as PropType<Record<string, string>> | null,
      default: null,
    },
  });

  const pointsInOrder = computed(() => {
    if (props.trip?.reversed) {
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
</script>

<template>
  <ul class="timeline timeline-snap-icon timeline-compact timeline-vertical">
    <template v-for="(point, index) in pointsInOrder" :key="point.id">
      <li>
        <hr class="bg-current opacity-20"
            v-if="index !== 0"/>

        <div class="timeline-middle">
          <div class="px-1 pb-1"
               :class="{'pt-1': index !== 0}">
            <MapPinHouse class="w-6 h-6 translate-x-[2px]"
                         v-if="index === 0 || index === pointsInOrder.length - 1"/>
            <MapPin class="w-6 h-6"
                    v-else/>
          </div>
        </div>

        <div class="timeline-start"
             :class="{'pt-1': index !== 0}">
          <time class="font-mono italic"
                v-if="timesInOrder?.[index]">
            {{ toHumanTime(timesInOrder[index]) }}<span class="text-sm"> ({{ toHumanWeekday(timesInOrder[index]) }}, {{ toHumanDate(timesInOrder[index]) }})</span>
          </time>
          <div class="w-full flex justify-start items-start"
               v-if="point.city">
            <div class="w-full flex flex-col justify-start items-start">
              <span class="text-lg font-semibold grow pt-1">{{ point.city }}</span>

              <Deferred data="countries">
                <template #fallback>
                  <span class="text-md">{{ getUnicodeFlagIcon(point.country) }} {{ point.country }} </span>
                </template>

                <span class="text-md font-light">{{ getUnicodeFlagIcon(point.country) }} {{ countries?.[point.country] ?? point.country?.toUpperCase() }} </span>
              </Deferred>
            </div>
          </div>

          <div v-else>
            <span class="text-lg">{{ point.name }}</span>
          </div>

          <template v-if="trip?.reversed">
            <template v-if="index < (route.points.length - 1)">
              <time class="font-mono italic" v-if="route.points[index + 1].travel_time">{{ minutesToHumanReadable(point.travel_time) }}</time>
            </template>
          </template>

          <template v-else>
            <time class="font-mono italic" v-if="index < (route.points?.length - 1) && route.points[index + 1].travel_time">{{ minutesToHumanReadable(route.points[index + 1].travel_time) }}</time>
          </template>
        </div>

        <hr class="bg-current opacity-20"
            v-if="index !== route.points.length - 1"/>
      </li>
    </template>
  </ul>
</template>
