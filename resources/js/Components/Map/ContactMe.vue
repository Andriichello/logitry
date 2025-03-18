<script setup lang="ts">
  import { ref } from 'vue';
  import { ChevronRight } from 'lucide-vue-next';
  import { useToast } from 'vue-toastification';

  const props = defineProps({
    collapsed: {
      type: Boolean,
      default: true,
    }
  });
  const toast = useToast();

  const isOpen = ref(!props.collapsed);
</script>

<template>

  <div class="w-full flex flex-col justify-center items-center bg-base-200/80 px-4 py-2 pt-0"
       :class="{'pb-1': !isOpen}">
    <div class="w-full max-w-lg flex flex-col justify-center items-center">
      <div class="w-full flex flex-col justify-between items-between rounded-right rounded-xl py-3 gap-4 font-mono">
        <div class="w-full flex flex-col justify-start items-start">
          <div class="w-full flex flex-row justify-between items-start gap-2">
            <h3 class="w-full text-2xl font-semibold pt-1"
                :class="{'cursor-pointer': !isOpen}"
                @click="!isOpen && (isOpen = !isOpen)">
              Contact Me
            </h3>

            <button class="btn btn-sm h-fit py-1 text-[14px] btn-outline border-base-content/60 opacity-80 hover:opacity-100 font-semibold"
                    @click="isOpen = !isOpen">
              {{ isOpen ? 'Hide' : 'Show' }}
            </button>
          </div>

          <p class="w-full text-md opacity-80 pt-2" v-if="isOpen">
            Fill out your details for our manager to contact you regarding pickup, destination, date, time, and seats
          </p>

          <p class="w-full text-md opacity-80 pt-2 cursor-pointer" v-else
             @click="isOpen = !isOpen">
            Our manager will contact you...
          </p>
        </div>

        <button class="btn btn-lg btn-outline flex flex-row justify-center items-center gap-1 px-3 border-base-content/50"
                @click="toast.info('Not implemented yet', {position: 'bottom-center', timeout: 2000})"
                v-if="isOpen" >
          <ChevronRight class="w-6 h-6 mb-0.5 opacity-0"/>
          <span class="w-full pt-0.5">Contact Me</span>
          <ChevronRight class="w-6 h-6 mb-0.5"/>
        </button>
      </div>
    </div>
  </div>
</template>
