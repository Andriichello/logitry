<script lang="ts">
  import AuthLayout from "@/Layouts/AuthLayout.vue";

  export default {
    layout: AuthLayout,
  };
</script>

<script setup lang="ts">
  import { ref, onMounted, PropType } from 'vue';
  import { Me } from '@/api';
  import { router, useForm } from '@inertiajs/vue3';

  const props = defineProps({
    me: Object as PropType<Me> | null,
  });

  const isLoggingOut = ref(null as boolean | null);

  onMounted(() => {
    if (props.me) {
      isLoggingOut.value = true;

      useForm().delete('/logout', {
        onSuccess: () => {
          isLoggingOut.value = false;
        },
        onError: () => {
          isLoggingOut.value = false;
        },
      });
    }
  });
</script>

<template>
  <div class="w-full flex flex-col justify-start items-center">
    <div class="w-full flex flex-col justify-center items-center gap-2" v-if="isLoggingOut">
      <h3>
        Logging Out
      </h3>

      <span class="loading loading-lg loading-spinner"/>
    </div>

    <div class="w-full flex flex-col justify-center items-center gap-2" v-else-if="!props.me">
      <h3>You are already logged out.</h3>
      <button class="btn btn-sm btn-primary" @click="router.visit('/login')">
        Log In
      </button>
    </div>
  </div>
</template>
