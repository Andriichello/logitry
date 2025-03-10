<script setup lang="ts">
  import { computed, PropType } from 'vue';
  import { Point, Route, Trip } from '@/api';
  import { MapPin, MapPinHouse } from 'lucide-vue-next';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { minutesToHumanReadable, numberAsIntOrFloat, toHumanDate, toHumanTime, toHumanWeekday } from '@/helpers';
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
  });
</script>

<template>
  <table class="w-fit table table-md text-md">
    <tbody class="">
      <template v-for="price in route.prices" :key="price.id">
        <tr>
          <th class="text-[16px] text-start px-3">
            <span class="font-semibold">
              <span v-if="price.unit === 'Seat'">seat</span>
              <span v-else-if="price.unit === 'Volume'">m³</span>
              <span v-else-if="price.unit === 'Weight'">kg</span>
            </span>
          </th>
          <td class="text-lg w-full flex justify-start items-baseline gap-2">
            <span class="font-bold font-mono ">
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
</template>
