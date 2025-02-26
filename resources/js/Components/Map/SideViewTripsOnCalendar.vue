<script setup lang="ts">
  import { PropType } from 'vue';
  import { Route } from '@/api';
  import { Calendar1, Car, X } from 'lucide-vue-next';
  import { toHumanDate, toHumanTime } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';

  const props = defineProps({
    route: {
      type: Object as PropType<Route>,
      required: true,
    },
    trips: {
      type: Array as PropType<Trip[]> | null,
      required: true,
    },
  });
</script>

<template>
  <div class="w-full flex flex-col justify-between items-baseline gap-2 p-3 overflow-y-auto">
    <div class="w-full flex flex-row justify-between items-baseline gap-2">
      <h3 class="text-md font-semibold">
        Route
      </h3>

      <div class="rounded flex justify-center items-center cursor-pointer p-2 translate-x-[8px]">
        <X class="w-5 h-5"/>
      </div>
    </div>

    <div class="w-full flex flex-row justify-between items-baseline gap-2 px-2">
      <h3 class="text-xl font-semibold">
        {{ route.name ?? 'Route' }}
      </h3>
    </div>

    <div class="w-full h-full flex flex-col justify-start items-center overflow-y-auto pb-10">
      <Deferred data="trips">
        <template #fallback>
          <div class="flex flex-col justify-center items-center gap-2 p-4 mt-4">
            <Car class="w-8 h-8"/>

            <span class="text-lg font-bold">Loading trips</span>
            <span class="loading loading-dots loading-md"></span>
          </div>
        </template>

        <div class="w-full flex flex-col justify-start items-center pt-4"
             v-if="trips?.length">
          <div>
          <VueDatePicker />
          </div>

          <div class="w-full flex flex-col justify-start items-start">

            <div class="w-full flex flex-row justify-between items-baseline gap-2">
              <h3 class="text-md font-semibold">
                Trips <span class="text-sm font-light"></span>
              </h3>

              <div class="rounded flex justify-center items-center cursor-pointer p-2 pr-0 gap-2 text-info"
                @click="toast.info('Not implemented yet!', { timeout: 3000, position: 'bottom-left'})">
                <span class="font-semibold">View on</span>
                <Calendar1 class="w-6 h-6"/>
              </div>
            </div>

            <template v-for="(trip, index) in trips" :key="trip.id">
              <div class="w-full flex flex-col justify-start items-center">
                <div class="w-full border-t-2 opacity-15" v-if="index > 0"/>

                <div class="w-full flex flex-row justify-start items-center cursor-pointer">

                  <div class="w-full flex flex-row justify-start items-baseline gap-2 p-1 pb-2">
                    <div class="flex flex-col justify-start items-baseline">
                      <span class="text-lg font-semibold">{{ toHumanTime(trip.departs_at) }}</span>
                      <span class="text-md">{{ toHumanDate(trip.departs_at) }}</span>
                    </div>

                    <span class="text-2xl"> - </span>

                    <div class="flex flex-col justify-start items-baseline">
                      <span class="text-lg font-semibold">{{ toHumanTime(trip.arrives_at) }}</span>
                      <span class="text-md">{{ toHumanDate(trip.arrives_at) }}</span>
                    </div>
                  </div>

                  <div class="flex flex-col justify-start items-baseline">
                    <span class="w-full text-lg font-semibold text-end">12.99</span>
                    <span class="w-full text-xs font-semibold text-end">USD</span>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div class="flex flex-col justify-center items-center gap-2 p-4"
             v-else>
          <Car class="w-8 h-8"/>

          <span class="text-lg font-bold">No trips found</span>
        </div>
      </Deferred>
    </div>
  </div>
</template>
