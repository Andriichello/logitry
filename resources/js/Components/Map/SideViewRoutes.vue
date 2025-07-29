<script setup lang="ts">
  import { computed, PropType, ref } from 'vue';
  import { Route, Trip } from '@/api';
  import {ArrowLeft, ChevronRight, FilterX, Route as RouteIcon, MapPin} from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
  import { MapFilters } from '@/stores/map';
  import { useThemeStore } from '@/stores/theme';
  import {Deferred, router} from '@inertiajs/vue3';
  import SideViewFilters from '@/Components/Map/SideViewFilters.vue';
  import dayjs from 'dayjs';
  import { useToast } from 'vue-toastification';
  import ContactMe from '@/Components/Map/ContactMe.vue';
  import OutlineButton from '@/Components/Reusable/OutlineButton.vue';

  const emits = defineEmits([
    'toggle-has-trips',
    'change-page',
    'open-from',
    'open-where',
    'swap-from-and-where',
    'open-calendar',
    'clear-filters',
    'route-clicked',
    'back-to-company',
    'toggle-map',
  ]);

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
    countries: {
      type: Object as PropType<Record<string, string>> | null,
    },
    meta: {
      type: Object as PropType<Record<string, any>> | null,
    },
  });

  const toast = useToast();
  const themeStore = useThemeStore();

  const isClearFiltersModal = ref(false);
  const showClearFilters = computed(() => {
    return props.filters?.from?.length
      || props.filters?.to?.length
      || props.filters?.has_trips
      || props.filters?.end
      || (props.filters?.beg && !props.filters.beg.isSame(dayjs(), 'day'));
  });

  /**
   * Computes the list of pages to display for pagination.
   */
  const paginationPages = computed(() => {
    if (!props.meta) return [];
    const { current_page, last_page } = props.meta;

    const pages = [];
    if (last_page <= 5) {
      // Case 1: Last page <= 5, show all pages
      for (let i = 1; i <= last_page; i++) {
        pages.push(i);
      }
    } else {
      // Case 2: More than 5 pages
      if (current_page <= 3) {
        // Show first 4 pages and last page
        pages.push(1, 2, 3, 4, '...', last_page);
      } else if (current_page >= last_page - 2) {
        // Show first page and last 4 pages
        pages.push(1, '...', last_page - 3, last_page - 2, last_page - 1, last_page);
      } else {
        // Show first page, 3 around the current, and last page
        pages.push(
          1,
          '...',
          current_page - 1,
          current_page,
          current_page + 1,
          '...',
          last_page
        );
      }
    }
    return pages;
  });

  function toggleHasTrips(hasTrips: boolean) {
    emits('toggle-has-trips', hasTrips);
  }
</script>

