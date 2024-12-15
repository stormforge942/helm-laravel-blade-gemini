<template>
  <div>
    <div v-if="selectedImage" class="text-left my-4">
      <div class="inline-block p-2 border-2 border-green-400 rounded-md">
        <img :src="selectedImage.url" :alt="selectedImage.altText" class="h-36 rounded-sm"/>
      </div>
      <small class="block mt-1 ml-2 text-green-600">New image selected</small>
    </div>

    <button
      type="button"
      @click="openLightbox"
      class="rounded-sm bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
    >
      {{ selectedImage ? 'Select Another Image' : buttonText }}
    </button>

    <image-manager ref="imgManager" :section-id="sectionId"></image-manager>
  </div>
</template>


<script setup>
import ImageManager from "../ImageManager.vue";
import { ref, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  sectionId: String
});

const imgManager = ref(null);
const buttonText = ref('Image Library');
const selectedImage = ref(null);

const imageSelected = (event) => {
  const { sectionId, imageData } = event.detail;
  if (sectionId === props.sectionId) {  // Only handle the event if the sectionId matches
    console.log('Image data received in LibraryButton:', imageData);
    selectedImage.value = {
      url: imageData.url,
      altText: imageData.altText || ''
    };
    buttonText.value = 'Image Selected';
  }
};

onMounted(() => {
  window.addEventListener('image-selected', imageSelected);
});

onBeforeUnmount(() => {
  window.removeEventListener('image-selected', imageSelected);
});

const openLightbox = () => {
  imgManager.value.open = true;
};

const closeLightbox = () => {
  imgManager.value.open = false;
};
</script>

