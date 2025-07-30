<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import { MapPin, MapPinHouse } from 'lucide-vue-next';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { minutesToHumanReadable, toHumanDate, toHumanTime, toHumanWeekday } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import { useThemeStore } from '@/stores/theme';
  import dayjs from 'dayjs';

  const emits = defineEmits([]);

  const themeStore = useThemeStore();

  const props = defineProps({
    route: {
      type: Object as PropType<Route>,
      required: true,
    },
    trip: {
      type: Object as PropType<Trip> | null,
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
  <div class="space-y-4">
    <!-- Start Stop -->
    <div class="flex items-start gap-3" v-if="pointsInOrder.length > 0">
      <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mt-1">
        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
      </div>
      <div>
        <p class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'" v-if="timesInOrder?.[0]">
          {{ toHumanTime(timesInOrder[0]) }} ({{ toHumanWeekday(timesInOrder[0]) }}, {{ toHumanDate(timesInOrder[0]) }})
        </p>
        <p class="font-medium" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">
          {{ pointsInOrder[0].city || pointsInOrder[0].name }}
        </p>
        <div class="flex items-center gap-2 text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
          <span>{{ getUnicodeFlagIcon(pointsInOrder[0].country) }}</span>
          <Deferred data="countries">
            <template #fallback>
              <span>{{ pointsInOrder[0].country }}</span>
            </template>
            <span>{{ countries?.[pointsInOrder[0].country] ?? pointsInOrder[0].country?.toUpperCase() }}</span>
          </Deferred>
        </div>
        <p class="text-sm text-gray-500">{{ trip ? 'Departure' : 'Start point' }}</p>
      </div>
    </div>

    <!-- Middle Stops -->
    <div v-for="(point, index) in pointsInOrder.slice(1, -1)" :key="point.id" class="flex items-start gap-3">
      <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mt-1">
        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
      </div>
      <div>
        <p class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'" v-if="timesInOrder?.[index + 1]">
          {{ toHumanTime(timesInOrder[index + 1]) }} ({{ toHumanWeekday(timesInOrder[index + 1]) }}, {{ toHumanDate(timesInOrder[index + 1]) }})
        </p>
        <p class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'" v-else-if="point.travel_time">
          {{ minutesToHumanReadable(point.travel_time) + ' from start' }}
        </p>
        <p class="font-medium" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">
          {{ point.city || point.name }}
        </p>
        <div class="flex items-center gap-2 text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
          <span>{{ getUnicodeFlagIcon(point.country) }}</span>
          <Deferred data="countries">
            <template #fallback>
              <span>{{ point.country }}</span>
            </template>
            <span>{{ countries?.[point.country] ?? point.country?.toUpperCase() }}</span>
          </Deferred>
        </div>
        <p class="text-sm text-gray-500" v-if="point.travel_time && !timesInOrder?.[index + 1]">
          {{ minutesToHumanReadable(point.travel_time) }}
        </p>
      </div>
    </div>

    <!-- End Stop -->
    <div class="flex items-start gap-3" v-if="pointsInOrder.length > 1">
      <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mt-1">
        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
      </div>
      <div>
        <p class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'" v-if="timesInOrder?.[pointsInOrder.length - 1]">
          {{ toHumanTime(timesInOrder[pointsInOrder.length - 1]) }} ({{ toHumanWeekday(timesInOrder[pointsInOrder.length - 1]) }}, {{ toHumanDate(timesInOrder[pointsInOrder.length - 1]) }})
        </p>
        <p class="font-medium" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">
          {{ pointsInOrder[pointsInOrder.length - 1].city || pointsInOrder[pointsInOrder.length - 1].name }}
        </p>
        <div class="flex items-center gap-2 text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
          <span>{{ getUnicodeFlagIcon(pointsInOrder[pointsInOrder.length - 1].country) }}</span>
          <Deferred data="countries">
            <template #fallback>
              <span>{{ pointsInOrder[pointsInOrder.length - 1].country }}</span>
            </template>
            <span>{{ countries?.[pointsInOrder[pointsInOrder.length - 1].country] ?? pointsInOrder[pointsInOrder.length - 1].country?.toUpperCase() }}</span>
          </Deferred>
        </div>
        <p class="text-sm text-gray-500">{{ trip ? 'Arrival' : 'Final destination' }}</p>
      </div>
    </div>
  </div>
</template>
