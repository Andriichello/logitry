<script setup lang="ts">
  import { Building2 } from 'lucide-vue-next';
  import { PropType } from 'vue';
  import { Company } from '@/api';

  const props = defineProps({
    company: {
      type: Object as PropType<Company> | null,
      required: true,
    },
  });
</script>

<template>
  <div class="w-full flex flex-row justify-start items-center gap-2 p-2 pb-0">
    <div class="p-3 rounded bg-gray-200">
      <Building2 class="w-6 h-6" color="black"/>
    </div>

    <div class="w-full flex flex-col justify-start items-start">
      <h3 class="text-xl font-bold">{{ props.company?.name ?? 'Company' }}</h3>
      <span class="text-sm">{{ props.company?.abbreviation ?? 'Abbreviation' }}</span>
    </div>
  </div>
</template>
