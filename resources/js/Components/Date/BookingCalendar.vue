<script setup lang="ts">
  import { ref, computed, PropType, onMounted } from 'vue';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { ArrowLeft } from 'lucide-vue-next';
  import { useThemeStore } from '@/stores/theme';

  dayjs.extend(isBetween);

  const themeStore = useThemeStore();

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

  const fromDate = ref(props.beg as dayjs.Dayjs | null);
  const toDate = ref(props.end as dayjs.Dayjs | null);
  const hoverDate = ref(null as dayjs.Dayjs | null);

  const isDotted = (date: dayjs.Dayjs) =>
    !!props.dottedDates?.find(dottedDate => dottedDate.date === date.format('YYYY-MM-DD'));

  const dottedTooltip = (date: dayjs.Dayjs) => {
    const dottedDate = props.dottedDates?.find(dottedDate => {
      return dottedDate.date === date.format('YYYY-MM-DD');
    });

    return dottedDate ? dottedDate.count + (dottedDate.count > 1 ? ' trips' : ' trip')  : '';
  }

  const startDate = ref(dayjs().startOf('month'));
  const endDate = ref(dayjs().add(props.months, 'month').endOf('month'));

  // Generate all dates for the next year
  const calendar = computed(() => {
    const dates = [];
    let current = startDate.value;

    while (current.isBefore(endDate.value)) {
      const startOfMonth = current.startOf('month');
      const endOfMonth = current.endOf('month');
      const days = [];

      // Start from Monday
      let day = startOfMonth.startOf('week').add(1, 'day');
      if (day.day() === 0) {
        day = day.subtract(6, 'days');
      }

      while (day.isBefore(endOfMonth.endOf('week').add(1, 'day'))) {
        days.push(day);
        day = day.add(1, 'day');
      }

      dates.push({
        month: startOfMonth.format('MMMM YYYY'),
        days,
      });

      current = current.add(1, 'month');
    }

    return dates;
  });

  function dateClick(date: dayjs.Dayjs) {
    if (toDate.value) {
      fromDate.value = date;
      toDate.value = null;

      return;
    }

    if (fromDate.value) {
      if (fromDate.value.isSame(date, 'day')) {
        toDate.value = date.endOf('day');
      } else if (fromDate.value?.isAfter(date)) {
        fromDate.value = date;
      } else {
        toDate.value = date.endOf('day');
      }

      return;
    }

    fromDate.value = date;
  }

  function applyCalendar() {
    if (fromDate.value) {
      emits('apply-calendar', { beg: fromDate.value, end: toDate.value });
    }
  }
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center">
    <!-- Calendar - Scrollable Content -->
    <div class="w-full flex-1 flex flex-col gap-8 overflow-y-auto overflow-x-hidden">
      <!-- Day Labels -->
      <div class="w-full sticky top-0 z-10">
        <div class="w-full grid grid-cols-7 gap-1 px-2 pt-4 text-center text-sm text-gray-400"
             :class="themeStore.isDark ? 'bg-gray-900' : 'bg-white'">
        <span v-for="day in ['mon', 'tue', 'wen', 'thu', 'fri', 'sat', 'sun']" :key="day">
          {{ day }}
        </span>
        </div>

        <div class="w-full border-b" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'"/>
      </div>

      <div v-for="month in calendar" :key="month.month">
        <!-- Month Header -->
        <h3 class="w-full text-start text-lg font-bold px-4" :class="themeStore.isDark ? 'text-gray-100' : 'text-gray-900'">
          {{ month.month }}
        </h3>

        <!-- Days in Month -->
        <div class="grid grid-cols-7 px-2">
          <div v-for="day in month.days" :key="day.format('YYYY-MM-DD')"
               class="flex justify-center items-center h-10"
               :class="[
                  ...(
                    day.isBefore(dayjs().startOf('day').subtract(1, 'day'))
                      ? ['text-gray-400']
                      : [
                        themeStore.isDark
                          ? 'hover:bg-gray-800 '
                          : 'hover:bg-gray-100 text-gray-700',
                        day.format('MMMM YYYY') === month.month ? 'cursor-pointer' : '',
                        day.isSame(dayjs(), 'day') ? 'font-bold' : '',
                      ]
                  ),
                  fromDate && day.isSame(fromDate, 'day') ? 'bg-purple-600 hover:bg-purple-700 text-white rounded-l-lg' : '',
                  fromDate && toDate && day.isAfter(fromDate) && day.isBefore(toDate)
                    ? (themeStore.isDark ? 'bg-gray-800' : 'bg-gray-100 text-gray-800') : '',
                  fromDate && !toDate && hoverDate && day.isAfter(fromDate) && day.isBefore(hoverDate)
                    ? (themeStore.isDark ? 'bg-gray-800' : 'bg-gray-100') : '',
                  toDate && day.isSame(toDate, 'day') ? 'bg-purple-600 hover:bg-purple-700 text-white rounded-r-lg' : '',
                  day.format('MMMM YYYY') !== month.month ? 'opacity-0 cursor-default' : '',
               ]"
               @click="(!day.isBefore(dayjs().startOf('day')) || day.isSame(dayjs().subtract(1,'day').startOf('day'))) && day.format('MMMM YYYY') === month.month ? dateClick(day) : null"
               @mouseenter="!day.isBefore(dayjs().startOf('day')) && day.format('MMMM YYYY') === month.month ? hoverDate = day : null"
               @mouseleave="!day.isBefore(dayjs().startOf('day')) && day.format('MMMM YYYY') === month.month ? hoverDate = null : null">

            <template v-if="isDotted(day)">
              <div class="w-full h-full tooltip tooltip-bottom tooltip-success flex flex-row justify-center items-center gap-0.5">
                <div class="tooltip-content -translate-y-3">
                  <div class="text-md font-semibold font-black">{{ dottedTooltip(day) }}</div>
                </div>

                <span class="indicator-item status status-success opacity-0"/>
                <span class="text-md">{{ day.date() }}</span>
                <span class="indicator-item status status-success"
                      :class="[
                      isDotted(day) ? '' : 'opacity-0',
                    ]"/>
              </div>
            </template>

            <template v-else>
              <div class="flex flex-row justify-center items-center gap-0.5">
                <span class="indicator-item status status-success opacity-0"/>
                <span class="text-md">{{ day.date() }}</span>
                <span class="indicator-item status status-success opacity-0"/>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <div class="w-full flex flex-col justify-center items-center">
      <div class="w-full">
        <div class="w-full border-b" :class="themeStore.isDark ? 'border-gray-700' : 'border-gray-200'"/>
      </div>

      <template v-if="fromDate && toDate">
        <template v-if="!fromDate.isSame(toDate, 'day')">
          <div class="w-full flex justify-center items-center pt-3 pb-3 gap-2">
            <span class="flex-1 text-sm font-semibold text-end" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
              {{ fromDate.format('ddd, DD MMM') }}
            </span>
            <span class="text-sm font-medium" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'">-</span>
            <span class="flex-1 text-sm font-semibold text-start" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
              {{ toDate.format('ddd, DD MMM') }}
            </span>
          </div>
        </template>

        <template v-else>
          <div class="w-full flex justify-center items-center pt-3 pb-3 gap-2">
            <span class="flex-1 text-sm font-semibold text-center" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
              {{ fromDate.format('ddd, DD MMM') }}
            </span>
          </div>
        </template>
      </template>

      <template v-else-if="fromDate">
        <div class="w-full flex justify-center items-center pt-3 pb-3 gap-2">
          <span class="flex-1 text-sm font-semibold text-end" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
            {{ fromDate?.format('ddd, DD MMM') ?? '' }}
          </span>
          <span class="text-sm font-medium" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'">-</span>
          <span class="flex-1 text-sm font-semibold text-start" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
            {{ toDate?.format('ddd, DD MMM') ?? 'Future' }}
          </span>
        </div>
      </template>

      <template v-else>
        <div class="w-full flex justify-center items-center pt-3 pb-3 gap-2">
          <span class="flex-1 text-sm font-semibold text-end" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
            Start date
          </span>
          <span class="text-sm font-medium" :class="themeStore.isDark ? 'text-gray-400' : 'text-gray-500'">-</span>
          <span class="flex-1 text-sm font-semibold text-start" :class="themeStore.isDark ? 'text-gray-200' : 'text-gray-700'">
            End date
          </span>
        </div>
      </template>

      <div class="w-full flex justify-between items-center gap-2 p-4 px-2 pt-0 pb-2">
        <button
          class="w-full py-3 px-4 rounded-lg text-white font-medium transition-colors cursor-pointer"
          :class="[
            fromDate === null
              ? (themeStore.isDark ? 'bg-gray-700 cursor-not-allowed' : 'bg-gray-300 cursor-not-allowed')
              : 'bg-purple-600 hover:bg-purple-700'
          ]"
          :disabled="fromDate === null"
          @click="applyCalendar">
          Apply
        </button>
      </div>

    </div>
  </div>
</template>
