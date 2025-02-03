<script setup lang="ts">
  import { onMounted, PropType } from 'vue';
  import { Me } from '../api';
  import { login, LoginRequest, LoginResult } from '../api';

  defineProps({
    me: Object as PropType<Me> | null,
  });

  onMounted(() => {
    if (this?.me) {
      router.visit('/home');
    }

    const request = {
      email: 'john@email.com',
      password: 'pa$$w0rd',
      abbreviation: 'das-auto',
    } as LoginRequest;

    console.log(request);

    login(request)
      .then((result: LoginResult) => {
        console.log(result);
      })
      .catch((error) => {
        console.error(error);
      });
  });
</script>

<template>
  <div class="w-full flex flex-col justify-center items-center p-10">
    <div class="flex flex-row justify-center items-center p-10">
      <input type="checkbox" value="light" class="toggle theme-controller" />
      <h3 class="text-4xl font-bold p-4">Theme Toggle</h3>
    </div>

    <fieldset class="fieldset w-xs bg-base-200 border border-base-300 p-4 rounded-box">
      <legend class="fieldset-legend text-lg">Welcome</legend>

      <label class="fieldset-label">Email</label>
      <input type="email" class="input" placeholder="Email" />

      <label class="fieldset-label">Password</label>
      <input type="password" class="input" placeholder="Password" />

      <button class="btn btn-neutral mt-4">Login</button>
    </fieldset>
  </div>
</template>
