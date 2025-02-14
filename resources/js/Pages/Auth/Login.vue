<script setup lang="ts">
  import { PropType, ref, watch } from 'vue';
  import { AvailableSignIn, LoginRequest, Me, signIns, SignInsParams, SignInsResult } from '../../api';
  import Company from '../../Components/Auth/Company.vue';
  import { useForm } from '@inertiajs/vue3';

  const props = defineProps({
    me: Object as PropType<Me> | null,
    success: String as PropType<string> | null,
  });

  if (props.success) {
    alert(props.success);
  }

  const on = ref('username' as string);
  const username = ref('john@email.com' as string);
  const email = ref(null as string | null);
  const phone = ref(null as string | null);
  const abbreviation = ref(null as string | null);
  const password = ref('pa$$w0rd' as string | null);

  const company = ref(null as Company | null);
  const companies = ref(null as AvailableSignIn[] | null) ;
  const isLoadingCompanies = ref(null);

  const isLoggingIn = ref(null);

  watch(company, async (newValue) => {
    if (newValue !== null) {
      on.value = 'credentials';
      abbreviation.value = newValue.abbreviation;
    } else {
      abbreviation.value = null;

      if (company.value?.length === 1) {
        on.value = 'username';
      } else {
        on.value = 'companies';
      }
    }
  });

  watch(companies, async (newValue, oldValue) => {
    isLoadingCompanies.value = false;

    if (newValue !== oldValue) {
      if (newValue === false) {
        alert('Failed to check companies. Please try again later.');
      } else if (newValue?.length === 0) {
        alert('No such user found.');
      } else if (newValue?.length === 1) {
        company.value = newValue[0].company;
        on.value = 'credentials';
      } else {
        on.value = 'companies';
      }
    }
  });

  function submitUsername() {
    const request = {} as SignInsParams;

    const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(username.value);

    if (isEmail) {
      request.email = username.value;
      email.value = username.value;
    } else {
      email.value = null;
    }

    const isPhone = /^\+?[1-9]\d{1,14}$/.test(username.value);

    if (isPhone) {
      request.phone = username.value;
      phone.value = username.value;
    } else {
      phone.value = null;
    }

    if (!isEmail && !isPhone) {
      return;
    }

    isLoadingCompanies.value = true;

    signIns(request)
      .then((result: SignInsResult) => {
        companies.value = result.data.data.sign_ins ?? [];
      })
      .catch(() => {
        companies.value = false;
      });
  }

  function submitLogIn() {
    isLoggingIn.value = true;

    const request = {
      abbreviation: abbreviation.value,
      email: email.value,
      phone: phone.value,
      password: password.value,
    } as LoginRequest;

    useForm(request).post('/login');
  }

</script>

<template>
  <div class="w-full min-h-screen flex flex-col justify-start items-center p-10">
    <div class="w-full flex flex-row justify-center items-center p-10">
      <input type="checkbox" value="light" class="toggle theme-controller" checked/>
      <h3 class="text-4xl font-bold p-4">Logitry</h3>
    </div>

    <fieldset class="w-full fieldset max-w-xs bg-base-200 border border-base-300 p-4 rounded-box">

      <template v-if="on === 'username'">
        <legend class="fieldset-legend text-lg">Welcome</legend>

        <form v-on:keydown.enter.prevent="submitUsername">
          <label class="fieldset-label mb-1">Username<span class="text-error translate-x-[-4px]">*</span></label>
          <input v-model="username" type="email" required
                 class="input"
                 placeholder="Type here"/>

          <div class="w-full flex justify-end">
            <button class="btn btn-sm btn-primary mt-4 w-fit"
                    @click="submitUsername"
                    :disabled="isLoadingCompanies">
              <span class="loading loading-xs loading-spinner" v-if="isLoadingCompanies"/>
                Continue
            </button>
          </div>
        </form>
      </template>

      <template v-else-if="on === 'companies'">
        <legend class="fieldset-legend text-lg">Select Company</legend>

        <span class="translate-y-[-20px] ml-1">
          {{ username }}
        </span>

        <div class="w-full h-fit max-h-[200px] flex flex-col justify-start gap-1 overflow-y-auto">
          <template v-for="singIn in companies" :key="singIn.company.id">
            <Company :company="singIn.company"
                     :role="singIn.role"
                     :with-password="singIn.with_password"
                      @select="company = $event.company"/>
          </template>
        </div>

        <div class="w-full flex justify-start">
          <button class="btn btn-sm btn-ghost mt-2 w-fit"
                  @click="on = 'username'">
              Back
          </button>
        </div>
      </template>

      <template v-else-if="on === 'credentials'">
        <legend class="fieldset-legend text-lg">Log In</legend>

        <span class="translate-y-[-20px]">
          {{ company?.name }}
        </span>

        <form v-on:keydown.enter.prevent="submitUsername">
          <label class="fieldset-label mb-1">Password<span class="text-error translate-x-[-4px]">*</span></label>
          <input v-model="password" type="password" required
                 class="input"
                 placeholder="Type here"/>

          <div class="w-full flex justify-between">
            <button class="btn btn-sm btn-ghost mt-4 w-fit"
                    @click="company = null">
              Back
            </button>

            <button class="btn btn-sm btn-primary mt-4 w-fit"
                    @click="submitLogIn"
                    :disabled="isLoggingIn">
              <span class="loading loading-xs loading-spinner" v-if="isLoggingIn"/>
                Log In
              <span class="loading loading-xs loading-spinner opacity-0" v-if="isLoggingIn"/>
            </button>
          </div>
        </form>
      </template>


    </fieldset>
  </div>
</template>
