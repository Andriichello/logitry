<script setup>
  import {useThemeStore} from "@/stores/theme";

  const theme = useThemeStore();
</script>

<template>
  <main>
    <article>
      <input type="checkbox" value="light" class="toggle theme-controller mt-1"
             :checked="!theme.isDark"
             @change="theme.toggle" hidden/>

      <div class="w-full h-full flex-col justify-start items-center">
        <slot/>
      </div>
    </article>
  </main>
</template>
