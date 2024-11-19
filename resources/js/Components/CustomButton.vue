<template>
    <button
      :class="[
        'inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150',
        buttonClass
      ]"
      :disabled="disabled"
    >
      <slot />
    </button>
  </template>
  
  <script>
  export default {
    props: {
      type: {
        type: String,
        default: 'primary',
        validator: value => ['primary', 'danger'].includes(value)
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      buttonClass() {
        if (this.disabled) {
          return 'bg-gray-200 text-gray-700 cursor-not-allowed';
        }
        switch (this.type) {
          case 'danger':
            return 'bg-red-500 text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:ring-red-500';
          case 'primary':
          return 'bg-primary text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500';
          case 'cancel':
          return 'bg-red-500 text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:ring-red-500';
          case'success':
          return 'bg-green-500 text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500';
          case 'warning':
          return 'bg-yellow-500 text-white hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:ring-yellow-500';
          case 'text':
          return 'text-blue-500';
          case 'text-error':
          return 'text-red-500  focus:ring-red-500';
          case 'outlined':
          return 'text-gray-500 border-2 border-gray-300 hover:bg-gray-50 focus:bg-gray-50 active:bg-gray-50 focus:ring-gray-300';
          default:
            return 'bg-blue-500 text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:ring-blue-500';
        }
      }
    }
  };
  </script>