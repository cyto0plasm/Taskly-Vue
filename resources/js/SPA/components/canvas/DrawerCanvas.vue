<script setup>
import { ref, watch, onMounted, onUnmounted, computed, nextTick } from "vue";
import { useCanvas, SHAPES, FONTS } from "../../composables/useCanvas.js";
import { useDrawingStore } from "../../store/drawing-store.js";
import { useTaskStore }    from "../../store/task-store.js";
import { useProjectStore } from "../../store/project-store.js";
import { useLayoutStore }  from "../../store/layout-store.js";

const props = defineProps({
    type: { type: String, default: 'task' },   // 'task' | 'project'
    entityId: { type: [Number, String], default: null },
});

const drawingStore = useDrawingStore();
const taskStore    = useTaskStore();
const projectStore = useProjectStore();
const layoutStore  = useLayoutStore();
const canvas       = useCanvas();

const canvasElRef   = ref(null);
const containerRef  = ref(null);
const gridCanvasRef = ref(null);

// Flyout state
const showShapePicker = ref(false);
const showBrushPicker = ref(false);
const showColorPicker = ref(false);
const shapeBtnRef     = ref(null);
const brushBtnRef     = ref(null);
const colorBtnRef     = ref(null);
const shapeFlyPos     = ref({ top: 0, left: 0 });
const brushFlyPos     = ref({ top: 0, left: 0 });
const colorFlyPos     = ref({ top: 0, left: 0 });

// Computed
const selectedId = computed(() => {
    if (props.entityId) return props.entityId;
    return props.type === 'project'
        ? projectStore.selectedProjectId
        : taskStore.selectedTaskId;
});
const entityType = computed(() => props.type);
const canvasVisible = computed(() => {
    const key = entityType.value === 'project' ? 'projects' : 'tasks';
    return layoutStore.layouts?.[key]?.detailsSections?.canvas?.visible ?? false;
});
const currentShape   = computed(() => SHAPES.find(s => s.key === canvas.activeShape.value) ?? SHAPES[0]);
const dotSize        = computed(() => Math.min(20, Math.max(4, canvas.lineWidth.value * 0.5)) + "px");
const canvasInitialized = ref(false);
const isCanvasReady = computed(() => canvasInitialized.value && !!canvas.getFabricInstance?.());
const toolLabel = computed(() => ({
  pen: "Pen  [D]", eraser: "Eraser  [E]", select: "Select  [V]",
  connector: "Connector", text: "Text  [T]",
})[canvas.tool.value] ?? "");
const canvasCursor = computed(() => canvas.isPanning.value ? "grabbing" : "default");

// Grid drawing
function redrawGrid() {
  if (gridCanvasRef.value) canvas.drawGrid(gridCanvasRef.value);
}

// Lifecycle
let resizeObserver, cleanupKeyboard, fabricRenderOff;

onMounted(async () => {
  if (!canvasElRef.value || !containerRef.value) return;

  const result = canvas.init(canvasElRef.value, containerRef.value);
  if (!result) return;

  await nextTick();
  canvasInitialized.value = true;

  cleanupKeyboard = canvas.setupKeyboard({
    onSave: saveDrawing,
    onOpenColor: () => openFlyout("color"),
    onOpenSize: () => openFlyout("brush"),
  });

  fabricRenderOff = canvas._fc_onAfterRender(redrawGrid);

  if (gridCanvasRef.value && containerRef.value) {
    gridCanvasRef.value.width = containerRef.value.clientWidth;
    gridCanvasRef.value.height = containerRef.value.clientHeight;
  }
  redrawGrid();

  resizeObserver = new ResizeObserver(([entry]) => {
    const { width, height } = entry.contentRect;
    canvas.resize(width, height);
    if (gridCanvasRef.value) {
      gridCanvasRef.value.width = width;
      gridCanvasRef.value.height = height;
      redrawGrid();
    }
  });
  resizeObserver.observe(containerRef.value);

  document.addEventListener("click", closeAllFlyouts);
  canvas.value?._fc?.renderAll();
});

onUnmounted(() => {
  resizeObserver?.disconnect();
  fabricRenderOff?.();
  canvas.dispose();
  cleanupKeyboard?.();
  document.removeEventListener("click", closeAllFlyouts);
});

// Load drawing when conditions are met
async function tryLoadDrawing() {
  if (selectedId.value && canvasVisible.value && isCanvasReady.value) {
    await loadDrawing();
  }
}

watch(selectedId, tryLoadDrawing);
watch(canvasVisible, tryLoadDrawing);
watch(isCanvasReady, (ready) => { if (ready) tryLoadDrawing(); });

