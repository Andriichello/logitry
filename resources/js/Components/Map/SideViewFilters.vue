<script setup lang="ts">
  import { computed, inject, PropType, ref, watch } from 'vue';
  import { Route, Trip } from '@/api';
  import { Route as RouteIcon, X, Search, ArrowRightLeft, MapPin, Calendar, Circle } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
  import { useMapStore, MapFilters } from "@/stores/map";
  import { Deferred, usePage } from '@inertiajs/vue3';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';

  const emits = defineEmits(['open-from', 'open-where', 'swap-from-and-where', 'open-calendar']);

  const props = defineProps({
    filters: {
      type: Object as PropType<MapFilters> | null,
      required: true,
    },
    countries: Object as PropType<Record<string, string>> | null,
  });

  const from = computed(() => {
    return props.filters?.from?.split(',') ?? [];
  });

  const to = computed(() => {
    return props.filters?.to?.split(',') ?? [];
  });

  const fromCountries = computed(() => {
    return from.value?.map(a2 => findCountry(a2)) ?? [];
  });

  const toCountries = computed(() => {
    return to.value?.map(a2 => findCountry(a2));
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
  };
</script>

<template>
  <div class="w-full flex flex-col justify-center items-center">
    <div class="w-full flex justify-start items-center gap-2">
      <div class="w-full flex justify-center items-center">
        <div class="w-full flex flex-col justify-start items-start gap-1">
          <div class="w-full min-h-full flex justify-start items-center gap-2 text-md">
            <div class="w-full flex flex-col justify-start items-start px-2 py-1 rounded cursor-pointer hover:bg-base-300"
                 @click="emits('open-from')">
              <div class="text-xs text-gray-500 font-bold">
                Countries
              </div>

              <div class="w-full min-h-full flex justify-start items-center gap-2 text-md translate-y-[-4px] font-bold">
                <Deferred data="countries">
                  <template #fallback>
                    <div v-for="a2 in from" :key="a2">
                      <div class="flex justify-center items-center">{{ getUnicodeFlagIcon(a2?.toUpperCase()) }}</div>
                      <div>
                        <span class="loading loading-dots loading-xs"/>
                        {{ a2?.toUpperCase() ?? 'Countries' }}
                      </div>
                    </div>
                  </template>

                  <div class="w-full flex flex-wrap flex-row justify-start items-start" v-if="fromCountries?.length">
                    <div v-for="(country, index) in fromCountries" :key="country.a2"
                         class="flex justify-center items-center gap-1">
                      <div class="flex justify-center items-center">{{ getUnicodeFlagIcon(country.a2?.toUpperCase()) }}</div>
                      <div>{{ country.a2?.toUpperCase() ?? country.name ?? '' }}</div>
                      <div class="pr-2"
                        v-if="index !== fromCountries.length - 1">,</div>
                    </div>
                  </div>

                  <div class="w-full flex flex-wrap flex-row justify-start items-start" v-else>
                    Countries
                  </div>
                </Deferred>

<!--                <Deferred data="countries">-->
<!--                  <template #fallback>-->
<!--                    <template v-for="a2 in from" :key="a2">-->
<!--                      <div class="flex justify-center items-center">{{ getUnicodeFlagIcon(a2?.toUpperCase()) }}</div>-->
<!--                      <div>-->
<!--                        <span class="loading loading-dots loading-xs"/>-->
<!--                        {{ a2?.toUpperCase() ?? 'Countries' }}-->
<!--                      </div>-->
<!--                    </template>-->
<!--                  </template>-->

<!--                  <template v-if="!fromCountries?.length">-->
<!--                    <template v-for="country in fromCountries" :key="country.a2">-->
<!--                      <div class="flex justify-center items-center">{{ getUnicodeFlagIcon(country.a2?.toUpperCase()) }}</div>-->
<!--                      <div>-->
<!--                        <span class="loading loading-dots loading-xs"/>-->
<!--                        {{ country.a2?.toUpperCase() ?? 'Countries' }}-->
<!--                      </div>-->
<!--                    </template>-->

<!--                    <div class="flex justify-center items-center" v-if="props.filters?.from">{{ getUnicodeFlagIcon(props.filters?.from?.toUpperCase()) }}</div>-->
<!--                    <div>{{ props.filters?.from ?? 'Countries' }}</div>-->
<!--                  </template>-->

<!--                  <template v-else>-->
<!--                    <div class="flex justify-center items-center" v-if="fromCountry.flag">{{ fromCountry.flag }}</div>-->
<!--                    <div>{{ fromCountry.name ?? fromCountry.a2 ?? props.filters?.from }}</div>-->
<!--                  </template>-->
<!--                </Deferred>-->
              </div>
            </div>
          </div>
<!--          <div class="w-full border-t-1 opacity-15" />-->
<!--          <div class="w-full min-h-full flex justify-start items-center gap-2 font-bold text-md">-->
<!--            <div class="w-full flex flex-col justify-start items-start px-2 py-1 rounded cursor-pointer hover:bg-base-300"-->
<!--                 @click="emits('open-where')">-->
<!--              <div class="text-xs font-bold text-gray-500 filter-hint">-->
<!--                Where?-->
<!--              </div>-->

<!--              <div class="w-full min-h-full flex justify-start items-center gap-2 text-md translate-y-[-4px]">-->
<!--                <Deferred data="countries">-->
<!--                  <template #fallback>-->
<!--                    <div class="flex justify-center items-center" v-if="props.filters?.to">{{ getUnicodeFlagIcon(props.filters?.to?.toUpperCase()) }}</div>-->
<!--                    <div>-->
<!--                      <span class="loading loading-dots loading-xs" v-if="props.filters?.to"/>-->
<!--                      {{ props.filters?.to ?? 'Destination' }}-->
<!--                    </div>-->
<!--                  </template>-->

<!--                  <template v-if="!toCountry">-->
<!--                    <div class="flex justify-center items-center" v-if="props.filters?.to">{{ getUnicodeFlagIcon(props.filters?.to?.toUpperCase()) }}</div>-->
<!--                    <div>{{ props.filters?.to ?? 'Destination' }}</div>-->
<!--                  </template>-->

<!--                  <template v-else>-->
<!--                    <div class="flex justify-center items-center" v-if="toCountry.flag">{{ toCountry.flag }}</div>-->
<!--                    <div>{{ toCountry.name ?? toCountry.a2 ?? props.filters?.to }}</div>-->
<!--                  </template>-->
<!--                </Deferred>-->

<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
        </div>

<!--        <div class="flex p-3 hover:bg-base-300 rounded cursor-pointer ml-2"-->
<!--          @click="emits('swap-from-and-where')">-->
<!--          <ArrowRightLeft class="w-4 h-4 rotate-90"/>-->
<!--        </div>-->
      </div>
    </div>

    <div class="w-full flex justify-start items-center gap-2 pt-1">
      <div class="w-full flex flex-col justify-start items-start gap-1">
        <div class="w-full border-t-2 opacity-15"/>
        <div class="w-full min-h-full flex justify-start items-center gap-3 font-bold text-md">
          <div class="w-full flex flex-col justify-start items-start px-2 py-1 rounded cursor-pointer hover:bg-base-300"
               @click="emits('open-calendar')">
            <div class="text-xs font-bold text-gray-500 filter-hint">
              Departure
            </div>

            <div class="w-full min-h-full flex justify-start items-center gap-2 text-md">
              <template v-if="props.filters.beg && props.filters.end">
                <div class="w-full flex justify-start items-center gap-2">
                  <div>{{ props.filters.beg?.format('ddd, DD MMM') ?? 'Start date' }}</div>

                  <template v-if="!props.filters.beg?.isSame(props.filters.end, 'day')">
                    <div>-</div>
                    <div>{{ props.filters.end?.format('ddd, DD MMM') ?? 'End Date' }}</div>
                  </template>
                </div>
              </template>

              <template v-else-if="props.filters.beg">
                <div class="w-full flex justify-start items-center gap-2">
                  <div>{{ props.filters.beg?.format('ddd, DD MMM') ?? 'Start date' }}</div>
                  <div>-</div>
                  <div>Future</div>
                </div>
              </template>

              <template v-else>
                <div class="w-full flex justify-start items-center gap-2">
                  <div>Start date</div>
                  <div>-</div>
                  <div>End date</div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
