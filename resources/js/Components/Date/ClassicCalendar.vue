SideViewRoute.vue
<script setup lang="ts">
  import { ref, computed, onMounted, PropType } from 'vue';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import isoWeek from 'dayjs/plugin/isoWeek';
  import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

  dayjs.extend(isBetween);
  dayjs.extend(isoWeek);

  const props = defineProps({
    mode: {
      type: String as PropType<'single' | 'range'>,
      default: 'single',
    },
    selectedDates: {
      type: Array as PropType<string[]>,
      required: false,
      default: [],
    },
    disabledDates: {
      type: Array as PropType<string[]>,
      required: false,
      default: [],
    },
    dottedDates: {
      type: Array as PropType<string[]>,
      required: false,
      default: [],
    },
  });

  const begDate = ref(dayjs().startOf('day'));
  const endDate = ref(null as dayjs.Dayjs);

  const selectedDates = ref<[string, string] | null>([...props.selectedDates]);
  const currentMonth = ref(dayjs().startOf('month'));

  // Get all days for the current month including leading/trailing days
  const daysInMonth = computed(() => {
    const start = currentMonth.value.startOf('isoWeek');
    const end = currentMonth.value.endOf('month').endOf('isoWeek');
    const days = [];

    let day = start;

    while (day.isBefore(end)) {
      days.push(day);
      day = day.add(1, 'day');
    }

    return days;
  });

  const isSelected = (date: dayjs.Dayjs) =>
    selectedDates.value?.includes(date.format('YYYY-MM-DD'));

  const isDisabled = (date: dayjs.Dayjs) =>
    props.disabledDates?.includes(date.format('YYYY-MM-DD'));

  const isDotted = (date: dayjs.Dayjs) =>
    props.dottedDates?.includes(date.format('YYYY-MM-DD'));


  // Select date or date range
  function selectDate(date: dayjs.Dayjs) {
    if (isDisabled(date)) {
      return;
    }

    if (!selectedDates.value) {
      selectedDates.value = [date.format('YYYY-MM-DD'), date.format('YYYY-MM-DD')];
    } else {
      selectedDates.value[1] = date.format('YYYY-MM-DD');
      if (dayjs(selectedDates.value[0]).isAfter(selectedDates.value[1])) {
        selectedDates.value.reverse();
      }
    }
  }


  const view = ref('dates');

  const today = ref(dayjs());
  const currentDate = ref(dayjs());
  const selectedYear = ref(currentDate.value.year());
  const selectedMonth = ref(currentDate.value.month());

  const years = ref([]);
  const months = ref([
    'January', 'February',
    'March', 'April', 'May',
    'June', 'July', 'August',
    'September', 'October', 'November',
    'December',
  ]);

  function generateYears(startYear = null) {
    if (startYear) {
      const begYear = startYear;
      const endYear = begYear + 11

      years.value = Array.from({ length: endYear - begYear + 1 }, (_, i) => begYear + i);
    } else {
      const currentYear = selectedYear.value ?? currentDate.value.year();

      const begYear = currentYear - 4;
      const endYear = currentYear + 7;

      years.value = Array.from({ length: endYear - begYear + 1 }, (_, i) => begYear + i);

    }
  }

  function selectMonth(monthIndex) {
    selectedMonth.value = monthIndex;
    currentMonth.value = currentMonth.value.set('month', monthIndex);

    view.value = 'dates';
  }

  function changeView(newView) {
    if (view.value === newView) {
      view.value = 'dates';

      return;
    }

    if (newView === 'years') {
      generateYears();
    }

    view.value = newView;
  }

  function next() {
    if (view.value === 'months') {
      return;
    }

    if (view.value === 'years') {
      generateYears(years.value[years.value.length - 1] + 1);
    }

    if (view.value === 'dates') {
      nextMonth();
    }
  }

  function prev() {
    if (view.value === 'months') {
      return;
    }

    if (view.value === 'years') {
      generateYears(years.value[0] - 12);
    }


    if (view.value === 'dates') {
      prevMonth();
    }
  }

  function prevYear() {
    if (years.value.length === 0) {
      generateYears();
    } else {
      //
    }
  }

  function nextYear() {
    if (years.value.length === 0) {
      generateYears();
    } else {
      //
    }
  }

  function prevMonth() {
    currentMonth.value = currentMonth.value.subtract(1, 'month');
    selectedMonth.value = currentMonth.value.month();

    view.value = 'dates';
  }

  function nextMonth() {
    currentMonth.value = currentMonth.value.add(1, 'month');
    selectedMonth.value = currentMonth.value.month();

    view.value = 'dates';
  }

  function selectYear(year) {
    selectedYear.value = year;
    currentMonth.value = currentMonth.value.set('year', year);

    view.value = 'dates';
  }

  const hoveredDate = ref(null);

  // Update hovered date
  function hoverDate(date: dayjs.Dayjs) {
    if (isDisabled(date)) return;
    hoveredDate.value = date.format('YYYY-MM-DD');
  }

  // Clear hovered date
  function clearHover() {
    hoveredDate.value = null;
  }

  onMounted(() => {
    generateYears();
  });
