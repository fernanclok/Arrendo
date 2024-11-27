<template>
  <div class="notification">
      <div class="icon-container">
          <i :class="iconClass"></i>
      </div>
      <div class="flex flex-col mx-10 text-container">
          <div class="text-lg">
              <strong>{{ title }}</strong>
          </div>
          <div class="text-sm" v-html="message"></div>
      </div>
      <div @click="$emit('close')" class="cursor-pointer close-button">close</div>
  </div>
</template>

<script>
export default {
  props: {
      type: {
          type: String,
          required: true,
      },
      title: {
          type: String,
          required: true,
      },
      message: {
          type: String,
          required: true,
      },
  },
  computed: {
      iconClass() {
          switch (this.type) {
              case 'success':
                  return 'mdi mdi-check-circle-outline text-green-500';
              case 'error':
                  return 'mdi mdi-alert-outline text-red-500';
              case 'info':
                  return 'mdi mdi-information-outline text-blue-500';
              default:
                  return '';
          }
      },
  },
};
</script>

<style scoped>
.notification {
  display: flex;
  align-items: center;
  background-color: white;
  padding: 20px;
  margin-bottom: 20px;
  margin-right: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
  min-width: 450px;
  max-width: 450px;
}

.icon-container {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  width: 50px; 
  height: 50px;
}

.text-container {
  display: flex;
  flex-direction: column;
  justify-content: center; 
  flex-grow: 1; 
  max-width: calc(100% - 70px);
}

.text-sm {
  word-wrap: break-word;
  overflow: hidden;
  text-overflow: ellipsis;
}

.cursor-pointer {
  cursor: pointer;
}

.close-button {
  margin-left: 20px;
}
</style>