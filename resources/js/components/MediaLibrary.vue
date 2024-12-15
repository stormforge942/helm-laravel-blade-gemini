<template>
  <div class="flex relative">
    <div class="bg-white p-6" style="width: 75%;">
      <div class="media-gallery my-2">
        <div v-if="loading" class="text-center">
          <p>Loading media...</p>
        </div>

        <div v-else-if="error" class="text-center text-red-500">
          <p>Error loading media: {{ error }}</p>
        </div>

        <div v-else class="grid xs:grid-cols-4 grid-cols-7 md:grid-cols-7 gap-4 m-4">
          <div v-for="media in mediaItems" :key="media.id"
            class="media-item relative border-4 hover:border-blue-400 cursor-pointer transition-all duration-300 ease-in-out"
            :class="{ 'border-blue-500 bg-blue-100 shadow-lg': selectedMediaId === media.id }"
            @click="selectMedia(media)">
            <img :src="media.source_url" :alt="media.title.rendered"
              class="media-image w-full min-h-36 h-36 object-cover shadow-md" />
            <p class="text-center mt-2 text-sm text-gray-600" style="display: none;">{{ media.title.rendered }}</p>
          </div>
        </div>
      </div>

    </div>
    <div class="bg-gray-100 p-4" style="width: 25%;">
      <h2 class="text-sm font-medium mb-4 uppercase text-gray-700">Attachment Details</h2>

      <div class="mb-2">
        <img src="http://wordpress.test/wp-content/uploads/2024/08/39965743eb30634afdc5906133e19740.jpg"
          alt="Attachment Image" class="w-24 h-24 object-cover mb-2 border">
        <p class="text-xs text-gray-600">39965743eb30634afdc5906133e19740.jpg</p>
        <p class="text-xs text-gray-600">August 20, 2024</p>
        <p class="text-xs text-gray-600">32 KB</p>
        <p class="text-xs text-gray-600">736 by 736 pixels</p>
      </div>

      <div class="flex space-x-4 mb-4">
        <button class="text-xs text-red-600 hover:underline">Delete permanently</button>
      </div>

      <div class="mb-4">
        <label for="alt-text" class="block text-xs font-medium text-gray-700">Alt Text</label>
        <p class="text-xs text-gray-500 mb-1">
          Learn how to describe the purpose of the image
          <a href="#" class="text-indigo-600 hover:underline">(opens in a new tab)</a>.
          Leave empty if the image is purely decorative.
        </p>
        <input type="text" id="alt-text" name="alt-text" v-model="mediaFormData.altText"
          class="block w-full rounded-md border-gray-300 py-2 px-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
      </div>

      <div class="mb-4">
        <label for="title" class="block text-xs font-medium text-gray-700">Title</label>
        <input type="text" id="title" name="title" v-model="mediaFormData.title"
          class="block w-full rounded-md border-gray-300 py-2 px-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
      </div>

      <div class="mb-4">
        <label for="caption" class="block text-xs font-medium text-gray-700">Caption</label>
        <QuillEditor toolbar="minimal" v-model="mediaFormData.description" />
      </div>

      <div class="mb-4">
        <label for="description" class="block text-xs font-medium text-gray-700">Description</label>
        <textarea id="description" name="description" rows="3" v-model="mediaFormData.description"
          class="block w-full rounded-md border-gray-300 py-2 px-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"></textarea>
      </div>

      <div class="mb-4">
        <label for="file-url" class="block text-xs font-medium text-gray-700">File URL</label>
        <input type="text" id="file-url" name="file-url"
          value="http://wordpress.test/wp-content/uploads/2024/08/39965743eb30634afdc5906133e19740.jpg" readonly
          class="block w-full rounded-md border-gray-300 py-2 px-3 text-sm bg-gray-100 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
      </div>
    </div>
  </div>


</template>

<script setup>
import { QuillEditor } from '@vueup/vue-quill'
import { ref, onMounted, reactive } from 'vue';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import axios from 'axios';

const emit = defineEmits(['selectMedia'])
const props = defineProps(['siteId'])

const mediaItems = ref([]);
const loading = ref(true);
const error = ref(null);
const selectedMediaId = ref(null);
const mediaFormData = reactive({
  altText: null,
  title: null,
  caption: null,
  description: null,
})

const selectMedia = (media) => {
  selectedMediaId.value = media.id;
  mediaFormData.altText = media.alt_text
  mediaFormData.title = media.title.rendered
  mediaFormData.caption = media.caption.rendered
  mediaFormData.description = media.description.rendered

  emit('selectMedia', media.source_url)
};

const fetchMedia = async () => {
  try {
    const response = await axios.get('/api/media', {
      params: {
        siteId: props.siteId
      }
    });
    mediaItems.value = response.data;
  } catch (err) {
    error.value = err.response ? err.response.data.error : 'An error occurred';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchMedia();
});
</script>