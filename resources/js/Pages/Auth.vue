<script setup lang="ts">
  import { onMounted, PropType } from 'vue';
  import { Me } from '../api';
  import { login, LoginRequest, LoginResult } from '../api';
  import {useForm} from "@inertiajs/vue3";

  defineProps({
    me: Object as PropType<Me> | null,
  });

  const form = useForm({
    abbreviation: "",
    username: "",
    password: "",
  });

  function logIn() {
    const request = {
      abbreviation: form.abbreviation,
      email: form.username,
      password: form.password,
    } as LoginRequest;

    console.log(request);

    login(request)
      .then((result: LoginResult) => {
        console.log(result);
      })
      .catch((error) => {
        console.error(error);
      });
  }

  // onMounted(() => {
    // if (this?.me) {
    //   router.visit('/home');
    // }
    //
    // const request = {
    //   email: 'john@email.com',
    //   password: 'pa$$w0rd',
    //   abbreviation: 'das-auto',
    // } as LoginRequest;
    //
    // console.log(request);
    //
    // login(request)
    //   .then((result: LoginResult) => {
    //     console.log(result);
    //   })
    //   .catch((error) => {
    //     console.error(error);
    //   });
  // });
</script>

<template>
  <div class="w-full min-h-screen flex flex-col justify-start items-center p-10">
    <div class="w-full flex flex-row justify-center items-center p-10">
      <input type="checkbox" value="light" class="toggle theme-controller" checked/>
      <h3 class="text-4xl font-bold p-4">Logitry</h3>
    </div>

    <fieldset class="w-full fieldset max-w-xs bg-base-200 border border-base-300 p-4 rounded-box">
      <legend class="fieldset-legend text-lg">Welcome</legend>

      <label class="fieldset-label">Company</label>
      <input v-model="form.abbreviation" type="text" class="input" placeholder="Company"/>

      <label class="fieldset-label">Phone or Email</label>
      <input v-model="form.username" type="email" class="input" placeholder="Email"/>

      <label class="fieldset-label right">Password</label>
      <input v-model="form.password" type="password" class="input" placeholder="Password"/>

      <div class="flex flex-row justify-end items-center mt-2">
        <a href="/forgot-password" class="text-xs font-semibold">Forgot Password?</a>
      </div>

      <button class="btn btn-neutral mt-4"
        @click="logIn">
        Log In
      </button>
    </fieldset>
  </div>
</template>
