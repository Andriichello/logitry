<script setup lang="ts">
  import { PropType } from 'vue';
  import { Me } from '../api';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    me: Object as PropType<Me> | null,
  });

  console.log(props);
</script>

<template>
  <div>
    <input type="checkbox" value="light" class="toggle theme-controller" checked hidden/>

    <div class="py-10 w-full flex flex-col justify-center items-center gap-3">
      <h1>Hello <span class="font-semibold">{{ props.me?.name ?? 'Guest' }}</span>, you are logged in</h1>

      <button class="btn btn-sm btn-primary" @click="router.visit('/logout')">
        Log Out
      </button>
    </div>
  </div>
</template>
