<template>
  <input
    :id="id"
    ref="input"
    :type="type"
    :value="modelValue"
    :required="required"
    :placeholder="placeholder"
    :disabled="disabled"
    class="border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm w-full disabled:bg-gray-100 disabled:cursor-not-allowed transition-all duration-200 hover:border-indigo-300"
    @input="$emit('update:modelValue', $event.target.value)"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue';

defineProps({
  id: String,
  type: {
    type: String,
    default: 'text',
  },
  modelValue: String,
  required: {
    type: Boolean,
    default: false,
  },
  placeholder: String,
  disabled: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus();
  }
});

defineExpose({ focus: () => input.value.focus() });
</script>
