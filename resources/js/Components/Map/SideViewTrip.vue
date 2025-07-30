<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import {ArrowLeft, ArrowRight, ChevronDown, ChevronUp, X, Play, Square, MapPin} from 'lucide-vue-next';
  import {minutesToHumanReadable, toHumanDate, toHumanTime, toHumanWeekday} from '@/helpers';
  import { useMapStore } from '@/stores/map';
  import { useThemeStore } from '@/stores/theme';
  import SideViewStops from '@/Components/Map/SideViewStops.vue';
  import SideViewPrices from '@/Components/Map/SideViewPrices.vue';

  const emits = defineEmits(['trip-clicked', 'trip-closed', 'toggle-map']);

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
  const themeStore = useThemeStore();

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
  <div class="flex flex-col h-full">
    <!-- Back Button -->
    <div class="p-4 border-b" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'">
      <button
        @click="emits('trip-closed', trip)"
        class="flex items-center gap-2"
        :class="themeStore.isDark ? 'text-gray-200 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
      >
        <ArrowLeft class="w-4 h-4" />
        Trip details
      </button>
    </div>

    <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 pb-20">
      <div class="mb-4">
        <h2 class="text-xl font-bold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ route.name }}</h2>
        <span class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
          {{ toHumanTime(trip.departs_at) }} ({{ toHumanWeekday(trip.departs_at) }}, {{ toHumanDate(trip.departs_at) }})
        </span>
        <div class="flex items-center gap-2 mt-2">
          <ArrowRight v-if="!trip?.reversed" class="w-4 h-4 text-green-600" />
          <ArrowLeft v-else class="w-4 h-4 text-red-600" />
          <span class="px-2 py-1 rounded text-xs"
                :class="trip?.reversed ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'">
            {{ trip?.reversed ? 'Return' : 'Forward' }}
          </span>
        </div>
      </div>

      <!-- Stops -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Stops ({{ pointsInOrder.length }})</h3>
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

        <SideViewStops
          v-if="!mapStore.arePointsHidden"
          :route="route"
          :trip="trip"
          :countries="countries"
        />
      </div>

      <!-- Prices -->
      <div v-if="prices?.length">
        <h3 class="font-semibold mb-1" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Prices</h3>
        <p class="text-sm mb-3" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'">for the current trip</p>

        <div class="flex items-center justify-between" v-for="price in prices" :key="price.id">
          <span :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
            <span v-if="price.unit === 'Seat'">seat</span>
            <span v-else-if="price.unit === 'Weight'">kg</span>
            <span v-else-if="price.unit === 'Volume'">m³</span>
          </span>
          <span class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">
            {{ price.from }}
            <span class="text-xs text-gray-500">{{ price.currency }}</span>
          </span>
        </div>
      </div>
    </div>


  </div>
</template>
