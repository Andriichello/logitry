<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { MapFilters } from '@/stores/map';
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
  <div class="w-full flex flex-col justify-start items-start gap-1">
    <p class="text-md opacity-80">
      Countries to search routes for
    </p>

    <button class="btn btn-lg h-fit btn-outline w-full flex flex-row justify-between items-center gap-2 px-3 mb-2 border-base-content/0">
      <div class="min-h-11 w-full flex flex-row justify-between items-center gap-2 py-1 font-normal"
           @click="emits('open-from')">
        <MapPin class="w-6 h-6" v-if="!from?.length"/>

        <Deferred data="countries">
          <template #fallback>
            <div v-for="a2 in from" :key="a2">
              <div class="flex justify-center items-center">{{ getUnicodeFlagIcon(a2.toUpperCase()) }}</div>
              <div>
                <span class="loading loading-dots loading-xs mt-1"/>
                {{ a2.toUpperCase() }}
              </div>
            </div>
          </template>

          <div class="w-full flex flex-wrap flex-row justify-start items-start pt-1" v-if="fromCountries?.length">
            <div v-for="(country, index) in fromCountries" :key="country.a2"
                 class="flex justify-center items-center gap-1">
              <div class="flex justify-center items-center">{{ getUnicodeFlagIcon(country.a2?.toUpperCase()) }}</div>
              <div>{{ country.a2?.toUpperCase() ?? country.name ?? '' }}</div>
              <div class="pr-2"
                   v-if="index !== fromCountries.length - 1">,</div>
            </div>
          </div>

          <div class="w-full flex flex-wrap flex-row justify-start items-center gap-1"
               v-else>
            <span class="mt-1">Countries</span>
          </div>
        </Deferred>

        <ChevronRight class="w-6 h-6"/>
      </div>
    </button>

    <p class="text-md opacity-80">
      Departure date or an interval
    </p>

    <button class="btn btn-lg h-fit btn-outline w-full flex flex-row justify-between items-center gap-2 px-3 border-base-content/0"
         @click="emits('open-calendar')">
      <div class="min-h-11 w-full w-full flex flex-row justify-between items-center gap-3 py-1 font-normal">
        <Calendar class="w-6 h-6"/>

        <span class="w-full text-start mt-0.5">
          <template v-if="props.filters.beg && props.filters.end">
            <div class="w-full flex justify-start items-center gap-2 mt-1">
              <div>{{ props.filters.beg?.format('ddd, DD MMM') ?? 'Start date' }}</div>

              <template v-if="!props.filters.beg?.isSame(props.filters.end, 'day')">
                <div class="font-normal px-2">-</div>
                <div>{{ props.filters.end?.format('ddd, DD MMM') ?? 'End Date' }}</div>
              </template>
            </div>
          </template>

          <template v-else-if="props.filters.beg">
            <div class="w-full flex justify-start items-center gap-2 mt-1">
              <div>{{ props.filters.beg?.format('ddd, DD MMM') ?? 'Start date' }}</div>
              <div class="font-normal px-2">-</div>
              <div>Future</div>
            </div>
          </template>

          <template v-else>
            <div class="w-full flex justify-start items-center gap-2 mt-1">
              <div>Start date</div>
              <div class="font-normal px-2">-</div>
              <div>End date</div>
            </div>
          </template>
        </span>

        <ChevronRight class="w-6 h-6"/>
      </div>
    </button>

    <button class="btn btn-lg h-fit btn-outline w-full flex flex-row justify-between items-center gap-2 px-3 border-base-content/0">
      <div class="min-h-11 w-full w-full flex flex-row justify-between items-center gap-3 py-1 font-normal"
           @click="emits('toggle-has-trips', hasTrips)">
        <Route class="w-6 h-6" :class="{'opacity-60': !hasTrips}"/>

        <span class="w-full text-start mt-0.5"
          :class="{'opacity-60': !hasTrips}">
          Only routes with trips
        </span>

        <input id="with_trips" type="checkbox" class="toggle" v-model="hasTrips"
          @change="emits('toggle-has-trips', !$event.target.checked)"/>
      </div>
    </button>
  </div>
</template>
