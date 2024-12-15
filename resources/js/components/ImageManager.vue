<template>
  <TransitionRoot as="template" :show="open">
    <Dialog class="relative z-10" @close="triggerClose">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        />
      </TransitionChild>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div
          class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0"
        >
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel
              class="relative transform rounded-lg bg-white text-left w-full shadow-xl transition-all sm:my-8"
            >
              <div class="bg-white px-4 pt-5">
                <div class="mt-3 sm:ml-4 sm:mt-0">
                  <DialogTitle
                    as="h3"
                    class="text-base font-semibold leading-6 text-gray-900"
                    >Image Library</DialogTitle
                  >
                  <div class="my-4">
                    <label
                      for="niche"
                      class="block text-sm font-medium text-gray-700"
                      >Select Niche</label
                    >
                    <niche-select @changed="fetchImagesByNiche"></niche-select>

                    <p v-if="!formData.niche_id" class="mt-2 text-sm text-red-600">
                      Please select a niche before uploading or selecting an image.
                    </p>
                  </div>

                  <div class="mt-2">
                    <div class="sm:hidden">
                      <label for="tabs" class="sr-only">Select a tab</label>
                      <select
                        id="tabs"
                        name="tabs"
                        v-model="selectedTab"
                        class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                      >
                        <option
                          v-for="tab in tabs"
                          :key="tab.name"
                          :value="tab.name"
                        >
                          {{ tab.name }}
                        </option>
                      </select>
                    </div>
                    <div class="hidden sm:block">
                      <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                          <a
                            v-for="tab in tabs"
                            :key="tab.name"
                            href="javascript:void(0)"
                            @click="selectTab(tab)"
                            :class="[
                              tab.current
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                              'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium',
                            ]"
                            :aria-current="tab.current ? 'page' : undefined"
                            >{{ tab.name }}</a
                          >
                        </nav>
                      </div>
                    </div>
                    <div v-if="selectedTab === 'Upload Image'">
                      <div class="mb-4">
                        <div
                          v-if="filePreview"
                          class="current-logo text-center my-4"
                        >
                          <img
                            :src="filePreview"
                            alt="File Preview"
                            class="logo mx-auto h-36 mb-4 rounded-sm"
                          />
                        </div>

                        <div v-if="fileTooLarge" class="text-red-500 text-sm mt-2">
                          The selected file is too large. Please choose a file smaller than 2 MB.
                        </div>
                        <Uploader @fileChanged="fileChanged"></Uploader>

                        <label
                          for="title"
                          class="block text-xs font-medium text-gray-700"
                          >Title</label
                        >
                        <input
                          type="text"
                          id="title"
                          name="title"
                          v-model="formData.title"
                          class="block w-full rounded-md border-gray-300 py-2 px-3 mb-4 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                          :disabled="!formData.niche_id"
                        />

                        <label
                          for="alt-text"
                          class="block text-xs font-medium text-gray-700"
                          >Alt Text</label
                        >
                        <input
                          type="text"
                          id="alt-text"
                          name="alt-text"
                          v-model="formData.alt_text"
                          class="block w-full rounded-md border-gray-300 py-2 px-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                          :disabled="!formData.niche_id"
                        />

                        <button
                          type="button"
                          :disabled="!formData.niche_id || !formData.file"
                          @click="uploadFile"
                          :class="[
                            'mt-5 float-right rounded-md px-3.5 py-2.5 text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
                            {
                              'bg-indigo-600 text-white hover:bg-indigo-500 focus-visible:outline-indigo-600':
                                formData.niche_id && formData.file,
                              'bg-gray-300 text-gray-500 cursor-not-allowed':
                                !formData.niche_id || !formData.file,
                            },
                          ]"
                        >
                          Upload
                        </button>
                      </div>
                    </div>
                    <div v-else class="mb-4">
                      <p class="text-sm my-4 font-semibold text-gray-600">
                        <span v-if="images.length"
                          >Select the image you want to use</span
                        >
                      </p>
                      <div v-if="images.length" class="grid grid-cols-8 gap-4">
                        <div
                          v-for="image in images"
                          :key="image.id"
                          :class="{
                            'border p-2 rounded hover:border-blue-500 cursor-pointer': true,
                            'border-2 border-blue-700': image === selectedImage,
                          }"
                          @click="selectImage(image)"
                          >
                          <img
                            :src="image.url"
                            :alt="image.alt_text"
                            :title="image.title"
                            class="h-30 w-full object-cover rounded"
                          />
                          <!-- <p class="mt-2 text-xs text-gray-600">{{ image.name }}</p> -->
                        </div>
                      </div>

                      <div v-else class="text-center text-gray-500 text-sm my-10">
                        <span v-if="formData.niche_id">No images available for this niche.</span>
                        <span v-else>Please select niche to view the image library.</span>
                      </div>

                      <div class="my-4" v-if="images.length">
                        <label
                          for="title"
                          class="block text-xs font-medium text-gray-700"
                          >Title</label
                        >
                        <input
                          type="text"
                          id="title"
                          name="title"
                          v-model="selectedImage.title"
                          class="block w-full rounded-md border-gray-300 py-2 px-3 mb-4 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                          :disabled="!formData.niche_id"
                        />

                        <label
                          for="alt-text"
                          class="block text-xs font-medium text-gray-700"
                          >Alt Text</label
                        >
                        <input
                          type="text"
                          id="alt-text"
                          name="alt-text"
                          v-model="selectedImage.alt_text"
                          class="block w-full rounded-md border-gray-300 py-2 px-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500"
                          :disabled="!formData.niche_id"
                        />

                        <button
                          type="button"
                          v-if="selectedImage.id !== null"
                          @click="updateImageData"
                          class="mt-5 float-right rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                          Update Info
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
              >
                <button
                  type="button"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                  @click="triggerClose"
                  ref="cancelButtonRef"
                >
                  Close
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import "vue3-toastify/dist/index.css";
import NicheSelect from "./NicheSelect.vue";
import Uploader from "./Uploader.vue";

