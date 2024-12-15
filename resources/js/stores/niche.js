import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useNicheStore = defineStore('niche', () => {
  const niches = ref([]);
  const selectedNiche = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const selectedNicheId = ref(null)

  // Fetch all niches
  const fetchNiches = async () => {
    loading.value = true;
    try {
      const response = await axios.get('/api/niches');
      niches.value = response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Error fetching niches';
    } finally {
      loading.value = false;
    }
  };

  // Fetch a single niche by ID
  const fetchNicheById = async (id) => {
    loading.value = true;
    try {
      const response = await axios.get(`/api/niches/${id}`);
      selectedNiche.value = response.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Error fetching niche';
    } finally {
      loading.value = false;
    }
  };

  const setSelectedNicheId = (nicheId) => {
    selectedNicheId.value = nicheId;
  }

  // Getters
  const getNiches = computed(() => niches.value);
  const getSelectedNiche = computed(() => selectedNiche.value);

  return {
    niches,
    selectedNiche,
    loading,
    error,
    fetchNiches,
    fetchNicheById,
    getNiches,
    getSelectedNiche,
    setSelectedNicheId,
    selectedNicheId
  };
});
