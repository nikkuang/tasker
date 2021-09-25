<template>
    <label class="flex items-center cursor-pointer">
        <div class="relative">
        <input type="checkbox" class="sr-only">
        <div class="block bg-gray-600 w-10 h-6 rounded-full"></div>
        <div class="dot absolute left-1 top-1 bg-gray-800 w-4 h-4 rounded-full transition"></div>
        </div>
        <div class="ml-3 text-gray-700 font-medium">
            <slot />
        </div>
    </label>
</template>
<style lang="postcss" scoped>
input:checked ~ .dot {
  transform: translateX(100%);
  @apply bg-white;
  @apply transform;
  @apply translate-x-full;
}
</style>
