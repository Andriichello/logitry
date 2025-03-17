<script setup lang="ts">
  import { Building2, SearchX } from 'lucide-vue-next';
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
  <div class="w-full flex flex-row justify-start items-center gap-3 p-2">
    <div class="btn btn-square btn-lg btn-primary opacity-80 hover:opacity-100">
      <Building2 class="w-6 h-6"/>
    </div>

    <div class="w-full flex flex-col justify-start items-start">
      <h3 class="text-xl font-bold">{{ props.company ? (props.company.name ?? 'Company') : 'Not found' }}</h3>
      <span class="text-sm">{{ props.company ? (props.company.abbreviation ?? 'Abbreviation') : 'no such company' }}</span>
    </div>
  </div>
</template>
