import { defineStore } from "pinia";
import { ref, watch,computed } from "vue";

export const useLayoutStore = defineStore("layout", () => {
  const layouts = ref({
    tasks: {
      sections: {
        header: { visible: true, open: true, showHeaderBar: false },
        filters: { visible: false, open: false, showHeaderBar: false },
        tasklist: { visible: true, open: true, showHeaderBar: false },
      },
      detailsSections: {
        details: { visible: true },
        canvas: { visible: false },
      },
    },
    projects: {
      sections: {
        header: { visible: true, open: true, showHeaderBar: false },
        filters: { visible: false, open: false, showHeaderBar: false },
        projectlist: { visible: true, open: true, showHeaderBar: false },
      },
      detailsSections: {
        details: { visible: true },
        canvas: { visible: false },
      },
    },
  });

  const active = ref("tasks"); // default context

  function setActive(name) {
    if (layouts.value[name]) active.value = name;
  }

  const sections = computed(() => layouts.value[active.value].sections);
  const detailsSections = computed(() => layouts.value[active.value].detailsSections);

  function toggleSection(key) {
    if (sections.value[key]) sections.value[key].open = !sections.value[key].open;
  }

  function toggleVisibility(key) {
    if (sections.value[key]) sections.value[key].visible = !sections.value[key].visible;
  }

  function toggleHeaderBar(key) {
    if (sections.value[key]) sections.value[key].showHeaderBar =
      !sections.value[key].showHeaderBar;
  }

  function toggleDetailsVisibility(key) {
    if (detailsSections.value[key]) detailsSections.value[key].visible =
      !detailsSections.value[key].visible;
  }

  // persistence for each context
  const saved = localStorage.getItem("layouts");
  if (saved) layouts.value = JSON.parse(saved);

  watch(
    layouts,
    (v) => localStorage.setItem("layouts", JSON.stringify(v)),
    { deep: true }
  );

  return {
    layouts,
    active,
    sections,
    detailsSections,
    setActive,
    toggleSection,
    toggleVisibility,
    toggleHeaderBar,
    toggleDetailsVisibility,
  };
});