// Load / Save
async function loadDrawing() {
  const id = selectedId.value;
  if (!id) return;

  try {
    const raw = await drawingStore.loadDrawing(props.type, id);
    if (!raw) {
      canvas.clear();
      return;
    }

    let drawingData = raw;
    if (typeof raw === 'string') drawingData = JSON.parse(raw);
    else if (raw?.data) drawingData = typeof raw.data === 'string' ? JSON.parse(raw.data) : raw.data;

    const canvasData = drawingData.canvas || drawingData;
    canvas.clear();
    await canvas.setData(canvasData);
  } catch (err) {
    console.error("Failed to load drawing:", err);
  }
}

async function saveDrawing() {
  const id = selectedId.value;
  const data = canvas.getData();
  if (!id || !data) return;
  await drawingStore.saveDrawing({ type: props.type, id, data });
}

// Flyouts
function closeAllFlyouts() {
  showShapePicker.value = false;
  showBrushPicker.value = false;
  showColorPicker.value = false;
}

function openFlyout(name, e) {
  e?.stopPropagation();
  const refMap  = { shape: shapeBtnRef, brush: brushBtnRef, color: colorBtnRef };
  const posMap  = { shape: shapeFlyPos, brush: brushFlyPos, color: colorFlyPos };
  const showMap = { shape: showShapePicker, brush: showBrushPicker, color: showColorPicker };
  const btnEl = refMap[name]?.value;
  if (btnEl) {
    const r = btnEl.getBoundingClientRect();
    posMap[name].value = { top: r.top, left: r.right + 10 };
  }
  const wasOpen = showMap[name].value;
  closeAllFlyouts();
  if (!wasOpen) showMap[name].value = true;
}

const openShapePicker = e => openFlyout("shape", e);
const openBrushPicker = e => openFlyout("brush", e);
const openColorPicker = e => openFlyout("color", e);

function pickShape(key) {
  canvas.activeShape.value = key;
  showShapePicker.value = false;
  canvas.addShape(key);
}

// Style helpers (kept as is for template readability)
const btn = (active = false, danger = false) => [
  "flex items-center justify-center w-9 h-9 rounded-xl transition-all duration-100 cursor-pointer",
  "hover:scale-105 active:scale-95 select-none",
  active  ? "bg-blue-500/15 text-blue-600 ring-1 ring-blue-500/30"
          : "text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700",
  danger && !active ? "hover:!bg-red-50 hover:!text-red-500" : "",
].join(" ");

const zoomBtn = () =>
  "flex items-center justify-center w-8 h-8 rounded-lg bg-white/80 backdrop-blur-sm border border-zinc-200 text-zinc-500 hover:bg-white hover:text-zinc-700 shadow-sm transition-all duration-100 cursor-pointer select-none";

const tbBtn = (active = false) => [
  "flex items-center justify-center h-7 min-w-[28px] px-1.5 rounded-lg text-xs font-medium",
  "transition-all duration-75 cursor-pointer select-none",
  active ? "bg-zinc-800 text-white" : "text-zinc-600 hover:bg-zinc-100",
].join(" ");

// Watch grid appearance
watch([() => canvas.gridVisible.value, () => canvas.canvasDark.value], redrawGrid);
</script>