import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { ref, onMounted, computed, reactive } from "vue";
import { useMediaStore } from "../stores/media";
import { toast } from "vue3-toastify";

const props = defineProps({
  sectionId: {
    type: String,
    required: true,
  }
});

const mediaStore = useMediaStore();

const open = ref(false);
const fileTooLarge = ref(false); 

const tabs = reactive([
  { name: "Upload Image", current: false },
  { name: "Media Library", current: true },
]);

const selectedTab = computed({
  get: () => tabs.find((tab) => tab.current)?.name,
  set: (value) => {
    tabs.forEach((tab) => (tab.current = tab.name === value));
  },
});

const images = computed(() => mediaStore.images);
const selectedImage = computed(() => mediaStore.getSelectedImage);
const nicheSelect = ref(null);
const filePreview = ref(null);

const initialFormData = {
  name: null,
  file: null,
  alt_text: null,
  niche_id: null,
  title: null
};

const formData = ref({ ...initialFormData });

const resetFormData = () => {
  formData.value.name = null;
  formData.value.file = null;
  formData.value.alt_text = null;
  formData.value.niche_id = null;
  formData.value.title = null;
  filePreview.value = null;

  const fileInput = document.querySelector('input[type="file"]');
  if (fileInput) {
    fileInput.value = null;
  }
};

const selectTab = (selectedTab) => {
  tabs.forEach((tab) => {
    tab.current = false;
  });
  selectedTab.current = true;
};

const updateImageData = async () => {
  await mediaStore.updateAltTxt(selectedImage.id);

  toast.success("Image alt text updated", {
    autoClose: 1000,
  });
};


const fileChanged = (preview, file) => {
  const maxSize = 2048 * 1024;  

  if (file.size > maxSize) {
    fileTooLarge.value = true; 
    filePreview.value = null;  
    formData.value.file = null; 
    toast.error("File is too large. Please select a file smaller than 2 MB.");
  } else {
    fileTooLarge.value = false; 
    filePreview.value = preview;
    formData.value.file = file;
    formData.value.name = file.name;
  }
};

const fetchImagesByNiche = async (id) => {
  if (id) {
    resetFormData();

    await mediaStore.fetchImagesByNiche(id);
    formData.value.niche_id = id;
  }
};

const uploadFile = async () => {
  await mediaStore.uploadImage(formData.value);
  if (!formData.value.title) {
    console.error('Title is undefined or empty.');
    return;
  }
  toast.success("Image uploaded", {
    autoClose: 1000,
  });

  const uploadedData = {
    sectionId: props.sectionId,
    file: formData.value.file,
    altText: formData.value.alt_text,
    nicheId: formData.value.niche_id,
    title: formData.value.title
  };
  console.log(uploadedData);

  const event = new CustomEvent('file-uploaded', { detail: uploadedData });
  window.dispatchEvent(event);

  resetFormData();
};


const selectImage = async (image) => {
  mediaStore.selectImage(image);

  const selectedImageData = {
    sectionId: props.sectionId,
    id: image.id,
    url: image.url,
    altText: image.alt_text,
    nicheId: image.niche_id,
    title: image.title
  }

  const event = new CustomEvent('image-selected', { 
    detail: { 
      sectionId: props.sectionId,
      imageData: selectedImageData } });
  window.dispatchEvent(event);

    toast.info("Image selected", {
    autoClose: 500,
    hideProgressBar: true

  });
};

const triggerClose = () => {
  open.value = false;
};

defineExpose({
  open,
});

onMounted(async () => {
  await mediaStore.fetchNiches();
});
</script>
