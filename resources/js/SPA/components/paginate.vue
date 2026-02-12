<script setup>
import { computed,  defineEmits } from "vue";

const props = defineProps({
  page: { type: Number, required: true },
  lastPage: { type: Number, required: true }
});

const emit = defineEmits(["change"]);

// UX control: how many pages around active
const SIBLINGS = 1;

const pages = computed(() => {
  const current = props.page;
  const last = props.lastPage;

  if (last <= 1) return [];

  const result = [];

  // 1️- Always show first page
  result.push(1);

  // 2️- Left dots
  if (current > 2 + SIBLINGS) {
    result.push("...");
  }

  // 3️- Middle pages
  const start = Math.max(2, current - SIBLINGS);
  const end = Math.min(last - 1, current + SIBLINGS);

  for (let i = start; i <= end; i++) {
    result.push(i);
  }

  // 4️- Right dots
  if (current < last - (1 + SIBLINGS)) {
    result.push("...");
  }

  // 5️- Always show last page
  result.push(last);

  return result;
});
</script>


<template>
  <!-- Previous -->
  <button
    class="pagination-button  mx-1 dark:bg-gray-800 dark:border-0 dark:text-white dark:hover:text-black "
    :disabled="page === 1"
    @click="emit('change', page - 1)"
    aria-label="Previous page"
  >
    ←
  </button>

  <!-- Pages -->
  <button
    v-for="p in pages"
    :key="p"
    class="pagination-row dark:bg-gray-800  dark:text-white"
    :disabled="p === '...' || p === page"
    @click="p !== '...' && emit('change', p)"
  >
    <span
      class="pagination-button dark:bg-gray-800 dark:hover:text-black dark:text-white  dark:border-0 "
      :class="{ active: p === page, dots: p === '...' }"
    >
      {{ p }}
    </span>
  </button>

  <!-- Next -->
  <button
    class="pagination-button mx-1 dark:bg-gray-800 dark:border-0 dark:text-white dark:hover:text-black"
    :disabled="page === lastPage"
    @click="emit('change', page + 1)"
    aria-label="Next page"
  >
    →
  </button>
</template>


<style>
.pagination-row {
    display: inline-block;
    margin: 0 2px;
}
.pagination-button {
    /* background-color: #f4f4f4; */
    /* border: 1px solid #e4e4e4; */
    transition: all 100ms ease-out;
    padding: 4px 10px;
    cursor: pointer;
    border-radius: 5px;

}
.pagination-button:hover {
    background-color: #e4e4e4;
}
.pagination-button:active {
    background-color: #d4d4d4;
}

.pagination-button.active {
  background-color: #11ba82;
  color: white;
  font-weight: bold;
  cursor: default;
}

.pagination-button.dots {
  cursor: default;
  background: transparent;
  border: none;
}

.pagination-row button[disabled] {
  cursor: default;
}
</style>
