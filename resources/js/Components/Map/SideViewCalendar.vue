<script setup lang="ts">
  import {ArrowLeft} from 'lucide-vue-next';
  import {useMapStore} from '@/stores/map';
  import {useThemeStore} from '@/stores/theme';
  import {useToast} from 'vue-toastification';
  import BookingCalendar from "@/Components/Date/BookingCalendar.vue";

  const emits = defineEmits(['close-calendar', 'apply-calendar']);

  const props = defineProps({
    beg: {
      type: Object as PropType<dayjs.Dayjs | null>,
      required: false,
      default: null,
    },
    end: {
      type: Object as PropType<dayjs.Dayjs | null>,
      required: false,
      default: null,
    },
    months: {
      type: Number as PropType<number>,
      required: false,
      default: 2,
    },
    dottedDates: {
      type: Array,
      required: false,
      default: [],
    },
  });

  const toast = useToast();

  const mapStore = useMapStore();
  const themeStore = useThemeStore();

  const applyCalendar = ({beg, end}) => {
    emits('apply-calendar', {beg, end});
  }
</script>

<template>
  <div class="flex flex-col h-full">
    <!-- Back Button -->
    <div class="p-4 border-b" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'">
      <button
        @click="emits('close-calendar')"
        class="flex items-center gap-2"
        :class="themeStore.isDark ? 'text-gray-200 hover:text-white' : 'text-gray-600 hover:text-gray-900'"
      >
        <ArrowLeft class="w-4 h-4" />
        When?
      </button>
    </div>

    <div class="flex-1 overflow-y-auto overflow-x-hidden pt-2 px-2">
      <BookingCalendar :beg="beg"
                       :end="end"
                       :months="months"
                       :dotted-dates="dottedDates"
                       @apply-calendar="applyCalendar"
                       @close-calendar="emits('close-calendar')"/>
    </div>
  </div>
</template>
