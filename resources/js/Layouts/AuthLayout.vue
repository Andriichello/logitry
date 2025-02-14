<script setup>
import {useThemeStore} from "@/stores/theme";

const theme = useThemeStore();

</script>

<template>
  <main>
    <article>
      <div class="w-full min-h-screen flex flex-col justify-start items-center p-10">
        <div class="w-full flex flex-row justify-center items-center p-10 gap-2">

          <input type="checkbox" value="light" class="toggle theme-controller mt-1"
                 :checked="!theme.isDark"
                 @change="theme.toggle"/>

          <!--      <div class="w-[40px] h-[50px] flex flex-col justify-center items-start mb-1">-->
          <!--        <input type="checkbox" value="light" class="toggle-rotated toggle theme-controller translate-x-[-5px] translate-y-[19px]"-->
          <!--               style="transform: rotate(270deg);"-->
          <!--               :checked="!theme.isDark"-->
          <!--               @change="theme.toggle"/>-->

          <!--        <input type="checkbox" value="light" class="toggle theme-controller mt-2 ml-2"-->
          <!--               :checked="!theme.isDark"-->
          <!--               @change="theme.toggle"/>-->
          <!--      </div>-->

          <h3 class="text-4xl font-bold">Logitry</h3>

          <input type="checkbox" value="light" class="toggle theme-controller opacity-0"
                 :checked="!theme.isDark"
                 @change="theme.toggle"/>
        </div>
        <slot/>
      </div>
    </article>
  </main>
</template>