<template>
  <div class="w-full h-full flex flex-col">
    <!-- Back Button -->
    <div class="p-4 border-b" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'">
      <button
        @click="emits('back-to-company')"
        class="flex items-center gap-2"
        :class="themeStore.isDark ? 'text-gray-200 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
      >
        <ArrowLeft class="w-4 h-4" />
        Routes
      </button>
    </div>

    <!-- Filters -->
    <div class="p-4 border-b" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Filters</h3>
        <button
          v-if="showClearFilters || (!routes.length && meta?.total > 1)"
          @click="showClearFilters ? isClearFiltersModal = true : emits('clear-filters')"
          class="text-sm border px-3 py-1 rounded flex items-center gap-1"
          :class="themeStore.isDark ? 'text-gray-300 border-gray-600 hover:bg-gray-800' : 'text-gray-500 border-gray-300 hover:bg-gray-50'"
        >
          <FilterX class="w-3 h-3" />
          Clear
        </button>
      </div>

      <SideViewFilters
        :filters="filters"
        :countries="countries"
        @toggle-has-trips="toggleHasTrips"
        @swap-from-and-where="emits('swap-from-and-where')"
        @open-from="emits('open-from')"
        @open-where="emits('open-where')"
        @open-calendar="emits('open-calendar')"
      />
    </div>

    <!-- Routes List -->
    <div class="flex-1 overflow-y-auto">
      <div class="p-4">
        <h3 class="font-semibold mb-2" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Routes</h3>
        <p class="text-sm mb-4" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-600'">Here are your search results</p>

        <div class="space-y-3" v-if="props.routes?.length">
          <div
            v-for="route in props.routes"
            :key="route.id"
            @click="emits('route-clicked', route)"
            class="p-4 border rounded-lg cursor-pointer"
            :class="themeStore.isDark
              ? 'border-gray-700 hover:bg-gray-800'
              : 'border-gray-200 hover:bg-gray-50'"
          >
            <h4 class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ route.name }}</h4>
            <div class="flex items-center gap-4 mt-2 text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">
              <Deferred data="trips">
                <template #fallback>
                  <span><span class="loading loading-dots loading-xs mr-1"/>trips</span>
                </template>

                <span v-if="trips?.filter(trip => trip.route_id === route.id)?.length">
                  {{ trips?.filter(trip => trip.route_id === route.id)?.length }} {{ trips?.filter(trip => trip.route_id === route.id)?.length > 1 ? 'trips' : 'trip' }}
                </span>
                <span v-else>No trips</span>
              </Deferred>
              <span v-if="route.points?.length">{{ route.points?.length }} stops</span>
              <span v-if="route.travel_time">{{ minutesToHumanReadable(route.travel_time) }}</span>
            </div>
          </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-8">
          <RouteIcon class="w-8 h-8 mb-3" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'" />
          <span class="text-lg font-semibold mb-4" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">No routes found</span>
          <button
            v-if="showClearFilters || (!routes.length && meta?.total > 1)"
            @click="emits('clear-filters')"
            class="px-4 py-2 border rounded-lg flex items-center gap-2"
            :class="themeStore.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-800' : 'border-gray-300 text-gray-600 hover:bg-gray-50'"
          >
            <FilterX class="w-4 h-4" />
            Clear all filters
          </button>
        </div>

        <p v-if="routes.length && meta?.total > 1" class="text-xs text-gray-500 mt-4">
          Showing {{ meta.from }}-{{ meta.to }} of {{ meta.total }}
        </p>

        <div v-if="routes.length && meta?.total > 1 && meta.last_page !== 1" class="flex justify-center mt-4">
          <div class="flex items-center gap-2">
            <template v-for="page in paginationPages" :key="page">
              <button
                v-if="page !== '...'"
                @click="page !== meta.current_page && emits('change-page', page)"
                class="w-8 h-8 flex items-center justify-center rounded"
                :class="page === meta.current_page
                  ? (themeStore.isDark ? 'bg-gray-700 text-white' : 'bg-gray-200 text-gray-800')
                  : (themeStore.isDark ? 'text-gray-300 hover:bg-gray-800' : 'text-gray-600 hover:bg-gray-100')"
              >
                {{ page }}
              </button>
              <span v-else class="text-gray-500">...</span>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile View on Map Button -->
    <div class="lg:hidden p-4 border-t" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'">
      <button
        @click="emits('toggle-map')"
        class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg flex items-center justify-center gap-2 hover:bg-purple-700"
      >
        <MapPin class="w-4 h-4" />
        View on Map
      </button>
    </div>

    <!-- Clear Filters Modal -->
    <div v-if="isClearFiltersModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-xs w-full" :class="themeStore.isDark ? 'bg-gray-800' : 'bg-white'">
        <h3 class="text-lg font-bold mb-4" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">Clear all filters?</h3>
        <div class="flex gap-3">
          <button
            @click="isClearFiltersModal = false"
            class="flex-1 py-2 border rounded"
            :class="themeStore.isDark ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50'"
          >
            No
          </button>
          <button
            @click="emits('clear-filters'); isClearFiltersModal = false"
            class="flex-1 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          >
            Yes
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .list-row::after {
    border-color: color-mix(in oklab,var(--color-base-content)30%,#0000);
  }
</style>
