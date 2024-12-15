<template>
  <div>
    <select v-model="selectedNicheId" @change="changed"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
      <option v-for="niche in niches" :key="niche.id" :value="niche.id">
        {{ niche.niche }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useNicheStore } from '../stores/niche';

const emit = defineEmits(['changed'])

const nicheStore = useNicheStore();

const selectedNicheId = computed({
  get: () => nicheStore.selectedNicheId,
  set: (value) => {
    nicheStore.setSelectedNicheId(value);
    emit('changed', value);
  }
});

const niches = computed(() => nicheStore.getNiches);

onMounted(async () => {
  await nicheStore.fetchNiches();
});

</script>