<template>
    <div>
        <h1>Hello from Home...</h1>
        <p>{{ me?.name ?? 'Guest' }}</p>
    </div>
</template>

<script setup>
    defineProps({
        me: Object,
    });
</script>
