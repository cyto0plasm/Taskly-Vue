<script setup>
import { ref, reactive, onMounted, nextTick } from "vue";

const props = defineProps({
  active: { type: Boolean, default: true }
});

const step = ref(0);
const canvas = ref(null);

const steps = [
  {
    target: '[data-layout-id="tasklist"]',
    text: "This is where all your tasks live",
    zoom: 1.1
  },
  {
    target: '#sortable-list',
    text: "Scroll, paginate or reorder tasks",
    zoom: 1.25
  },
  {
    target: '#taskDetailContent',
    text: "Selected task details appear here",
    zoom: 1.15
  },
  {
    target: '#status',
    text: "Track task status clearly",
    zoom: 1.4
  },
  {
    target: '#task-description',
    text: "Read full task description",
    zoom: 1.35
  },
  {
    target: '#task-edit-btn',
    text: "Edit task anytime",
    zoom: 1.45
  }
];

const spotlight = reactive({ x: 0, y: 0, w: 0, h: 0 });
const transform = ref("");

function focusStep() {
  const s = steps[step.value];
  const el = document.querySelector(s.target);
  if (!el) return;

  const rect = el.getBoundingClientRect();

  spotlight.x = rect.left - 12;
  spotlight.y = rect.top - 12;
  spotlight.w = rect.width + 24;
  spotlight.h = rect.height + 24;

  const cx = rect.left + rect.width / 2;
  const cy = rect.top + rect.height / 2;

  transform.value = `
    translate(calc(50vw - ${cx}px), calc(50vh - ${cy}px))
    scale(${s.zoom})
  `;
}

function next() {
  step.value++;
  if (step.value >= steps.length) end();
  else nextTick(focusStep);
}

function end() {
  step.value = 0;
  transform.value = "";
}

onMounted(() => nextTick(focusStep));
</script>

<template>
  <!-- Overlay -->
  <div v-if="props.active" class="demo-overlay">
    <div class="spotlight" :style="{
      left: spotlight.x + 'px',
      top: spotlight.y + 'px',
      width: spotlight.w + 'px',
      height: spotlight.h + 'px'
    }"></div>

    <div class="caption">
      {{ steps[step]?.text }}
      <button @click="next">Next</button>
      <button @click="end">Skip</button>
    </div>
  </div>

  <!-- Camera -->
  <div ref="canvas" class="demo-canvas" :style="{ transform }">
    <slot />
  </div>
</template>

<style scoped>
.demo-canvas {
  transition: transform 700ms cubic-bezier(.22,.61,.36,1);
  transform-origin: center;
}

.demo-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  pointer-events: none;
}

.spotlight {
  position: absolute;
  border-radius: 14px;
  box-shadow:
    0 0 0 9999px rgba(0,0,0,.65),
    0 0 0 2px white;
  transition: all 600ms ease;
}

.caption {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  background: black;
  color: white;
  padding: 12px 16px;
  border-radius: 12px;
  display: flex;
  gap: 12px;
  pointer-events: auto;
}

.caption button {
  background: white;
  color: black;
  border-radius: 6px;
  padding: 4px 10px;
  border: none;
  cursor: pointer;
}
</style>
