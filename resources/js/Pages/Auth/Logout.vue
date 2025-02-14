<script setup lang="ts">
  import { ref, onMounted, PropType } from 'vue';
  import { Me } from '../../api';
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
  <div class="w-full min-h-screen flex flex-col justify-start items-center p-10">
    <div class="w-full flex flex-row justify-center items-center p-10">
      <input type="checkbox" value="light" class="toggle theme-controller" checked/>
      <h3 class="text-4xl font-bold p-4">Logitry</h3>
    </div>

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
