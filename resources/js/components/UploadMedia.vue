<template>
  <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="logo-manager bg-white rounded-sm p-6 relative">
      <div class="upload-logo">
        <input type="file" @change="handleFileChange"
          class="block w-full rounded-sm text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors" />
      </div>
    </div>

  </div>
</template>

<script setup>
import 'vue3-toastify/dist/index.css';
import { ref} from 'vue';

const selectedFile = ref(null);
const previewUrl = ref(null);

const handleFileChange = (event) => {
  selectedFile.value = event.target.files[0];

  if (selectedFile.value) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewUrl.value = e.target.result;
    };
    reader.readAsDataURL(selectedFile.value);
  } else {
    previewUrl.value = null;
  }
};

const uploadLogo = async () => {
  if (!selectedFile.value) return;

  const formData = new FormData();
  formData.append('logo', selectedFile.value);
  formData.append('siteId', selectedOption.value);

  try {
    const response = await axios.post('/api/logo/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    logoUrl.value = response.data.logo_url;
    previewUrl.value = null;
    selectedFile.value = null;

    toast.success(response.data.message, {
      autoClose: 2000,
    });
  } catch (error) {
    console.error('Error uploading logo:', error);
  }
};


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
