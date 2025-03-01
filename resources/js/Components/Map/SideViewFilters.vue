<script setup lang="ts">
  import { PropType, ref } from 'vue';
  import { Route, Trip } from '@/api';
  import { Route as RouteIcon, X, Search, ArrowRightLeft, MapPin, Calendar, Circle } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';
  import { useMapStore, MapFilters } from "@/stores/map";
  import { Deferred } from '@inertiajs/vue3';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';

  const emits = defineEmits(['open-from', 'open-where', 'open-calendar']);

  const props = defineProps({
    filters: {
      type: Object as PropType<MapFilters> | null,
      required: true,
    },
  });
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
                From?
              </div>

              <div class="w-full min-h-full flex justify-start items-center gap-2 text-md translate-y-[-4px] font-bold">
                <div>{{ props.filters?.from ?? 'Origin' }}</div>
                <div class="flex justify-center items-center" v-if="props.filters?.from">{{ getUnicodeFlagIcon(props.filters?.from === 'Ukraine' ? 'UA' : props.filters?.from === 'Slovakia' ? 'SK' : props.filters?.from) }}</div>
              </div>
            </div>
          </div>
          <div class="w-full border-t-1 opacity-15" />
          <div class="w-full min-h-full flex justify-start items-center gap-2 font-bold text-md">
            <div class="w-full flex flex-col justify-start items-start px-2 py-1 rounded cursor-pointer hover:bg-base-300">
              <div class="text-xs font-bold text-gray-500 filter-hint">
                Where?
              </div>

              <div class="w-full min-h-full flex justify-start items-center gap-2 text-md translate-y-[-4px]">
                <div>{{ props.filters?.to ?? 'Destination' }}</div>
                <div class="flex justify-center items-center" v-if="props.filters?.to">{{ getUnicodeFlagIcon(props.filters?.to === 'Ukraine' ? 'UA' : props.filters?.to === 'Slovakia' ? 'SK' : props.filters?.to) }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex p-3 hover:bg-base-300 rounded cursor-pointer ml-2">
          <ArrowRightLeft class="w-4 h-4 rotate-90"/>
        </div>
      </div>
    </div>

    <div class="w-full flex justify-start items-center gap-2 pt-1">
      <div class="w-full flex flex-col justify-start items-start gap-1">
        <div class="w-full border-t-1 opacity-15"/>
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
