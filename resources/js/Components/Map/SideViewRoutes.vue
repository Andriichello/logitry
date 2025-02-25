<script setup lang="ts">
  import { PropType } from 'vue';
  import { Route } from '@/api';
  import { Route as RouteIcon, X } from 'lucide-vue-next';
  import { minutesToHumanReadable } from '@/helpers';

  const emits = defineEmits(['route-clicked']);

  const props = defineProps({
    routes: {
      type: Array as PropType<Route[]> | null,
      required: true,
    },
  });
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center p-3 overflow-y-auto pb-10">

    <div class="w-full flex flex-row justify-between items-baseline gap-2">
      <h3 class="text-md font-semibold">
        Routes
      </h3>

      <div class="rounded flex justify-center items-center cursor-pointer p-2 opacity-0">
        <X class="w-5 h-5"/>
      </div>
    </div>

    <div class="w-full grow flex flex-col justify-start items-start overflow-y-auto pb-10"
         v-if="props.routes?.length">

      <template v-for="(route, index) in props.routes" :key="route.id">
        <div class="w-full flex flex-col justify-start items-center">
          <div class="w-full border-t-2 opacity-15" v-if="index > 0"/>

          <div class="w-full flex flex-col justify-start items-center cursor-pointer p-2 pt-0"
               @click="emits('route-clicked', route)">

            <div class="w-full flex flex-row justify-start items-center gap-2 rounded pt-2">
              <span class="text-xl font-semibold">{{ route.name }}</span>
            </div>

            <div class="w-full flex flex-row justify-start items-baseline self-start gap-4">
              <div class="flex flex-row justify-start items-center"
                   v-if="route.points?.length">
                <span class="text-md">{{ route.points?.length }} stops</span>
              </div>

              <div class="flex flex-row justify-center items-center"
                   v-if="route.travel_time">
                <span class="text-md">{{ minutesToHumanReadable(route.travel_time) }}</span>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <div class="flex flex-col justify-center items-center gap-2 p-4"
         v-else>
      <RouteIcon class="w-8 h-8"/>

      <span class="text-lg font-bold">No routes found</span>
    </div>
  </div>
</template>
