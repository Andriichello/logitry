<script lang="ts">
  import AuthLayout from "@/Layouts/AuthLayout.vue";

  export default {
    layout: AuthLayout,
  };
</script>

<script setup lang="ts">
  import { PropType } from 'vue';
  import { Me } from '@/api';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    me: Object as PropType<Me> | null,
  });
</script>

<template>
  <div class="w-full h-full flex flex-col justify-center items-center">
    <input type="checkbox" value="light" class="toggle theme-controller" checked hidden/>

    <div class=" w-full flex flex-col justify-center items-center gap-3">
      <h1>Hello <span class="font-semibold">{{ props.me?.name ?? 'Guest' }}</span>, you are {{ props.me ? '' : 'not' }} logged in</h1>

      <button class="btn btn-sm btn-primary" v-if="props.me"
              @click="router.visit('/logout')">
        Log Out
      </button>

      <button class="btn btn-sm btn-primary" v-else
              @click="router.visit('/login')">
        Log In
      </button>
    </div>
  </div>
</template>
