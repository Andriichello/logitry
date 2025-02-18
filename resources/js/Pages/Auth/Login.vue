<script lang="ts">
  import AuthLayout from "@/Layouts/AuthLayout.vue";

  export default {
    layout: AuthLayout,
  };
</script>

<script setup lang="ts">
  import {onMounted, PropType, ref, watch} from 'vue';
  import {
    AvailableSignIn,
    Company as CompanyModel,
    LoginRequest,
    Me,
    signIns,
    SignInsParams,
    SignInsResult
  } from '@/api';
  import Company from '@/Components/Auth/Company.vue';
  import {useForm} from '@inertiajs/vue3';
  import {POSITION, useToast} from 'vue-toastification';
  import { useThemeStore } from "@/stores/theme";

  const props = defineProps({
    me: Object as PropType<Me> | null,
    error: String as PropType<string> | null,
    success: String as PropType<string> | null,
    errors: Object as PropType<Record<string, string[]>>,
  });

  const themeStore = useThemeStore()

  const error = ref(null as string | null);
  const success = ref(null as string | null);

  const toast = useToast();

  function messages() {
    if (props.error) {
      if (error.value !== props.error) {
        error.value = props.error;
        toast.error(error.value, {
          position: POSITION.BOTTOM_CENTER,
          timeout: 3000,
        });
      }
    } else if (props.errors) {
      Object.keys(props.errors)
        .some((key) => {
          if (props.errors[key]) {
            error.value = typeof props.errors[key] === 'string' || props.errors[key] instanceof String
              ? props.errors[key] : props.errors[key]?.[0];

            toast.error(error.value, {
              position: POSITION.BOTTOM_CENTER,
              timeout: 3000,
            });
            return true;
          }
          return false;
        })
    } else {
      if (error.value !== props.error) {
        error.value = props.error;
      }
    }

    if (props.success) {
      if (success.value !== props.success) {
        success.value = props.success;
        toast.info(success.value, {
          position: POSITION.BOTTOM_CENTER,
          timeout: 3000,
        });
      }
    } else {
      if (success.value !== props.success) {
        success.value = props.success;
      }
    }
  }

  onMounted(() => {
    messages();
  });

  const on = ref('username' as string);
  const username = ref('john@email.com' as string);
  const email = ref(null as string | null);
  const phone = ref(null as string | null);
  const abbreviation = ref(null as string | null);
  const password = ref('pa$$w0rd' as string | null);

  const company = ref(null as CompanyModel | null);
  const companies = ref(null as AvailableSignIn[] | boolean | null) ;
  const isLoadingCompanies = ref(null);

  const isLoggingIn = ref(null);

  watch(isLoggingIn, (newValue, oldValue) => {
    if (newValue === true && oldValue === false) {
      success.value = null;
      error.value = null;
    }

    if (newValue === false && oldValue === true) {
      messages()
    }
  });

  watch(company, async (newValue) => {
    if (newValue !== null) {
      on.value = 'credentials';
      abbreviation.value = newValue.abbreviation;
    } else {
      abbreviation.value = null;

      if (companies.value === false || companies.value?.length === 1) {
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
        toast.error('Failed to check companies. Please try again later.', {
          position: POSITION.BOTTOM_CENTER,
          timeout: 3000,
        });
      } else if (newValue?.length === 0) {
        toast.error('No such user found.', {
          position: POSITION.BOTTOM_CENTER,
          timeout: 3000,
        });
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
      request.email = null;
      email.value = null;
    }

    const isPhone = /^\+?[1-9]\d{1,14}$/.test(username.value);

    if (isPhone) {
      request.phone = username.value;
      phone.value = username.value;
    } else {
      request.phone = null;
      phone.value = null;
    }

    if (!isEmail && !isPhone) {
      toast.error('Username must be a valid email or phone number.', {
        timeout: 2000,
        position: POSITION.BOTTOM_CENTER,
      })
      return;
    }

    isLoadingCompanies.value = true;

    signIns(request)
      .then((result: SignInsResult) => {
        companies.value = result.data.data.sign_ins ?? [];
      })
      .catch((error) => {
        if (error.response.data.message) {
          toast.error(error.response.data.message, {
            timeout: 3000,
            position: POSITION.BOTTOM_CENTER,
          });
          isLoadingCompanies.value = false;
        } else {
          companies.value = true;
        }
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

    useForm(request).post('/login', {
      onSuccess: () => {
        isLoggingIn.value = false;
      },
      onError: () => {
        isLoggingIn.value = false;
      }
    });
  }

</script>

<template>
  <fieldset class="w-full fieldset max-w-xs bg-base-200 border border-base-300 p-4 rounded-box">

    <template v-if="on === 'username'">
      <legend class="fieldset-legend text-lg">Welcome</legend>

      <form v-on:keydown.enter.prevent="submitUsername" @submit.prevent="submitUsername">
        <label class="fieldset-label mb-1">Username<span class="text-error translate-x-[-4px]">*</span></label>
        <input v-model="username" type="text" required
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

      <form v-on:keydown.enter.prevent="submitLogIn" @submit.prevent="submitLogIn">
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
</template>
