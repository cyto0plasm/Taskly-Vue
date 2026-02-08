import { defineStore } from "pinia";
import { ref, watch } from "vue";

export const useLayoutStore = defineStore("layout", () => {
  const sections = ref({
    header:   { visible: true,  open: true,  showHeaderBar: false },
    filters:  { visible: false, open: false, showHeaderBar: false },
    tasklist: { visible: true,  open: true,  showHeaderBar: false },
  });

  function toggleSection(key) {
    if (sections.value[key]) {
      sections.value[key].open = !sections.value[key].open;
    }
  }

  function toggleVisibility(key) {
    if (sections.value[key]) {
      sections.value[key].visible = !sections.value[key].visible;
    }
  }

  function toggleHeaderBar(key) {
    if (sections.value[key]) {
      sections.value[key].showHeaderBar =
        !sections.value[key].showHeaderBar;
    }
  }

  // persistence
  const saved = localStorage.getItem("sections");
  if (saved) sections.value = JSON.parse(saved);

  watch(
    sections,
    v => localStorage.setItem("sections", JSON.stringify(v)),
    { deep: true }
  );

  return {
    sections,
    toggleSection,
    toggleVisibility,
    toggleHeaderBar,
  };
});