<template>
  <div class="flex w-full h-full min-h-0 overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50">
    <!-- Floating text toolbar -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 translate-y-1 scale-95"
        leave-active-class="transition duration-100 ease-in"
        leave-to-class="opacity-0 translate-y-1 scale-95"
      >
        <div
          v-if="canvas.toolbar.value.visible && canvas.editingText.value"
          class="fixed z-[9999] flex items-center gap-1 px-2 py-1.5 bg-white border border-zinc-200 rounded-2xl shadow-xl shadow-black/10 -translate-x-1/2 pointer-events-auto"
          :style="{ top: canvas.toolbar.value.top + 'px', left: canvas.toolbar.value.left + 'px' }"
          @mousedown.prevent
        >
          <!-- Font family -->
          <div class="relative">
            <button
              class="flex items-center gap-1 h-7 px-2 rounded-lg text-xs text-zinc-700 hover:bg-zinc-100 transition-colors min-w-[96px] justify-between"
              @click.stop="canvas.showFonts.value = !canvas.showFonts.value"
            >
              <span class="truncate max-w-[72px]" :style="{ fontFamily: canvas.txtFont.value }">{{ canvas.txtFont.value }}</span>
              <span class="text-[9px] text-zinc-400 shrink-0">{{ canvas.showFonts.value ? "▲" : "▼" }}</span>
            </button>
            <Transition
              enter-active-class="transition duration-100 ease-out"
              enter-from-class="opacity-0 -translate-y-1"
              leave-active-class="transition duration-75 ease-in"
              leave-to-class="opacity-0 -translate-y-1"
            >
              <div v-if="canvas.showFonts.value" class="absolute bottom-full mb-2 left-0 z-10 bg-white border border-zinc-200 rounded-xl shadow-xl py-1 min-w-[155px] max-h-52 overflow-y-auto">
                <button
                  v-for="font in FONTS" :key="font"
                  class="w-full text-left px-3 py-1.5 text-sm hover:bg-zinc-50 transition-colors"
                  :style="{ fontFamily: font }"
                  :class="font === canvas.txtFont.value ? 'text-zinc-900 font-semibold bg-zinc-50' : 'text-zinc-600'"
                  @click.stop="canvas.setFont(font)"
                >{{ font }}</button>
              </div>
            </Transition>
          </div>
          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0" />
          <div class="flex items-center gap-0.5">
            <button :class="tbBtn()" @click="canvas.changeSize(-1)">−</button>
            <span class="text-xs font-mono w-7 text-center tabular-nums text-zinc-700 select-none">{{ canvas.txtSize.value }}</span>
            <button :class="tbBtn()" @click="canvas.changeSize(1)">+</button>
          </div>
          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0" />
          <button :class="tbBtn(canvas.txtBold.value)"   class="!font-bold !text-sm"  @click="canvas.toggleBold">B</button>
          <button :class="tbBtn(canvas.txtItalic.value)" class="!italic !text-sm"     @click="canvas.toggleItalic">I</button>
          <button :class="[tbBtn(canvas.txtUnder.value), '!underline !text-sm']"       @click="canvas.toggleUnder">U</button>
          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0" />
          <button
            v-for="(rects, align) in { left: [[0,0.5,14,1.5],[0,3.5,9,1.5],[0,6.5,14,1.5],[0,9.5,7,1.5]], center: [[0,0.5,14,1.5],[2.5,3.5,9,1.5],[0,6.5,14,1.5],[3.5,9.5,7,1.5]], right: [[0,0.5,14,1.5],[5,3.5,9,1.5],[0,6.5,14,1.5],[7,9.5,7,1.5]] }"
            :key="align" :class="tbBtn(canvas.txtAlign.value === align)" :title="align"
            @click="canvas.setAlign(align)"
          >
            <svg width="14" height="12" viewBox="0 0 14 12" fill="none"><rect v-for="([x,y,w,h],i) in rects" :key="i" :x="x" :y="y" :width="w" :height="h" rx=".75" fill="currentColor"/></svg>
          </button>
          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0" />
          <label class="relative flex items-center justify-center w-7 h-7 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer" title="Text color">
            <span class="text-sm font-bold select-none leading-none" :style="{ color: canvas.txtColor.value }">A</span>
            <input type="color" :value="canvas.txtColor.value" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer" @input="canvas.applyTxtColor" />
          </label>
        </div>
      </Transition>
    </Teleport>

    <!-- Sidebar -->
    <aside class="flex flex-col items-center w-14 shrink-0 h-full py-3 gap-0.5 border-r border-zinc-200 bg-white rounded-l-xl overflow-y-auto overflow-x-visible">
      <button :class="btn(canvas.canvasDark.value)" title="Toggle theme" @click="canvas.canvasDark.value = !canvas.canvasDark.value">
        <span class="text-base">{{ canvas.canvasDark.value ? "☀️" : "🌙" }}</span>
      </button>
      <button :class="btn(canvas.gridVisible.value)" title="Toggle grid" @click="canvas.gridVisible.value = !canvas.gridVisible.value">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      </button>
      <div class="w-8 h-px my-1 bg-zinc-100 shrink-0" />
      <button :class="btn(canvas.tool.value === 'pen')" title="Pen  [D]" @click="canvas.setTool('pen')">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>
      </button>
      <button :class="btn(canvas.tool.value === 'eraser')" title="Eraser  [E]" @click="canvas.setTool('eraser')">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 20H7L3 16l10-10 7 7-3 4M6.0001 10.0001l8 8"/></svg>
      </button>
      <div class="w-8 h-px my-1.5 bg-zinc-100 shrink-0" />
      <div class="relative shrink-0 my-0.5" title="Stroke / fill colour  [C]">
        <div ref="colorBtnRef" class="w-7 h-7 rounded-lg border-2 border-zinc-200 cursor-pointer shadow-inner overflow-hidden hover:scale-105 transition-transform" :style="{ backgroundColor: canvas.strokeColor.value }" @click.stop="openColorPicker" />
      </div>
      <Teleport to="body">
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 -translate-x-1 scale-95" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0 -translate-x-1 scale-95">
          <div v-if="showColorPicker" class="fixed z-[9999] flex flex-col items-center gap-3 px-4 py-4 bg-white border border-zinc-200 rounded-2xl shadow-xl" :style="{ top: colorFlyPos.top + 'px', left: colorFlyPos.left + 'px' }" @click.stop>
            <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider self-start">Colour  [C]</p>
            <input type="color" v-model="canvas.strokeColor.value" class="w-32 h-32 rounded-xl cursor-pointer border-0 p-0" style="appearance:none;" />
            <div class="grid grid-cols-6 gap-1.5">
              <div v-for="c in ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4','#f97316','#84cc16','#6b7280','#1f2937','#ffffff']" :key="c" class="w-6 h-6 rounded-lg cursor-pointer border border-zinc-200/50 hover:scale-110 transition-transform shadow-sm" :style="{ backgroundColor: c, outline: canvas.strokeColor.value === c ? '2px solid #3b82f6' : 'none', outlineOffset: '2px' }" @click="canvas.strokeColor.value = c" />
            </div>
          </div>
        </Transition>
      </Teleport>
      <button ref="brushBtnRef" :class="btn(showBrushPicker)" title="Brush size  [S]" @click.stop="openBrushPicker">
        <div class="rounded-full bg-current transition-all duration-75" :style="{ width: dotSize, height: dotSize }" />
      </button>
      <Teleport to="body">
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 -translate-x-1 scale-95" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0 -translate-x-1 scale-95">
          <div v-if="showBrushPicker" class="fixed z-[9999] flex flex-col items-center gap-2 px-3 py-3 bg-white border border-zinc-200 rounded-2xl shadow-xl" :style="{ top: brushFlyPos.top + 'px', left: brushFlyPos.left + 'px' }" @click.stop>
            <p class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">Size  [S]</p>
            <div class="w-6 h-6 flex items-center justify-center">
              <div class="rounded-full transition-all" :style="{ width: Math.min(22,Math.max(3,canvas.lineWidth.value*0.55))+'px', height: Math.min(22,Math.max(3,canvas.lineWidth.value*0.55))+'px', backgroundColor: canvas.tool.value==='eraser'?'#d4d4d8':canvas.strokeColor.value }" />
            </div>
            <input type="range" v-model.number="canvas.lineWidth.value" min="1" max="50" class="cursor-pointer accent-blue-500" style="writing-mode:vertical-lr;direction:rtl;width:4px;height:100px;" />
            <span class="text-[10px] font-mono tabular-nums text-zinc-400 select-none">{{ canvas.lineWidth.value }}</span>
          </div>
        </Transition>
      </Teleport>
      <div class="w-8 h-px my-1.5 bg-zinc-100 shrink-0" />
      <button :class="btn(canvas.tool.value === 'select')" title="Select  [V]" @click="canvas.setTool('select')">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4l7.07 17 2.51-7.39L21 11.07z"/></svg>
      </button>
      <button :class="btn(canvas.tool.value === 'connector')" title="Connect shapes" @click="canvas.setTool('connector')">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m15 16 4-4-4-4"/><circle cx="5" cy="12" r="2" fill="currentColor" stroke="none" class="opacity-60"/></svg>
      </button>
      <div class="w-8 h-px my-1.5 bg-zinc-100 shrink-0" />
      <div class="relative flex flex-col items-center shrink-0">
        <button :class="btn()" :title="`Add ${currentShape.label}`" @click="canvas.addShape(canvas.activeShape.value)">
          <span class="text-base leading-none">{{ currentShape.icon }}</span>
        </button>
        <button ref="shapeBtnRef" class="text-[9px] px-1 py-0.5 rounded leading-none text-zinc-400 hover:text-zinc-600 transition-colors cursor-pointer" title="More shapes" @click.stop="openShapePicker">
          {{ showShapePicker ? "▲" : "▼" }}
        </button>
      </div>
      <Teleport to="body">
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 -translate-x-1 scale-95" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0 -translate-x-1 scale-95">
          <div v-if="showShapePicker" class="fixed z-[9999] bg-white border border-zinc-200 rounded-2xl shadow-xl p-2 flex flex-col gap-1" :style="{ top: shapeFlyPos.top+'px', left: shapeFlyPos.left+'px' }" @click.stop>
            <div v-for="s in SHAPES" :key="s.key" class="flex items-center gap-2 px-3 py-1.5 rounded-lg cursor-pointer hover:bg-zinc-50 transition-colors" :class="canvas.activeShape.value === s.key ? 'bg-blue-50 text-blue-600' : 'text-zinc-700'" @click="pickShape(s.key)">
              <span class="text-base w-5 text-center leading-none">{{ s.icon }}</span>
              <span class="text-xs whitespace-nowrap">{{ s.label }}</span>
            </div>
          </div>
        </Transition>
      </Teleport>
      <button :class="btn(canvas.tool.value === 'text')" title="Add text box  [T]" @click="canvas.addText()">
        <span class="text-[15px] font-bold leading-none" style="font-family:serif">T</span>
      </button>
      <div class="w-8 h-px my-1.5 bg-zinc-100 shrink-0" />
      <button :class="btn()" title="Group / Ungroup  [Ctrl+G]" @click="canvas.groupOrUngroup()">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="8" height="8" rx="1"/><rect x="14" y="2" width="8" height="8" rx="1"/><rect x="2" y="14" width="8" height="8" rx="1"/><rect x="14" y="14" width="8" height="8" rx="1"/></svg>
      </button>
      <div class="w-8 h-px my-1.5 bg-zinc-100 shrink-0" />
      <button :class="btn()" title="Undo  [Ctrl+Z]" @click="canvas.undo()">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>
      </button>
      <button :class="btn()" title="Redo  [Ctrl+Shift+Z]" @click="canvas.redo()">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3L21 13"/></svg>
      </button>
      <button :class="btn(false, true)" title="Clear canvas" @click="canvas.clear()">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
      </button>
      <div class="w-8 h-px my-1.5 bg-zinc-100 shrink-0" />
      <button :class="btn()" :title="drawingStore.saving ? 'Saving…' : 'Save  [Ctrl+S]'" :disabled="drawingStore.saving" @click="saveDrawing">
        <svg v-if="!drawingStore.saving" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <svg v-else class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-dasharray="31.4" stroke-dashoffset="10" stroke-linecap="round"/></svg>
      </button>
      <button :class="btn()" title="Export PNG  [Ctrl+P]" @click="canvas.exportPNG()">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      </button>
    </aside>

    <!-- Canvas area -->
    <div ref="containerRef" class="flex-1 min-w-0 h-full relative overflow-hidden">
      <div class="absolute inset-0 transition-colors duration-300" :style="{ backgroundColor: canvas.bgColor.value }" />
      <canvas ref="gridCanvasRef" class="absolute inset-0 z-[1] pointer-events-none" style="display:block; width:100%; height:100%;" />
      <div v-if="drawingStore.loading" class="absolute inset-0 z-30 flex items-center justify-center bg-white/60 backdrop-blur-sm">
        <svg class="w-8 h-8 animate-spin text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-dasharray="31.4" stroke-dashoffset="10" stroke-linecap="round"/></svg>
      </div>
      <div v-if="toolLabel" class="absolute bottom-3 left-3 z-10 pointer-events-none text-[10px] font-medium text-zinc-400 bg-white/80 backdrop-blur-sm px-2 py-1 rounded-full border border-zinc-200 select-none">{{ toolLabel }}</div>
      <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10 pointer-events-none text-[10px] text-zinc-400 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full border border-zinc-200 select-none whitespace-nowrap">
        <template v-if="canvas.isPanning.value">Panning — release Ctrl to stop</template>
        <template v-else-if="canvas.tool.value === 'connector'">Click shape anchor → drag → release on another shape</template>
        <template v-else-if="canvas.tool.value === 'text'">Click canvas to place text</template>
        <template v-else>Ctrl+drag to pan · Shift+click multi-select · Ctrl+A select all · Del to remove</template>
      </div>
      <div class="absolute bottom-12 right-4 z-10 flex flex-col gap-1">
        <button :class="zoomBtn()" title="Zoom in" @click="canvas.zoomIn()"><span class="text-sm font-bold leading-none">+</span></button>
        <button :class="zoomBtn()" title="Zoom out" @click="canvas.zoomOut()"><span class="text-sm font-bold leading-none">−</span></button>
        <button :class="zoomBtn()" title="Reset zoom" @click="canvas.zoomReset()"><span class="text-[10px] font-mono leading-none">1:1</span></button>
      </div>
      <canvas ref="canvasElRef" class="absolute inset-0 z-[2]" :style="{ display: 'block', width: '100%', height: '100%', cursor: canvasCursor }" />
    </div>
  </div>
</template>
