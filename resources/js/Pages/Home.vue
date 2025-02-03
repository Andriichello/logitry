<script setup lang="ts">
  import { onMounted, PropType } from 'vue';
  import { Me } from '../api';
  import { login, LoginRequest, LoginResult } from '../api';

  defineProps({
    me: Object as PropType<Me> | null,
  });

  onMounted(() => {
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
  <div>
    <input type="checkbox" value="light" class="toggle theme-controller" checked hidden/>

    <h1>Hello from Home...</h1>
    <p class="badge badge-primary">{{ me?.name ?? 'Guest' }}</p>
  </div>
</template>
