<script setup lang="ts">
  import 'leaflet/dist/leaflet.css';
  import { PropType } from 'vue';
  import { Company } from '@/api';
  import { useThemeStore } from '@/stores/theme';
  import { Truck, Moon, Sun } from 'lucide-vue-next';

  const props = defineProps({
    company: Object as PropType<Company> | null,
  });

  const themeStore = useThemeStore();
</script>

<template>
  <div id="app" class="min-h-screen" :class="themeStore.isDark ? 'bg-gray-900' : 'bg-gray-50'">
    <!-- Header -->
    <header class="shadow-sm border-b" :class="themeStore.isDark ? 'bg-gray-900 border-gray-700' : 'bg-white border-gray-200'">
      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-purple-600 rounded flex items-center justify-center">
            <Truck class="w-5 h-5 text-white" />
          </div>
          <div>
            <h1 class="font-semibold" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">{{ props.company?.name || 'Haul Auto' }}</h1>
            <p class="text-xs text-gray-500">{{ props.company?.abbreviation || 'haul-auto' }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <!-- Theme toggle button -->
          <button
            @click="themeStore.toggle"
            class="p-2 rounded flex items-center gap-2 text-sm"
            :class="themeStore.isDark ? 'hover:bg-gray-800 text-gray-300' : 'hover:bg-gray-100 text-gray-700'"
          >
            <Moon v-if="!themeStore.isDark" class="w-4 h-4" />
            <Sun v-else class="w-4 h-4" />
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1">
      <slot/>
    </div>
  </div>
</template>
