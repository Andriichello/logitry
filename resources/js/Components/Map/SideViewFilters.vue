<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { MapFilters } from '@/stores/map';
  import { useThemeStore } from '@/stores/theme';
  import { Deferred } from '@inertiajs/vue3';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { Calendar, ChevronRight, MapPin, Route } from 'lucide-vue-next';

  const emits = defineEmits(['toggle-has-trips', 'open-from', 'open-where', 'swap-from-and-where', 'open-calendar']);

  const props = defineProps({
    filters: {
      type: Object as PropType<MapFilters> | null,
      required: true,
    },
    countries: Object as PropType<Record<string, string>> | null,
  });

  const themeStore = useThemeStore();

  const hasTrips = computed(() => {
    return props.filters?.has_trips;
  });

  const from = computed(() => {
    return props.filters?.from?.split(',') ?? [];
  });

  const to = computed(() => {
    return props.filters?.to?.split(',') ?? [];
  });

  const fromCountries = computed(() => {
    return (from.value?.map(a2 => findCountry(a2)) ?? [])
      .filter(country => !!country);
  });

  const toCountries = computed(() => {
    return (from.to?.map(a2 => findCountry(a2)) ?? [])
      .filter(country => !!country);
  });

  function findCountry(a2: string | null) {
    if (!a2) {
      return null;
    }

    if (!props.countries) {
      return null;
    }

    const key = a2?.trim().toLowerCase();

    if (!key?.length) {
      return null;
    }

    const country = props.countries?.[key];

    return { a2: key, name: country, flag: getUnicodeFlagIcon(key.toUpperCase()) };
  }
</script>

<template>
  <div class="w-full flex flex-col gap-4">
    <!-- Date Filter -->
    <div class="mb-4">
      <button
        @click="emits('open-calendar')"
        class="w-full flex items-center justify-between p-3 border rounded-lg"
        :class="themeStore.isDark
          ? 'border-gray-600 hover:bg-gray-800 text-gray-200'
          : 'border-gray-300 hover:bg-gray-50 text-gray-700'"
      >
        <div class="flex items-center gap-2">
          <Calendar class="w-4 h-4" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'" />
          <span class="text-sm">
            <template v-if="props.filters.beg && props.filters.end">
              {{ props.filters.beg?.format('ddd, DD MMM') }} - {{ props.filters.end?.format('ddd, DD MMM') }}
            </template>
            <template v-else-if="props.filters.beg">
              {{ props.filters.beg?.format('ddd, DD MMM') }} - Future
            </template>
            <template v-else>
              Start date - End date
            </template>
          </span>
        </div>
        <ChevronRight class="w-4 h-4" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-400'" />
      </button>
    </div>

    <!-- Routes with trips toggle -->
    <div class="flex items-center justify-between">
      <span class="text-sm" :class="themeStore.isDark ? 'text-gray-300' : 'text-gray-600'">Only routes with trips</span>
      <button
        @click="emits('toggle-has-trips', hasTrips)"
        :class="[
          'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
          hasTrips
            ? 'bg-purple-600'
            : themeStore.isDark
              ? 'bg-gray-600'
              : 'bg-gray-200'
        ]"
      >
        <span
          :class="[
            'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
            hasTrips ? 'translate-x-6' : 'translate-x-1'
          ]"
        />
      </button>
    </div>

    <!-- Countries filter (hidden) -->
    <div v-if="false" class="mb-4">
      <button
        @click="emits('open-from')"
        class="w-full flex items-center justify-between p-3 border rounded-lg"
        :class="themeStore.isDark
          ? 'border-gray-600 hover:bg-gray-800 text-gray-200'
          : 'border-gray-300 hover:bg-gray-50 text-gray-700'"
      >
        <div class="flex items-center gap-2">
          <MapPin class="w-4 h-4" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'" />
          <span class="text-sm">
            <Deferred data="countries">
              <template #fallback>
                <span>Loading countries...</span>
              </template>

              <span v-if="fromCountries?.length">
                <span v-for="(country, index) in fromCountries" :key="country.a2">
                  {{ getUnicodeFlagIcon(country.a2?.toUpperCase()) }} {{ country.a2?.toUpperCase() }}
                  <span v-if="index !== fromCountries.length - 1">, </span>
                </span>
              </span>
              <span v-else>Countries</span>
            </Deferred>
          </span>
        </div>
        <ChevronRight class="w-4 h-4" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-400'" />
      </button>
    </div>
  </div>
</template>