</script>

<template>
  <div class="w-full ]] rounded-lg">
    <div class="flex justify-between items-center gap-2">
      <div class="btn btn-ghost btn-md"
           :class="[
             view === 'months' ? 'opacity-0 cursor-default' : '',
           ]"
           @click="prev">
        <ChevronLeft />
      </div>

      <div class="flex gap-2">
        <div class="btn btn-ghost btn-md"
             @click="changeView('months')">
          <h5 class="text-xl font-bold cursor-pointer">
            {{ months[selectedMonth] }}
          </h5>
        </div>

        <div class="btn btn-ghost btn-md"
             @click="changeView('years')">
          <h5 class="text-xl font-bold cursor-pointer">
            {{ selectedYear }}
          </h5>
        </div>
      </div>

      <div class="btn btn-ghost btn-md"
           :class="[
             view === 'months' ? 'opacity-0 cursor-default' : '',
           ]"
           @click="next">
        <ChevronRight />
      </div>
    </div>

    <div class="my-2 border-t-1 opacity-25"/>

    <template v-if="view === 'years'">
      <!-- Years Grid -->
      <div class="grid grid-cols-3 gap-2 text-center">
        <!-- Years in Grid -->
        <div v-for="year in years" :key="year"
                class="btn btn-md font-medium"
                :class="[
                  selectedYear === year
                  ? 'btn-primary' : 'btn-ghost text-gray-500',
                ]"
                @click="selectYear(year)">
          <span class="text-lg">{{ year }}</span>
        </div>
      </div>
    </template>

    <template v-else-if="view === 'months'">
      <!-- Months Grid -->
      <div class="grid grid-cols-3 gap-2 text-center">
        <!-- Months in Grid -->
        <div v-for="(month, index) in months" :key="index"
             class="btn btn-md font-medium"
             :class="[
                  selectedMonth === index
                  ? 'btn-primary' : 'btn-ghost text-gray-500',
                ]"
             @click="selectMonth(index)">
          <span class="text-lg">{{ month.substring(0, 3) }}</span>
        </div>
      </div>
    </template>

    <template v-else-if="view === 'dates'">
      <!-- Calendar Grid -->
      <div class="grid grid-cols-7 text-center gap-1">
        <!-- Day Labels -->
        <span class="font-bold text-lg" v-for="day in ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']" :key="day">{{ day }}</span>

        <!-- Days in Month -->
        <div v-for="day in daysInMonth"
                :key="day.format('YYYY-MM-DD')"
                class="flex flex-row btn btn-md btn-ghost"
                :class="[
                  day.month() !== currentMonth.month() ? 'text-gray-400 cursor-default' : '',
                  today.isSame(day, 'day') ? 'bg-neutral-200 text-neutral-700' : '',
                ]"
                @click="selectDate(day)"
                @mouseenter="hoverDate(day)"
                @mouseleave="clearHover">
          <span class="indicator-item status status-success opacity-0"/>
          <span class="min-w-5 text-end text-lg">{{ day.date() }}</span>
          <span class="indicator-item status status-success"
                :class="{'opacity-0': !isDotted(day)}"/>
        </div>
      </div>
    </template>
  </div>
</template>


