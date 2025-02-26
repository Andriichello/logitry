<script setup lang="ts">
  import { ref, computed, PropType, onMounted } from 'vue';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { X } from 'lucide-vue-next';

  dayjs.extend(isBetween);

  const emits = defineEmits(['close-calendar']);

  const props = defineProps({
    dottedDates: {
      type: Array as PropType<string[]>,
      required: false,
      default: [],
    },
  });

  const isDotted = (date: dayjs.Dayjs) =>
    props.dottedDates?.includes(date.format('YYYY-MM-DD'));

  const startDate = ref(dayjs().startOf('month'));
  const endDate = ref(dayjs().add(2, 'month').endOf('month'));

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
</script>

<template>
  <div class="w-full h-full flex flex-col justify-start items-center overflow-y-auto">
    <!-- Close Button -->
    <div class="w-full flex justify-between items-center ">
      <h3 class="text-xl font-semibold">When?</h3>

      <div class="rounded-full p-2 cursor-pointer"
           @click="emits('close-calendar')">
        <X class="w-6 h-6"/>
      </div>
    </div>

    <!-- Day Labels -->
    <div class="w-full grid grid-cols-7 gap-1 text-center text-sm text-gray-400 px-2 pt-4">
      <span v-for="day in ['mon', 'tue', 'wen', 'thu', 'fri', 'sat', 'sun']" :key="day">
        {{ day }}
      </span>
    </div>

    <div class="border-b-1 w-full opacity-25"/>


    <!-- Calendar -->
    <div class="w-full flex flex-col gap-8 overflow-y-auto pb-[50%] pt-8">
      <div v-for="month in calendar" :key="month.month">
        <!-- Month Header -->
        <h3 class="w-full text-start text-lg font-bold px-4">
          {{ month.month }}
        </h3>

        <!-- Days in Month -->
        <div class="grid grid-cols-7 gap-1 px-2">
          <div v-for="day in month.days"
               :key="day.format('YYYY-MM-DD')"
               class="flex justify-center items-center h-10 cursor-pointer rounded-full"
               :class="{
                 'text-gray-400': day.isBefore(dayjs().startOf('day')),
                 'font-bold': day.isSame(dayjs(), 'day'),
                 'opacity-0': day.format('MMMM YYYY') !== month.month
               }">

            <div class="flex flex-row justify-center items-center gap-1">
              <span class="indicator-item status status-success opacity-0"/>
              <span class="min-w-5 text-end text-lg">{{ day.date() }}</span>
              <span class="indicator-item status status-success"
                    :class="{'opacity-0': !isDotted(day)}"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
