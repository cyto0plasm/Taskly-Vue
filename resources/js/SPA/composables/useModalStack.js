import { ref, computed } from "vue";

const modalStack = ref([]); // ['task', 'project']

export function useModalStack() {
  function openModal(name) {
    console.log("openModal called with:", name);
  modalStack.value.push(name);
  console.log("modalStack now:", modalStack.value);
  }

  function closeModal() {
    modalStack.value.pop();
  }

  function closeAll() {
    modalStack.value = [];
  }

  const activeModal = computed(() => {
    return modalStack.value[modalStack.value.length - 1] ?? null;
  });

  return {
    modalStack,
    activeModal,
    openModal,
    closeModal,
    closeAll,
  };
}
