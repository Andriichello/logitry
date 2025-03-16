<script lang="ts">
  import LandingLayout from '@/Layouts/LandingLayout.vue';

  export default {
    layout: LandingLayout,
  };
</script>

<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import { inject, onMounted, PropType, watch } from 'vue';
  import { MapFilters, MapSelections, useMapStore } from '@/stores/map';
  import { Company, Trip } from '@/api';
  import { Route } from '@/api';
  import RouteOnMap from '@/Components/Map/RouteOnMap.vue';
  import { POSITION, useToast } from 'vue-toastification';

  const props = defineProps({
    company: Object as PropType<Company> | null,
  });
</script>

<template>
  <div class="w-full h-full min-w-[200px] min-h-[200px]">

  </div>
</template>
