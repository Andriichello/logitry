<script setup lang="ts">
    import { PropType } from 'vue';
    import Me = Models.Me;

    defineProps({
        me: Object as PropType<Me>,
    });
</script>

<template>
    <div>
        <h1>Hello from Home...</h1>
        <p class="badge badge-accent">{{ me?.name ?? 'Guest' }}</p>
    </div>
</template>
