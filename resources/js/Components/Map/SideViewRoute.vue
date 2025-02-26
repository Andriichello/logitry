<script setup lang="ts">
  import { PropType, ref } from 'vue';
  import { Route } from '@/api';
  import { Calendar1, Car, ChevronDown, ChevronUp, MapPin, MapPinHouse, X } from 'lucide-vue-next';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { minutesToHumanReadable, toHumanDate, toHumanTime } from '@/helpers';
  import { Deferred } from '@inertiajs/vue3';
  import { useToast } from 'vue-toastification';

  const emits = defineEmits(['route-closed', 'trip-clicked']);

  const props = defineProps({
    route: {
      type: Object as PropType<Route>,
      required: true,
    },
    trips: {
      type: Array as PropType<Trip[]> | null,
      required: true,
    }
  });

  const toast = useToast();

  const arePointsHidden = ref(false);

  function hidePoints() {
    arePointsHidden.value = !arePointsHidden.value;
  }
</script>

<template>
  <div class="w-full flex flex-col justify-between items-baseline gap-2 p-3 overflow-y-auto">
    <div class="w-full flex flex-row justify-between items-baseline gap-2">
      <h3 class="text-md font-semibold">
        Route
      </h3>

      <div class="rounded flex justify-center items-center cursor-pointer p-2 translate-x-[8px]"
           @click="emits('route-closed', route)">
        <X class="w-5 h-5"/>
      </div>
    </div>

    <div class="w-full flex flex-row justify-between items-baseline gap-2 px-2">
      <h3 class="text-xl font-semibold">
        {{ route.name ?? 'Route' }}
      </h3>
    </div>

    <div class="w-full h-full flex flex-col justify-start items-center overflow-y-auto pb-10">
      <div class="w-full flex flex-col justify-start items-start gap-1"
           v-if="route.points?.length">

        <div class="w-full flex flex-row justify-between items-end gap-2 p-2 px-4">
          <div class="flex flex-row justify-start items-center gap-2 translate-x-[-10px] cursor-pointer"
               @click="hidePoints">

            <label class="swap swap-rotate">
              <input type="checkbox" v-model="arePointsHidden"/>
              <ChevronDown class="swap-on w-6 h-6" @click="hidePoints"/>
              <ChevronUp class="swap-off w-6 h-6" @click="hidePoints"/>
            </label>

            <h3 class="text-lg users">
              Stops ({{route.points?.length }})
            </h3>
          </div>

          <div class="flex flex-row justify-end items-center"
               v-if="route.travel_time">
            <span class="text-md">{{ minutesToHumanReadable(route.travel_time) }}</span>
          </div>
        </div>

        <template v-for="(point, index) in route.points" :key="point.id">
          <div class="w-full flex flex-col justify-end items-center px-2" v-if="!arePointsHidden">
            <div class="w-full flex flex-row justify-start items-center pl-2.5">
              <div class="w-full flex flex-col justify-center items-start border-l-2 border-opacity-20 border-gray-500 pl-5 py-2"
                   v-if="index > 0">
                <div class="flex flex-row justify-start items-center"
                     v-if="point.travel_time">
                  <span>{{ minutesToHumanReadable(point.travel_time) }}</span>
                </div>
              </div>
            </div>

            <div class="w-full flex flex-col justify-start items-center">
              <div class="w-full flex flex-row justify-start items-center gap-2 rounded">
                <div class="rounded flex justify-center items-center">
                  <MapPinHouse class="w-6 h-6" v-if="index === 0 || index === route.points.length - 1"/>
                  <MapPin class="w-6 h-6" v-else/>
                </div>

                <div class="flex justify-start items-baseline"
                     v-if="point.city">
                <span>
                  <span class="text-lg font-semibold">{{ point.city }}</span><br>
                  <span class="text-md">{{ getUnicodeFlagIcon(point.country === 'Ukraine' ? 'UA' : point.country === 'Slovakia' ? 'SK' : point.country) }} {{ point.country }} </span>
                </span>
                </div>

                <div v-else>
                  <span class="text-lg">{{ point.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>

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

                <div class="w-full flex flex-row justify-start items-center cursor-pointer"
                     @click="emits('trip-clicked', trip)">

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
