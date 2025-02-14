<script setup lang="ts">
  import { PropType } from 'vue';
  import { AvailableSignInRole, Company } from '../../api';

  defineProps({
    title: String as PropType<string> | null,
    description: String as PropType<string> | null,
  });
</script>

<template>
  <div class="w-full flex flex-col justify-start items-start">
    <h5 class="text-md" v-if="title">{{ title }}</h5>
    <p class="text-xs text-gray-500" v-if="description">{{ description }}</p>
  </div>
</template>
