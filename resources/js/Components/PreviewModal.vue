<template>
    <div v-if="show" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg overflow-hidden w-4/5 h-4/5 relative">
        <button
          @click="$emit('close')"
          class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full hover:bg-red-600"
        >
          &times;
        </button>
        <!-- <div class="p-4 flex justify-center items-center h-full"> -->
          <!-- <div v-if="fileType === 'image'" class="h-full flex justify-center">
            <img :src="filePath" alt="Document preview" class="max-h-full max-w-full object-contain" />
          </div>
          <div v-else-if="fileType === 'pdf'" class="h-full w-full">
            <iframe :src="filePath" class="w-full h-full" frameborder="0"></iframe>
          </div>
          <div v-else class="text-gray-500 text-center">
            <p>Unsupported file type</p>
          </div> -->
        <!-- </div> -->
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      show: {
        type: Boolean,
        required: true,
      },
      filePath: {
        type: String,
        required: true,
      },
      fileType: {
        type: String,
        required: true,
      },
    },
  };
  </script>
  
  <style scoped>
  </style>  