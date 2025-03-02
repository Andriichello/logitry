<script setup lang="ts">
  import { computed, PropType, ref } from 'vue';
  import { Car, MapPinOff, X } from 'lucide-vue-next';
  import getUnicodeFlagIcon from 'country-flag-icons/unicode';
  import { Deferred } from '@inertiajs/vue3';

  const emits = defineEmits(['close-from', 'apply-from']);

  const props = defineProps({
    title: {
      type: String as PropType<string>,
      required: true,
    },
    from: {
      type: Array as PropType<string[]> | null,
    },
    countries: Object as PropType<Record<string, string>> | null,
  });

  const from = ref(props.from);
  const search = ref('');

  const selected = computed(() => {
    if (!from.value) {
      return [];
    }

    if (!props.countries) {
      return [];
    }

    return from.value
      .map(a2 => {
        const key = a2?.trim().toLowerCase();

        if (!key?.length) {
          return null;
        }

        const country = props.countries?.[key];

        return { a2: key, name: country, flag: getUnicodeFlagIcon(key.toUpperCase()) };
      });
  });

  const filteredCountries = computed(() => {
    if (!props.countries) {
      return {};
    }

    const byKey = Object.keys(props.countries)
      .filter(key => key.toLowerCase().includes(search.value.toLowerCase()));

    const byValue = Object.keys(props.countries)
      .filter(key => props.countries[key].toLowerCase().includes(search.value.toLowerCase()));

    const matches = {};

    byKey.forEach(key => {
      matches[key] = props.countries[key];
    });

    byValue.forEach(key => {
      if (!matches?.[key]) {
        matches[key] = props.countries[key];
      }
    });

    return Object.entries(matches)
      .reduce(
        (acc, [key, value]) => {
          acc[key] = value;
          return acc;
        },
        {} as Record<string, string>,
      );
  });

  function applyFrom() {
    emits('apply-from', from.value);
  }
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center gap-3">
    <!-- Close Button -->
    <div class="w-full flex justify-between items-center">
      <h3 class="text-xl font-semibold pl-2">{{ title }}</h3>

      <div class="rounded-full p-2 cursor-pointer"
           @click="emits('close-from')">
        <X class="w-6 h-6"/>
      </div>
    </div>

    <!-- From -->
    <div class="w-full h-full flex flex-col gap-2 pt-1 px-2">
      <div class="w-full min-h-12 flex flex-row justify-start items-center gap-2 pb-2 overflow-x-auto"
           v-if="selected?.length > 0">

        <template v-for="country in selected" :key="country.a2">
          <div class="flex flex-col justify-start items-center cursor-pointer p-1 border-2 border-primary border-dashed rounded"
               @click="from = from.filter(a2 => a2 !== country.a2)">
            <div class="w-full flex flex-row justify-center items-center gap-1 rounded rounded cursor-pointer">
              <div class="w-8 h-7 aspect-square flex justify-center items-center rounded">
                <span class="text-xl">{{ getUnicodeFlagIcon(country.a2) }}</span>
              </div>
              <span class="w-full text-lg font-medium">{{ country.a2?.toUpperCase() }}</span>
              <div class="flex justify-center items-center rounded">
                <X class="w-6 h-6 text-primary"/>
              </div>
            </div>
          </div>
        </template>
      </div>

      <input class="w-full min-h-12 input input-lg"
             type="text" placeholder="Country name or code..." autofocus
             @input="search = search.replace(/[0-9]*$/g, '')"
             v-model="search"/>

      <div class="w-full flex flex-col"/>

      <Deferred data="countries">
        <template #fallback>
          <div class="flex flex-col justify-center items-center gap-2 p-4 mt-4">
            <Car class="w-8 h-8"/>

            <span class="text-lg font-bold">Loading countries</span>
            <span class="loading loading-dots loading-md"></span>
          </div>
        </template>

        <div class="w-full flex flex-col overflow-y-auto" v-if="search.trim().length > 0">

          <template v-if="Object.keys(filteredCountries ?? {}).length === 0">
            <div class="w-full flex justify-center items-center pt-4">
              <MapPinOff class="w-8 h-8"/>
            </div>

            <div class="w-full flex flex-col justify-start items-center px-2 py-2 text-center font-semibold text-lg">
              No such country found.
            </div>
          </template>

          <template v-else
                    v-for="(a2, index) in Object.keys(filteredCountries ?? {})"
                    :key="a2">
            <div class="w-full flex flex-col justify-start items-center"
                 @click="!from?.includes(a2) ? from?.push(a2) : null; search = '';">
              <div class="w-full border-t-2 opacity-15 2-" v-if="index > 0 && Object.keys(filteredCountries ?? {})[index - 1] !== a2"/>

              <div class="w-full flex flex-col justify-start items-center cursor-pointer p-1"
                :class="[from?.includes(a2) ? 'border-2 border-primary border-dashed' : 'border-2 border-transparent border-dashed']">
                <div class="w-full flex flex-row justify-start items-start gap-2 rounded p-2 rounded cursor-pointer hover:bg-base-300">
                  <div class="w-8 h-7 aspect-square flex justify-center items-center p-1 rounded">
                    <span class="text-xl">{{ getUnicodeFlagIcon(a2.toUpperCase()) }}</span>
                  </div>
                  <span class="text-lg">{{ filteredCountries?.[a2] }}</span>
                </div>
              </div>

            </div>
          </template>
        </div>
      </Deferred>

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
