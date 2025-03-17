<script setup lang="ts">
  import { ChevronRight } from 'lucide-vue-next';
  import { useToast } from 'vue-toastification';

  const toast = useToast();
</script>

<template>
  <div class="w-full flex flex-col justify-center items-center bg-base-200/80 px-4 py-2">
    <div class="w-full max-w-lg flex flex-col justify-center items-center">
      <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 gap-4 font-mono">
        <div class="w-full flex flex-col justify-start items-start gap-2">
          <h3 class="text-2xl font-semibold">Contact Me</h3>
          <p class="text-md opacity-80">
            Fill out your details for our manager to contact you regarding pickup, destination, date, time, and seats
          </p>
        </div>

        <button class="btn btn-lg btn-outline flex flex-row justify-center items-center gap-1 px-3 border-base-content/50"
                @click="toast.info('Not implemented yet', {position: 'bottom-center', timeout: 2000})">
          <ChevronRight class="w-6 h-6 mb-0.5 opacity-0"/>
          <span class="w-full pt-0.5">Contact Me</span>
          <ChevronRight class="w-6 h-6 mb-0.5"/>
        </button>
      </div>
    </div>
  </div>
</template>
