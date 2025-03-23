<template>
  <div class="w-full flex flex-col justify-start items-start gap-2">
    <h3 class="text-2xl font-semibold">
      <slot name="title"/>
    </h3>

    <p class="text-md opacity-80">
      <slot name="description"/>
    </p>
  </div>
</template>
