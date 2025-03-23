<script setup lang="ts">
  import * as LucideIcons from 'lucide-vue-next';
  import { computed } from 'vue';

  const props = defineProps({
    leftIcon: {
      type: String,
      default: null, // Default to no icon
    },
    rightIcon: {
      type: String,
      default: null, // Default to no icon
    },
    size: {
      type: String as () => ('xs' | 'sm' | 'md' | 'lg' | 'xl'),
      default: 'md',
    },
    iconSize: {
      type: Number,
      default: null,
    },
    invisibleOffset: {
      type: Boolean,
      default: true,
    }
  });

  // Dynamically resolve icons
  const resolveIcon = (iconName: string) => {
    return LucideIcons[iconName] || null; // Ensure icon exists
  };

  const leftLucideIcon = computed(() => props.leftIcon ? resolveIcon(props.leftIcon) : null);
  const rightLucideIcon = computed(() => props.rightIcon ? resolveIcon(props.rightIcon) : null);

  const iconLucideSize = computed(() => {
    if (props.iconSize) {
      return (
        'w-' + props.iconSize + ' h-' + props.iconSize
      );
    }

    switch (props.size) {
      case 'xs':
        return 'w-4 h-4';
      case 'sm':
        return 'w-4 h-4';
      case 'md':
        return 'w-5 h-5';
      case 'lg':
        return 'w-6 h-6';
      case 'xl':
        return 'w-6 h-6';

      default:
        return 'w-5 h-5';
    }
  });

</script>

<template>
  <button class="w-fit h-fit btn btn-outline flex flex-row justify-center items-center px-2 border-base-content/60"
          :class="`btn-${props.size}`">
    <component :class="[
                  iconLucideSize,
                  leftLucideIcon ? 'opacity-100' : 'opacity-0',
               ]"
               :is="leftLucideIcon ?? rightLucideIcon"
               v-if="leftLucideIcon || (invisibleOffset && rightLucideIcon)" />

    <span class="w-full pb-1 pt-1.5">
      <slot/>
    </span>

    <component :class="[
                  iconLucideSize,
                  rightLucideIcon ? 'opacity-100' : 'opacity-0',
               ]"
               :is="rightLucideIcon ?? leftLucideIcon"
               v-if="rightLucideIcon || (invisibleOffset && leftLucideIcon)"/>
  </button>
</template>
