<template>
  <div class="mx-auto max-w-7xl my-2">
    <div class="logo-manager bg-white rounded-sm p-2 relative">
      <h1 class="text-l font-semibold text-gray-800 mb-4">Logo</h1>

      <div v-if="logoUrl" class="current-logo text-center mb-4">
        <img :src="logoUrl" alt="Current Logo" class="logo mx-auto mb-4 rounded-sm" />
      </div>
      <div v-else class="text-center text-gray-500 mb-4">
        <p>No logo available.</p>
      </div>

      <div class="upload-logo">
        <button type="button"
          class="mr-2 rounded-sm bg-red-500 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-600">Remove</button>
        <button type="button" @click="showDialog"
          class="rounded-sm bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Change
          logo</button>
      </div>
    </div>

    <media-dialog ref="mediaDialog" :siteId="selectedOption"></media-dialog>
    <media-library v-if="selectedOption && showMediaLibrary" :siteId="selectedOption"
      @selectMedia="selectMedia"></media-library>
  </div>
</template>

<script setup>
import 'vue3-toastify/dist/index.css';
import MediaLibrary from './MediaLibrary.vue';
import MediaDialog from './dialogs/MediaDialog.vue'

import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { useMediaStore } from '../stores/media';

const mediaStore = useMediaStore();
const { fetchLogo } = mediaStore;

const logoUrl = computed(() => mediaStore.logo);
const selectedFile = ref(null);

const selectedOption = ref('');
const showMediaLibrary = ref(false)
const mediaDialog = ref(null)

const showDialog = () => {
  mediaDialog.value.open = true
}

const selectMedia = (value) => {
  selectedFile.value = value
}

const handleSelectChange = (event) => {
  selectedOption.value = event.target.value;
  if (selectedOption.value) {
    fetchLogo(selectedOption.value)
  }
};

onMounted(() => {
  const selectElement = document.getElementById('site');
  if (selectElement) {
    // Initial update
    selectedOption.value = selectElement.value;
    // Set up event listener
    selectElement.addEventListener('change', handleSelectChange);
  }
});

onBeforeUnmount(() => {
  const selectElement = document.getElementById('site');
  if (selectElement) {
    selectElement.removeEventListener('change', handleSelectChange);
  }
});

</script>

<style scoped>
.logo-manager {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.logo {
  max-width: 150px;
  height: auto;
}
</style>
