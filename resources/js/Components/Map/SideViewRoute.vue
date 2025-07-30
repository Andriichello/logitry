<script setup lang="ts">
  import { computed, PropType, ref } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import {
    ArrowLeft,
    ArrowRight,
    Car,
    ChevronDown,
    ChevronRight,
    ChevronUp,
    FilterX,
    X,
    Play,
    Square,
    MapPin
  } from 'lucide-vue-next';
  import { minutesToHumanReadable, numberAsIntOrFloat, toHumanDate, toHumanTime } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import { useMapStore } from '@/stores/map';
  import { useThemeStore } from '@/stores/theme';
  import dayjs from 'dayjs';
  import SideViewStops from '@/Components/Map/SideViewStops.vue';
  import SideViewPrices from '@/Components/Map/SideViewPrices.vue';
  import { useToast } from 'vue-toastification';
  import ContactMe from '@/Components/Map/ContactMe.vue';

  const emits = defineEmits(['route-closed', 'trip-clicked', 'trip-closed', 'toggle-map']);

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

  const toast = useToast();

  const mapStore = useMapStore();
  const themeStore = useThemeStore();

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
  <div class="flex flex-col h-full">
    <!-- Back Button -->
    <div class="p-4 border-b" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'">
      <button
        @click="emits('route-closed', route)"
        class="flex items-center gap-2"
        :class="themeStore.isDark ? 'text-gray-200 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
      >
        <ArrowLeft class="w-4 h-4" />
        Route details
      </button>
    </div>

    <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 pb-20">
      <h2 class="text-xl font-bold mb-4" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ route.name }}</h2>

      <!-- Stops -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Stops ({{ route.points.length }})</h3>
          <button
            @click="hidePoints"
            class="text-sm border px-3 py-1 rounded"
            :class="themeStore.isDark
              ? 'text-gray-300 border-gray-600 hover:bg-gray-800'
              : 'text-gray-500 border-gray-300 hover:bg-gray-50'"
          >
            {{ !mapStore.arePointsHidden ? 'Hide list' : 'Show list' }}
          </button>
        </div>
        <p class="text-sm mb-3" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-600'">{{ minutesToHumanReadable(route.travel_time) }} travel time</p>

        <div v-if="!mapStore.arePointsHidden" class="space-y-3">
          <!-- Start Stop -->
          <div class="flex items-start gap-3" v-if="route.points.length > 0">
            <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mt-1">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
            </div>
            <div>
              <p class="font-medium" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ route.points[0].name }}</p>
              <div class="flex items-center gap-2 text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
                <span class="w-4 h-2 bg-red-500 rounded"></span>
                <span>{{ route.points[0].country }}</span>
              </div>
              <p class="text-sm text-gray-500">Start point</p>
            </div>
          </div>

          <!-- Middle Stops -->
          <div v-for="(point, index) in route.points.slice(1, -1)" :key="index" class="flex items-start gap-3">
            <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mt-1">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
            </div>
            <div>
              <p class="font-medium" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ point.name }}</p>
              <div class="flex items-center gap-2 text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
                <span class="w-4 h-2 bg-red-500 rounded"></span>
                <span>{{ point.country }}</span>
              </div>
              <p class="text-sm text-gray-500">{{ point.travel_time ? minutesToHumanReadable(point.travel_time) + ' from start' : '' }}</p>
            </div>
          </div>

          <!-- End Stop -->
          <div class="flex items-start gap-3" v-if="route.points.length > 1">
            <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mt-1">
              <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
            </div>
            <div>
              <p class="font-medium" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ route.points[route.points.length - 1].name }}</p>
              <div class="flex items-center gap-2 text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
                <span class="w-4 h-2 bg-red-500 rounded"></span>
                <span>{{ route.points[route.points.length - 1].country }}</span>
              </div>
              <p class="text-sm text-gray-500">Final destination</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Prices -->
      <div class="mb-6" v-if="route.prices?.length">
        <h3 class="font-semibold mb-1" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Prices</h3>
        <p class="text-sm mb-3" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'">may vary depending on the trip</p>
        <div class="flex items-center justify-between" v-if="route.base_price">
          <span :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
            <span v-if="route.base_price.unit === 'Seat'">seat</span>
            <span v-else-if="route.base_price.unit === 'Weight'">kg</span>
            <span v-else-if="route.base_price.unit === 'Volume'">m³</span>
          </span>
          <span class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">
            {{ numberAsIntOrFloat(route.base_price.from) }}
            <span class="text-xs text-gray-500">{{ route.base_price.currency }}</span>
          </span>
        </div>
      </div>

      <!-- Trips -->
      <Deferred data="trips">
        <template #fallback>
          <div class="flex flex-col items-center justify-center py-8">
            <Car class="w-8 h-8 mb-3" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'" />
            <span class="text-lg font-semibold" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">Loading trips...</span>
            <span class="loading loading-dots loading-md mt-2"></span>
          </div>
        </template>

        <div v-if="trips?.length">
          <h3 class="font-semibold mb-3" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Trips</h3>

          <div class="mb-3" v-if="forwardTrips.length && returnTrips.length">
            <span class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">Direction:</span>
            <div class="flex gap-2 mt-2">
              <button
                @click="tripsMode = 'forward'"
                :class="[
                  'px-3 py-1 rounded text-sm border',
                  tripsMode === 'forward'
                    ? 'bg-green-100 text-green-700 border-green-300'
                    : themeStore.isDark
                      ? 'bg-gray-700 text-gray-300 border-gray-600'
                      : 'bg-gray-100 text-gray-600 border-gray-300'
                ]"
              >
                Forward ({{ forwardTrips.length }})
              </button>
              <button
                @click="tripsMode = 'backward'"
                :class="[
                  'px-3 py-1 rounded text-sm border',
                  tripsMode === 'backward'
                    ? 'bg-red-100 text-red-700 border-red-300'
                    : themeStore.isDark
                      ? 'bg-gray-700 text-gray-300 border-gray-600'
                      : 'bg-gray-100 text-gray-600 border-gray-300'
                ]"
              >
                Return ({{ returnTrips.length }})
              </button>
              <button
                @click="tripsMode = 'all'"
                :class="[
                  'px-3 py-1 rounded text-sm border',
                  tripsMode === 'all'
                    ? 'bg-blue-100 text-blue-700 border-blue-300'
                    : themeStore.isDark
                      ? 'bg-gray-700 text-gray-300 border-gray-600'
                      : 'bg-gray-100 text-gray-600 border-gray-300'
                ]"
              >
                All
              </button>
            </div>
          </div>

          <!-- Trip List -->
          <div class="space-y-3">
            <div
              v-for="trip in filteredTrips"
              :key="trip.id"
              @click="emits('trip-clicked', trip)"
              class="flex items-center justify-between p-3 border rounded-lg cursor-pointer"
              :class="themeStore.isDark
                ? 'border-gray-700 hover:bg-gray-800'
                : 'border-gray-200 hover:bg-gray-50'"
            >
              <div class="flex items-center gap-3">
                <ArrowRight v-if="!trip.reversed" class="w-4 h-4 text-green-600" />
                <ArrowLeft v-else class="w-4 h-4 text-red-600" />
                <div>
                  <p class="font-medium" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">
                    {{ toHumanTime(trip.departs_at) }}
                    <template v-if="trip.arrives_at || arrivesAtsFromDurations?.[trip.id]">
                      - {{ trip.arrives_at ? toHumanTime(trip.arrives_at) : toHumanTime(arrivesAtsFromDurations[trip.id]) }}
                    </template>
                  </p>
                  <p class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
                    {{ toHumanDate(trip.departs_at) }}
                    <template v-if="trip.arrives_at || arrivesAtsFromDurations?.[trip.id]">
                      {{ trip.arrives_at && !trip.arrives_at.isSame(trip.departs_at, 'day') ? ' - ' + toHumanDate(trip.arrives_at) : '' }}
                      {{ !trip.arrives_at && arrivesAtsFromDurations?.[trip.id] && !arrivesAtsFromDurations[trip.id].isSame(trip.departs_at, 'day') ? ' - ' + toHumanDate(arrivesAtsFromDurations[trip.id]) : '' }}
                    </template>
                  </p>
                </div>
              </div>
              <div class="text-right" v-if="route.base_price">
                <p class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ numberAsIntOrFloat(route.base_price.from) }}</p>
                <p class="text-xs text-gray-500">
                  <span v-if="route.base_price.unit === 'Seat'">seat</span>
                  <span v-else-if="route.base_price.unit === 'Weight'">kg</span>
                  <span v-else-if="route.base_price.unit === 'Volume'">m³</span>
                  / {{ route.base_price.currency }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-8">
          <Car class="w-8 h-8 mb-3" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'" />
          <span class="text-lg font-semibold" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">No trips found</span>
        </div>
      </Deferred>
    </div>
  </div>
</template>
