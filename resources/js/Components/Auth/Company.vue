<script setup lang="ts">
  import { PropType } from 'vue';
  import { AvailableSignInRole, Company } from '@/api';

  defineProps({
    company: Object as PropType<Company>,
    role: String as PropType<AvailableSignInRole> | null,
    withPassword: Boolean,
  });


  defineEmits({
    select(payload: { company: Company }) {
      return true;
    }
  });
</script>

<template>
  <div class="w-full flex justify-between items-center border border-gray-500 bg-base-100 rounded p-2">
    <div class="w-full flex flex-col justify-start items-start">
      <h5 class="text-md">{{ company.name }}</h5>
      <p class="text-xs text-gray-500">{{ company.abbreviation }}</p>
    </div>

    <button class="btn btn-sm btn-primary w-fit" v-if="withPassword"
      @click="$emit('select', { company })">
      Select
    </button>
  </div>
</template>
