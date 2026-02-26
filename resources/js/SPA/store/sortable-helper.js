import Sortable from "sortablejs";

export function initTaskSortable({
  el,
  tasks,
  store,
  softLoading,
  enabled,
} = {}) {
  if (!el || !softLoading || softLoading.value) return null;
  if (!enabled) return null;

  return new Sortable(el, {
    animation: 200,
    ghostClass: "bg-yellow-100",
    onEnd: async ({ oldIndex, newIndex }) => {
      if (
        oldIndex == null ||
        newIndex == null ||
        oldIndex === newIndex
      ) return;

      const moved = tasks.splice(oldIndex, 1)[0];
      tasks.splice(newIndex, 0, moved);

      const payload = tasks.map((t, idx) => ({
        id: t.id,
        position: idx + 1,
      }));

      await store.reorderTasks(payload);
    },
  });
}
export function initProjectSortable({
  el,
  projects,
  store,
  softLoading,
  enabled,
} = {}) {
  if (!el || !softLoading || softLoading.value) return null;
  if (!enabled) return null;

  return new Sortable(el, {
    animation: 200,
    ghostClass: "bg-yellow-100",
    onEnd: async ({ oldIndex, newIndex }) => {
      if (
        oldIndex == null ||
        newIndex == null ||
        oldIndex === newIndex
      ) return;

      const moved = projects.splice(oldIndex, 1)[0];
      projects.splice(newIndex, 0, moved);

      const payload = tasks.map((t, idx) => ({
        id: t.id,
        position: idx + 1,
      }));

      await store.reorderPorjects(payload);
    },
  });
}


export function initLayoutSortable(wrapper) {
  if (!wrapper) return null;

  const sortable = new Sortable(wrapper, {
    animation: 200,
    ghostClass: "bg-yellow-100",
    handle: ".draggable-handle",
    onEnd: () => {
      const order = Array.from(wrapper.children).map(
        el => el.dataset.layoutId
      );
      localStorage.setItem("layoutOrder", JSON.stringify(order));
    },
  });

  // restore order
  const saved = localStorage.getItem("layoutOrder");
  if (saved) {
    JSON.parse(saved).forEach(id => {
      const el = Array.from(wrapper.children)
        .find(c => c.dataset.layoutId === id);
      if (el) wrapper.appendChild(el);
    });
  }

  return sortable;
}
