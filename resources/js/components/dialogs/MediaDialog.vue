<template>
  <TransitionRoot as="template" :show="open">
    <Dialog class="relative z-10" @close="open = false">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
        leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel
              class="relative transform rounded-lg bg-white text-left w-full shadow-xl transition-all sm:my-8">
              <div class="bg-white px-4 pt-5">
                <div class="mt-3  sm:ml-4 sm:mt-0">
                  <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Select logo</DialogTitle>
                  <div class="mt-2">
                    <div class="sm:hidden">
                      <label for="tabs" class="sr-only">Select a tab</label>
                      <select id="tabs" name="tabs" v-model="selectedTab"
                        class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        <option v-for="tab in tabs" :key="tab.name" :value="tab.name">
                          {{ tab.name }}
                        </option>
                      </select>
                    </div>
                    <div class="hidden sm:block">
                      <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                          <a v-for="tab in tabs" :key="tab.name" href="javascript:void(0)" @click="selectTab(tab)"
                            :class="[tab.current ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium']"
                            :aria-current="tab.current ? 'page' : undefined">{{ tab.name }}</a>
                        </nav>
                      </div>
                    </div>
                    <div v-if="selectedTab === 'Upload Files'">
                      <upload-media></upload-media>
                    </div>
                    <div v-else>
                      <media-library :siteId="siteId"></media-library>
                    </div>
                  </div>
                </div>

              </div>
              <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button"
                  class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto"
                  @click="open = false">Select</button>
                <button type="button"
                  class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                  @click="open = false" ref="cancelButtonRef">Cancel</button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>

import MediaLibrary from '../MediaLibrary.vue'
import UploadMedia from '../UploadMedia.vue'

import { ref, reactive, computed } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

const props = defineProps(['siteId'])
const open = ref(false)

const tabs = reactive([
  { name: 'Upload Files', current: true },
  { name: 'Media Library', current: false },
])

const selectedTab = computed({
  get: () => tabs.find(tab => tab.current)?.name,
  set: (value) => {
    tabs.forEach(tab => tab.current = tab.name === value);
  },
});

const selectTab = (selectedTab) => {
  tabs.forEach((tab) => {
    tab.current = false
  });
  selectedTab.current = true
}

defineExpose({
  open
})
</script>