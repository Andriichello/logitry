<script setup lang="ts">
import { computed, PropType, ref } from 'vue';
import { X, MapPinOff, Keyboard } from 'lucide-vue-next';
import getUnicodeFlagIcon from 'country-flag-icons/unicode';


const emits = defineEmits(['close-from', 'apply-from']);

  const props = defineProps({
    from: {
      type: String as PropType<string> | null,
    },
    countries: Object as PropType<Record<string, string>> | null,
  });

  const from = ref(props.from);
  const search = ref('');

  const countries = computed(() => {
    return Object.entries(props.countries ?? {})
      .filter(
        ([key, value]) =>
          key.toLowerCase().includes(search.value.toLowerCase()) ||
          value.toLowerCase().includes(search.value.toLowerCase())
      )
      .reduce(
        (acc, [key, value]) => {
          acc[key] = value;
          return acc;
        },
        {} as Record<string, string>
      );
  });

  function applyFrom() {
    emits('apply-from', from.value);
  }
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center gap-3">
    <!-- Close Button -->
    <div class="w-full flex justify-between items-center ">
      <h3 class="text-xl font-semibold pl-2">From?</h3>

      <div class="rounded-full p-2 cursor-pointer"
           @click="emits('close-from')">
        <X class="w-6 h-6"/>
      </div>
    </div>

    <!-- From -->
    <div class="w-full h-full flex flex-col gap-2 overflow-y-auto">
      <input class="w-full min-h-12 input input-lg"
             type="text" placeholder="Country..." autofocus
             @input="search = search.replace(/[0-9]*$/g, '')"
             v-model="search"/>

      <div class="w-full flex flex-col overflow-y-auto"
           v-if="search.trim().length === 0">
      </div>

      <div class="w-full flex flex-col overflow-y-auto"
           v-else>
        <template v-if="Object.keys(countries ?? {}).length === 0">
          <div class="w-full flex justify-center items-center pt-4">
            <MapPinOff class="w-8 h-8"/>
          </div>

          <div class="w-full flex flex-col justify-start items-center px-2 py-2 text-center font-semibold text-lg">
            No such country found.
          </div>
        </template>

        <template v-else
                  v-for="(a2, index) in Object.keys(countries ?? {})"
                  :key="a2">
          <div class="w-full flex flex-col justify-start items-center">
            <div class="w-full border-t-2 opacity-15" v-if="index > 0 && Object.keys(countries ?? {})[index - 1] !== a2"/>

            <div class="w-full flex flex-col justify-start items-center cursor-pointer p-2 pt-0">
              <div class="w-full flex flex-row justify-start items-start gap-2 rounded pt-2">
                <div class="w-8 h-7 aspect-square flex justify-center items-center p-1 bg-base-300 rounded">
                  <span>{{ getUnicodeFlagIcon(a2.toUpperCase()) }}</span>
                </div>
                <span class="text-md font-semibold">{{ countries?.[a2] }}</span>
              </div>
            </div>

          </div>
        </template>
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center">

      <div class="w-full flex flex-wrap justify-between items-center gap-2 pt-4">
        <button class="flex-1 btn" @click="emits('close-from')">
          Cancel
        </button>

        <button class="flex-1 btn btn-primary"
                @click="applyFrom">
          Apply
        </button>
      </div>

    </div>
  </div>
</template>
