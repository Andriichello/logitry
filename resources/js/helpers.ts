export function minutesToHumanReadable(minutes: number | string) {
  const days = Math.floor(minutes / 1440);
  const hours = Math.floor((minutes % 1440) / 60);
  const mins = minutes % 60;

  const daysText = days > 0 ? `${days}d` : '';
  const hoursText = hours > 0 ? `${hours}h` : '';
  const minutesText = mins > 0 ? `${mins}m` : '';

  return [daysText, hoursText, minutesText].filter(Boolean).join(' ');
}

export function toHumanDateTime(date: Date | string | number) {
  return new Date(date)
    .toLocaleString('en-US', {
      day: '2-digit',    // Day of the month (e.g., "18")
      month: 'short',    // Short month name (e.g., "Feb")
      weekday: 'short',  // Optional: "Mon"
      hour: '2-digit',   // 24-hour format hour
      minute: '2-digit', // Minute
      hour12: false      // 24-hour format
    })
}

export function toHumanDate(date: Date | string | number) {
  return new Date(date)
    .toLocaleString('en-US', {
      day: '2-digit',    // Day of the month (e.g., "18")
      month: 'short',    // Short month name (e.g., "Feb")
    })
}

export function toHumanWeekday(date: Date | string | number) {
  return new Date(date)
    .toLocaleString('en-US', {
      weekday: 'short',  // Optional: "Mon"
    })
}

export function toHumanTime(date: Date | string | number) {
  return new Date(date)
    .toLocaleString('en-US', {
      hour: '2-digit',   // 24-hour format hour
      minute: '2-digit', // Minute
      hour12: false,     // 24-hour format
    });
}

export function isSameDate(one: Date | string | number, two: Date | string | number) {
  return toHumanDate(one) === toHumanDate(two);
}


export function numberAsIntOrFloat(value: number, digits: number | null = null): number {
  const intVal = Number.parseInt(value.toString());
  let floatVal = Number.parseFloat(value.toString());

  if (intVal === floatVal) {
    return intVal;
  }

  if (digits !== null && digits >= 0) {
    floatVal = Math.trunc(floatVal * 10 ** digits) / 10 ** digits;
  }

  return floatVal;
}
