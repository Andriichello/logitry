<script setup lang="ts">
  import { PropType } from 'vue';
  import { Me } from '../api';

  defineProps({
    me: Object as PropType<Me> | null,
  });
</script>

<template>
  <div>
    <h1>Hello from Home...</h1>
    <p class="badge badge-primary">{{ me?.name ?? 'Guest' }}</p>
  </div>
</template>
