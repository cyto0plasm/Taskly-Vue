<script setup>
import { onMounted, onBeforeUnmount,computed, watch } from "vue";

const props = defineProps({
  show: { type: Boolean, required: true },
  title: { type: String, default: "" },
  description: { type: String, default: "" },
  maxWidth: { type: String, default: "max-w-xl" },
  color:{type:String,default:"#10B981"}
});
// Compute gradient style dynamically
const gradientStyle = computed(() => ({
  background: `linear-gradient(to right, ${props.color}, ${shadeColor(props.color, 20)})`,
  color: "white",
  padding: "1rem 1.5rem",
}));

// Lighten a hex color by a percentage (handles # prefix)
function shadeColor(hexColor, percent) {
  const color = hexColor.startsWith("#") ? hexColor.slice(1) : hexColor;
  const num = parseInt(color, 16);
  const r = Math.min(255, ((num >> 16) & 0xFF) + percent);
  const g = Math.min(255, ((num >> 8) & 0xFF) + percent);
  const b = Math.min(255, (num & 0xFF) + percent);
  return "#" + ((r << 16) | (g << 8) | b).toString(16).padStart(6, "0");
}

const emit = defineEmits(["close"]);

function onEsc(e) {
  if (e.key === "Escape") emit("close");
}
watch(
  () => props.show,
  (val) => {
    if (val) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
  },
  { immediate: true }
);
onBeforeUnmount(() => {
  document.body.style.overflow = "";
});
onMounted(() => document.addEventListener("keydown", onEsc));
onBeforeUnmount(() => document.removeEventListener("keydown", onEsc));
</script>

<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-start justify-center  p-3 sm:p-4"
      role="dialog"
      aria-modal="true"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        @click="emit('close')"
      ></div>

      <!-- Modal - ADD max-h-[85vh] HERE TOO -->
      <div :class="['relative w-full mx-auto h-screen sm:h-[92vh] flex flex-col', props.maxWidth]">
        <div class="gradient-border flex flex-col rounded-2xl flex-1 min-h-0">
          <div
            class="flex flex-col lg:flex-row bg-[#f5f5f5] rounded-2xl flex-1 min-h-0 overflow-hidden "
          >
            <!-- MAIN - Already has min-h-0 ✓ -->
            <div class="flex-1 flex flex-col min-w-0 min-h-0">
              <!-- Header -->
              <div
                :style="gradientStyle"
                class="text-white px-4 sm:px-6 py-4 shrink-0"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="min-w-0">
                    <slot name="header">
                      <h2 class="text-xl sm:text-2xl font-bold truncate">
                        {{ title }}
                      </h2>
                      <p
                        v-if="description"
                        class="text-xs sm:text-sm text-white/90 mt-1"
                      >
                        {{ description }}
                      </p>
                    </slot>
                  </div>

                  <button
                    @click="emit('close')"
                    class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-1.5 shrink-0"
                    aria-label="Close modal"
                  >
                    ✕
                  </button>
                </div>
              </div>

              <!-- Content - SCROLLABLE -->
              <div class="dark:bg-[#2A2A2A] dark:text-white flex-1 overflow-y-auto px-4 sm:px-6 py-6 min-h-0 flex flex-col gap-2  ">
                <slot></slot>
              </div>

              <!-- Footer - FIXED AT BOTTOM -->
              <div v-if="$slots.footer" class="dark:bg-[#2A2A2A] dark:text-white px-4 sm:px-6 py-4 border-t border-gray-300 dark:border-gray-500 shrink-0 bg-[#f5f5f5]">
                <slot name="footer"></slot>
              </div>
            </div>

            <!-- Sidebar -->
            <div v-if="$slots.sidebar" class="hidden lg:flex w-64 bg-white shrink-0">
              <slot name="sidebar"></slot>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

</style>
