<template>
  <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="file-manager bg-white rounded-sm p-6 relative">
      <div class="upload-file">
        <input type="file" @change="handleFileChange"
          accept=".jpg,.jpeg,.gif,.png"
          class="block w-full rounded-sm text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors" />
      </div>
    </div>
  </div>
</template>

<script setup>
import 'vue3-toastify/dist/index.css';
import { ref } from 'vue';

const selectedFile = ref(null);
const previewUrl = ref(null);

const emit = defineEmits(['fileChanged'])

const handleFileChange = (event) => {
  selectedFile.value = event.target.files[0];

  if (selectedFile.value) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewUrl.value = e.target.result;
      emit('fileChanged', previewUrl.value, selectedFile.value)
    };
    reader.readAsDataURL(selectedFile.value);
  } else {
    previewUrl.value = null;
  }
};

</script>

<style scoped>
.file-manager {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.file {
  max-width: 150px;
  height: auto;
}
</style>
