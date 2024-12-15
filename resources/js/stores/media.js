import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "axios";

export const useMediaStore = defineStore("media", () => {
  // State
  const logo = ref(null);
  const selectedLogo = ref(null);
  const images = ref([]);
  const selectedImage = ref({
    id: null,
    alt_text: null,
    filename: null,
    name: null,
    niche_id: null,
    title: null
  });
  const niches = ref([]);
  const loading = ref(false);
  const error = ref(null);

  // Actions
  const fetchLogo = async (siteId) => {
    loading.value = true;
    try {
      const response = await axios.get("/api/logo", {
        params: {
          siteId: siteId,
        },
      });
      logo.value = response.data.logo_url;
    } catch (err) {
      error.value = err.response?.data?.message || "Error fetching logo";
    } finally {
      loading.value = false;
    }
  };

  const uploadLogo = async (file) => {
    loading.value = true;
    const formData = new FormData();
    formData.append("logo", file);

    try {
      const response = await axios.post("/api/logo/upload", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      logo.value.push(response.data);
    } catch (err) {
      error.value = err.response?.data?.message || "Error uploading logo";
    } finally {
      loading.value = false;
    }
  };

  const deleteLogo = async () => {
    loading.value = true;
    try {
      await axios.delete("/api/logo/delete");
      selectedLogo.value = null;
    } catch (err) {
      error.value = err.response?.data?.message || "Error deleting logo";
    } finally {
      loading.value = false;
    }
  };

  const fetchImagesByNiche = async (nicheId) => {
    loading.value = true;
    try {
      const response = await axios.get(`/api/images/niche/${nicheId}`);
      images.value = response.data;
    } catch (err) {
      error.value =
        err.response?.data?.message || "Error fetching images";
    } finally {
      loading.value = false;
    }
  };

  const uploadImage = async (imageData) => {
    loading.value = true;
    const formData = new FormData();

    formData.append("image", imageData.file);
    formData.append("name", imageData.name);
    formData.append("alt_text", imageData.alt_text);
    formData.append("niche_id", imageData.niche_id);
    formData.append("title", imageData.title);

    try {
      const response = await axios.post("/api/images", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      images.value.push(response.data);
    } catch (err) {
      error.value =
        err.response?.data?.message || "Error uploading image";
    } finally {
      loading.value = false;
    }
  };

  const updateAltTxt = async () => {
    loading.value = true;
    const formData = {
      title: selectedImage.value.title,
      alt_text: selectedImage.value.alt_text
    }

    try {
      await axios.put(`/api/images/${selectedImage.value.id}`, formData);
    } catch (err) {
      error.value =
        err.response?.data?.message || "Error updating image alt text";
    } finally {
      loading.value = false;
    }
  };

  const fetchImage = async (imageId) => {
    loading.value = true;
    try {
      const response = await axios.get(`/api/images/${imageId}`);
      selectedImage.value = response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Error fetching image";
    } finally {
      loading.value = false;
    }
  };

  const selectImage = (image) => {
    selectedImage.value = image;
  };

  const fetchNiches = async () => {
    loading.value = true;
    try {
      const response = await axios.get("/api/niches");
      niches.value = response.data;
    } catch (err) {
      error.value =
        err.response?.data?.message || "Error fetching niches";
    } finally {
      loading.value = false;
    }
  };

  const clearImages = () => {
    images.value = []
  }

  // Getters
  const getSelectedImage = computed(() => selectedImage.value);
  const getSelectedLogo = computed(() => selectedLogo.value);

  // Return state, actions, and getters
  return {
    logo,
    selectedLogo,
    images,
    selectedImage,
    niches,
    loading,
    error,
    fetchLogo,
    uploadLogo,
    deleteLogo,
    fetchImagesByNiche,
    uploadImage,
    fetchImage,
    selectImage,
    fetchNiches,
    getSelectedImage,
    getSelectedLogo,
    updateAltTxt,
    clearImages,
  };
});
