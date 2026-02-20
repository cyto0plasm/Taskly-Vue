<script setup>
import { onMounted, onUnmounted, ref, watch, nextTick } from "vue";
import { Canvas, PencilBrush, Rect, Circle, Triangle, Ellipse, Textbox, Group, ActiveSelection } from "fabric";
import Shape from "./svg/shape.vue";

/* ═══════════════════ STATE ═══════════════════ */
const canvasRef = ref(null);
const containerRef = ref(null);
const strokeColor = ref("#000000");
const lineWidth = ref(5);
const tool = ref("pen");
const canvasDark = ref(false);
const activeShape = ref("rect");
const shapeBtnRef = ref(null);
const showShapes = ref(false);
const flyoutPos = ref({ top: 0, left: 0 });

// Brush size flyout
const brushBtnRef = ref(null);
const showBrushSize = ref(false);
const brushFlyoutPos = ref({ top: 0, left: 0 });

// Text toolbar
const editingText = ref(null);
const toolbar = ref({ top: 0, left: 0, visible: false });
const showFonts = ref(false);
const txtFont = ref("sans-serif");
const txtSize = ref(16);
const txtBold = ref(false);
const txtItalic = ref(false);
const txtUnder = ref(false);
const txtAlign = ref("center");
const txtColor = ref("#000000");

const FONTS = [
  "sans-serif", "serif", "monospace", "Georgia", "Arial",
  "Verdana", "Courier New", "Times New Roman", "Trebuchet MS", "Impact",
];

const shapes = ["rect", "circle", "triangle", "ellipse"];
const shapeIcons = { rect: "▭", circle: "○", triangle: "△", ellipse: "⬯" };

let fc = null; // fabricCanvas
let ro = null; // resizeObserver

/* ═══════════════════ LIFECYCLE ═══════════════════ */
onMounted(() => {
  const container = containerRef.value;
  if (!container) return;

  fc = new Canvas(canvasRef.value, {
    isDrawingMode: true,
    width: container.clientWidth,
    height: container.clientHeight,
    backgroundColor: "#ffffff",
  });

  updateBrush();
  loadDrawing();
  document.addEventListener("click", onDocClick);

  fc.on("text:editing:entered", ({ target }) => {
    editingText.value = target;
    syncTextProps(target);
    placeToolbar(target);
  });

  fc.on("text:editing:exited", () => {
    editingText.value = null;
    toolbar.value.visible = false;
    showFonts.value = false;
    fc.renderAll();
  });

  fc.on("object:moving", () => editingText.value && placeToolbar(editingText.value));
  fc.on("after:render", () => editingText.value && placeToolbar(editingText.value));

  ro = new ResizeObserver(([entry]) => {
    if (!fc) return;
    const { width, height } = entry.contentRect;
    fc.setDimensions({ width, height });
    fc.renderAll();
  });
  ro.observe(container);
});

onUnmounted(() => {
  ro?.disconnect();
  fc?.dispose();
  document.removeEventListener("click", onDocClick);
});

/* ═══════════════════ WATCHERS ═══════════════════ */
watch([strokeColor, lineWidth, tool], () => {
  updateBrush();
});

watch(canvasDark, (dark) => {
  if (!fc) return;
  fc.backgroundColor = dark ? "#18181b" : "#ffffff";
  fc.renderAll();
  if (tool.value === "eraser") updateBrush();
});

/* ═══════════════════ BRUSH ═══════════════════ */
function updateBrush() {
  if (!fc) return;
  const brush = new PencilBrush(fc);
  brush.color = tool.value === "eraser"
    ? (canvasDark.value ? "#18181b" : "#ffffff")
    : strokeColor.value;
  brush.width = tool.value === "eraser" ? lineWidth.value * 1.5 : lineWidth.value;
  fc.freeDrawingBrush = brush;
  fc.isDrawingMode = tool.value === "pen" || tool.value === "eraser";
}

function setTool(t) {
  tool.value = t;
  if (!fc) return;
  if (t === "pen" || t === "eraser") {
    fc.isDrawingMode = true;
    fc.discardActiveObject();
  } else {
    fc.isDrawingMode = false;
    fc.selection = true;
    fc.getObjects().forEach(o => o.set({ selectable: true, evented: true }));
  }
  fc.renderAll();
}

/* ═══════════════════ SHAPES ═══════════════════ */
function addFigure(type) {
  if (!fc) return;
  activeShape.value = type;
  showShapes.value = false;
  setTool("select");

  const base = {
    originX: "center",
    originY: "center",
    left: 0,
    top: 0,
    fill: strokeColor.value + "33",
    stroke: strokeColor.value,
    strokeWidth: 2,
  };

  const shapeMap = {
    rect: () => new Rect({ ...base, width: 160, height: 100 }),
    circle: () => new Circle({ ...base, radius: 60 }),
    triangle: () => new Triangle({ ...base, width: 130, height: 120 }),
    ellipse: () => new Ellipse({ ...base, rx: 90, ry: 55 }),
  };

  const shapeObj = shapeMap[type]?.();
  if (!shapeObj) return;

  const textWidths = { rect: 130, circle: 90, triangle: 90, ellipse: 150 };
  const tb = new Textbox("", {
    originX: "center",
    originY: "center",
    left: 0,
    top: 0,
    width: textWidths[type] ?? 120,
    fontSize: 16,
    fontFamily: "sans-serif",
    fill: strokeColor.value,
    textAlign: "center",
    editable: true,
  });

  const group = new Group([shapeObj, tb], {
    left: 180,
    top: 160,
    interactive: true,
    subTargetCheck: true,
  });

  fc.add(group);
  fc.setActiveObject(group);
  fc.renderAll();
}

function addTextBox() {
  if (!fc) return;
  setTool("select");
  const tb = new Textbox("Text", {
    left: 160,
    top: 160,
    width: 200,
    fontSize: 20,
    fontFamily: "sans-serif",
    fill: strokeColor.value,
    textAlign: "left",
    editable: true,
  });
  fc.add(tb);
  fc.setActiveObject(tb);
  tb.enterEditing();
  fc.renderAll();
}

/* ═══════════════════ GROUP / UNGROUP ═══════════════════ */
function groupOrUngroup() {
  if (!fc) return;
  const active = fc.getActiveObject();
  if (!active) return;

  if (active.type === "activeselection" || active.type === "activeSelection") {
    const items = [...active.getObjects()];
    const group = new Group(items, {
      canvas: fc,
      interactive: false
    });
    fc.discardActiveObject();
    items.forEach(item => fc.remove(item));
    fc.add(group);
    fc.setActiveObject(group);
  }
  else if (active.type === "group") {
    const items = active.getObjects();
    const groupPos = {
      left: active.left,
      top: active.top,
      angle: active.angle,
      scaleX: active.scaleX,
      scaleY: active.scaleY
    };

    fc.remove(active);

    items.forEach(item => {
      item.set({
        left: groupPos.left + (item.left * groupPos.scaleX),
        top: groupPos.top + (item.top * groupPos.scaleY),
        angle: groupPos.angle + (item.angle || 0),
        scaleX: item.scaleX * groupPos.scaleX,
        scaleY: item.scaleY * groupPos.scaleY,
        selectable: true,
        evented: true
      });
      item.setCoords();
      fc.add(item);
    });

    setTimeout(() => {
      const sel = new ActiveSelection(items, { canvas: fc });
      fc.setActiveObject(sel);
      fc.renderAll();
    }, 10);
  }

  fc.renderAll();
}

/* ═══════════════════ TEXT TOOLBAR ═══════════════════ */
function syncTextProps(obj) {
  if (!obj) return;
  txtFont.value = obj.fontFamily ?? "sans-serif";
  txtSize.value = obj.fontSize ?? 16;
  txtBold.value = obj.fontWeight === "bold";
  txtItalic.value = obj.fontStyle === "italic";
  txtUnder.value = obj.underline === true;
  txtAlign.value = obj.textAlign ?? "center";
  txtColor.value = obj.fill ?? "#000000";
}

function placeToolbar(obj) {
  if (!obj || !canvasRef.value) return;
  const cr = canvasRef.value.getBoundingClientRect();
  const b = obj.getBoundingRect();
  toolbar.value = {
    visible: true,
    left: cr.left + b.left + b.width / 2,
    top: cr.top + b.top - 56,
  };
}

function applyText(prop, value) {
  const obj = editingText.value;
  if (!obj) return;
  obj.set(prop, value);
  fc.renderAll();
}

function toggleBold() {
  txtBold.value = !txtBold.value;
  applyText("fontWeight", txtBold.value ? "bold" : "normal");
}

function toggleItalic() {
  txtItalic.value = !txtItalic.value;
  applyText("fontStyle", txtItalic.value ? "italic" : "normal");
}

function toggleUnder() {
  txtUnder.value = !txtUnder.value;
  applyText("underline", txtUnder.value);
}

function setAlign(a) {
  txtAlign.value = a;
  applyText("textAlign", a);
}

function setFont(f) {
  txtFont.value = f;
  showFonts.value = false;
  applyText("fontFamily", f);
}

function changeSize(d) {
  txtSize.value = Math.max(6, txtSize.value + d);
  applyText("fontSize", txtSize.value);
}

function applyColor(e) {
  txtColor.value = e.target.value;
  applyText("fill", txtColor.value);
}

/* ═══════════════════ SHAPES FLYOUT ═══════════════════ */
function openShapesMenu(e) {
  e.stopPropagation();
  if (shapeBtnRef.value) {
    const r = shapeBtnRef.value.getBoundingClientRect();
    flyoutPos.value = { top: r.top, left: r.right + 25 };
  }
  showShapes.value = !showShapes.value;
}

function openBrushMenu(e) {
  e.stopPropagation();
  if (brushBtnRef.value) {
    const r = brushBtnRef.value.getBoundingClientRect();
    brushFlyoutPos.value = { top: r.top, left: r.right + 25 };
  }
  showBrushSize.value = !showBrushSize.value;
}

function onDocClick(e) {
  if (shapeBtnRef.value && !shapeBtnRef.value.contains(e.target))
    showShapes.value = false;
  if (brushBtnRef.value && !brushBtnRef.value.contains(e.target))
    showBrushSize.value = false;
}

/* ═══════════════════ CANVAS ACTIONS ═══════════════════ */
function undo() {
  const objs = fc.getObjects();
  if (objs.length) fc.remove(objs[objs.length - 1]);
}

function clearCanvas() {
  fc.clear();
  fc.backgroundColor = canvasDark.value ? "#18181b" : "#ffffff";
  fc.renderAll();
}

function save() {
  localStorage.setItem("drawing", JSON.stringify(fc.toJSON()));
}

function exportCanvas() {
  const a = document.createElement("a");
  a.download = `drawing-${Date.now()}.png`;
  a.href = fc.toDataURL({ format: "png", quality: 1 });
  a.click();
}

function loadDrawing() {
  const saved = localStorage.getItem("drawing");
  if (saved) {
    fc.loadFromJSON(saved, () => {
      fc.renderAll();
      fc.getObjects().forEach(obj => {
        if (obj.type === 'group') {
          obj.set({ interactive: true, subTargetCheck: true });
        }
      });
      fc.renderAll();
    });
  }
}

/* ═══════════════════ STYLE HELPERS ═══════════════════ */
function btn(active = false, danger = false) {
  return [
    "flex items-center justify-center w-9 h-9 rounded-xl transition-all duration-100 cursor-pointer hover:scale-105 active:scale-95",
    active ? "bg-zinc-800 text-white" : "text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700",
    danger && !active ? "hover:!bg-red-50 hover:!text-red-500" : "",
  ].join(" ");
}

function tbBtn(active = false) {
  return [
    "flex items-center justify-center h-7 min-w-[28px] px-1.5 rounded-lg text-xs font-medium transition-all duration-75 cursor-pointer select-none",
    active ? "bg-zinc-800 text-white" : "text-zinc-600 hover:bg-zinc-100",
  ].join(" ");
}
</script>

<template>
  <div class="flex w-full h-full min-h-0 overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100">

    <!-- ════ FLOATING TEXT TOOLBAR ════ -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 translate-y-1 scale-95"
        leave-active-class="transition duration-100 ease-in"
        leave-to-class="opacity-0 translate-y-1 scale-95"
      >
        <div
          v-if="toolbar.visible && editingText"
          class="fixed z-[9999] flex items-center gap-1 px-2 py-1.5 bg-white border border-zinc-200 rounded-2xl shadow-xl shadow-black/10 -translate-x-1/2 pointer-events-auto"
          :style="{ top: toolbar.top + 'px', left: toolbar.left + 'px' }"
          @mousedown.prevent
        >
          <!-- Font family -->
          <div class="relative">
            <button
              class="flex items-center gap-1 h-7 px-2 rounded-lg text-xs text-zinc-700 hover:bg-zinc-100 transition-colors"
              style="min-width:96px; justify-content:space-between;"
              @click.stop="showFonts = !showFonts"
            >
              <span class="truncate max-w-[72px]" :style="{ fontFamily: txtFont }">{{ txtFont }}</span>
              <span class="text-[9px] text-zinc-400 shrink-0">{{ showFonts ? '▲' : '▼' }}</span>
            </button>
            <Transition
              enter-active-class="transition duration-100 ease-out"
              enter-from-class="opacity-0 -translate-y-1"
              leave-active-class="transition duration-75 ease-in"
              leave-to-class="opacity-0 -translate-y-1"
            >
              <div v-if="showFonts" class="absolute bottom-full mb-2 left-0 z-10 bg-white border border-zinc-200 rounded-xl shadow-xl py-1 min-w-[150px] max-h-52 overflow-y-auto">
                <button
                  v-for="f in FONTS" :key="f"
                  class="w-full text-left px-3 py-1.5 text-sm hover:bg-zinc-100 transition-colors"
                  :style="{ fontFamily: f }"
                  :class="f === txtFont ? 'text-zinc-900 font-semibold bg-zinc-50' : 'text-zinc-700'"
                  @click.stop="setFont(f)"
                >{{ f }}</button>
              </div>
            </Transition>
          </div>

          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0"></div>

          <!-- Font size -->
          <div class="flex items-center gap-0.5">
            <button :class="tbBtn()" @click="changeSize(-1)">−</button>
            <span class="text-xs font-mono w-7 text-center tabular-nums text-zinc-700 select-none">{{ txtSize }}</span>
            <button :class="tbBtn()" @click="changeSize(1)">+</button>
          </div>

          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0"></div>

          <!-- B I U -->
          <button :class="tbBtn(txtBold)" class="!font-bold !text-sm" title="Bold" @click="toggleBold">B</button>
          <button :class="tbBtn(txtItalic)" class="!italic !text-sm" title="Italic" @click="toggleItalic">I</button>
          <button :class="[tbBtn(txtUnder), '!underline !text-sm']" title="Underline" @click="toggleUnder">U</button>

          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0"></div>

          <!-- Alignment -->
          <button :class="tbBtn(txtAlign === 'left')" title="Left" @click="setAlign('left')">
            <svg width="14" height="12" viewBox="0 0 14 12" fill="none">
              <rect x="0" y="0.25" width="14" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="0" y="3.25" width="9" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="0" y="6.25" width="14" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="0" y="9.25" width="7" height="1.5" rx=".75" fill="currentColor"/>
            </svg>
          </button>
          <button :class="tbBtn(txtAlign === 'center')" title="Center" @click="setAlign('center')">
            <svg width="14" height="12" viewBox="0 0 14 12" fill="none">
              <rect x="0" y="0.25" width="14" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="2.5" y="3.25" width="9" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="0" y="6.25" width="14" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="3.5" y="9.25" width="7" height="1.5" rx=".75" fill="currentColor"/>
            </svg>
          </button>
          <button :class="tbBtn(txtAlign === 'right')" title="Right" @click="setAlign('right')">
            <svg width="14" height="12" viewBox="0 0 14 12" fill="none">
              <rect x="0" y="0.25" width="14" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="5" y="3.25" width="9" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="0" y="6.25" width="14" height="1.5" rx=".75" fill="currentColor"/>
              <rect x="7" y="9.25" width="7" height="1.5" rx=".75" fill="currentColor"/>
            </svg>
          </button>

          <div class="w-px h-5 bg-zinc-200 mx-0.5 shrink-0"></div>

          <!-- Text color -->
          <label class="relative flex items-center justify-center w-7 h-7 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer" title="Text color">
            <span class="text-sm font-bold select-none leading-none" :style="{ color: txtColor }">A</span>
            <input type="color" :value="txtColor" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer" @input="applyColor" />
          </label>
        </div>
      </Transition>
    </Teleport>

    <!-- ════ SIDEBAR ════ -->
    <aside class="flex flex-col items-center w-14 shrink-0 h-full py-3 gap-1 border-r border-zinc-200 bg-white rounded-l-xl overflow-y-auto overflow-x-visible">

      <!-- Dark mode -->
      <button :class="btn(canvasDark)" title="Toggle background" @click="canvasDark = !canvasDark">
        <span class="text-base select-none leading-none">{{ canvasDark ? '☀️' : '🌙' }}</span>
      </button>

      <div class="w-8 h-px my-1 bg-zinc-200 shrink-0"></div>

      <!-- Pen -->
      <button :class="btn(tool === 'pen')" title="Pen" @click="setTool('pen')">
        <Shape name="pen2" :size="32" />
      </button>

      <!-- Eraser -->
      <button :class="btn(tool === 'eraser')" title="Eraser" @click="setTool('eraser')">
        <Shape name="eraser" :size="32" />
      </button>

      <div class="w-8 h-px my-1 bg-zinc-200 shrink-0"></div>

      <!-- Color -->
      <div class="relative shrink-0">
        <div class="w-7 h-7 rounded-lg border-2 border-[#d6d6d6] cursor-pointer shadow-inner overflow-hidden hover:scale-105 transition-transform"
             :style="{ backgroundColor: strokeColor }">
          <input type="color" v-model="strokeColor" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer" />
        </div>
      </div>

      <!-- Brush size toggle -->
      <button
        ref="brushBtnRef"
        :class="btn(showBrushSize)"
        title="Brush size"
        @click.stop="openBrushMenu"
      >
        <div class="flex items-center justify-center w-full h-full">
          <div
            class="rounded-full bg-current transition-all duration-75"
            :style="{ width: Math.min(18, Math.max(3, lineWidth * 0.4)) + 'px', height: Math.min(18, Math.max(3, lineWidth * 0.4)) + 'px' }"
          ></div>
        </div>
      </button>

      <!-- Brush size flyout -->
      <Teleport to="body">
        <Transition
          enter-active-class="transition duration-150 ease-out"
          enter-from-class="opacity-0 -translate-x-1 scale-95"
          leave-active-class="transition duration-100 ease-in"
          leave-to-class="opacity-0 -translate-x-1 scale-95"
        >
          <div
            v-if="showBrushSize"
            class="fixed z-[9999] flex flex-col items-center gap-2 px-3 py-3 bg-white border border-zinc-200 rounded-2xl shadow-xl shadow-black/10"
            :style="{ top: brushFlyoutPos.top + 'px', left: brushFlyoutPos.left + 'px' }"
            @click.stop
          >
            <!-- Preview dot -->
            <div class="w-6 h-6 flex items-center justify-center shrink-0">
              <div
                class="rounded-full transition-all duration-75"
                :style="{
                  width: Math.min(22, Math.max(3, lineWidth * 0.5)) + 'px',
                  height: Math.min(22, Math.max(3, lineWidth * 0.5)) + 'px',
                  backgroundColor: tool === 'eraser' ? '#d4d4d8' : strokeColor,
                }"
              ></div>
            </div>
            <!-- Vertical slider -->
            <input
              type="range"
              v-model.number="lineWidth"
              min="1"
              max="50"
              class="cursor-pointer accent-zinc-800"
              style="writing-mode: vertical-lr; direction: rtl; width: 4px; height: 100px;"
            />
            <!-- Size label -->
            <span class="text-[10px] font-mono tabular-nums text-zinc-400 select-none">{{ lineWidth }}</span>
          </div>
        </Transition>
      </Teleport>

      <div class="w-8 h-px my-1 bg-zinc-200 shrink-0"></div>

      <!-- Select -->
      <button :class="btn(tool === 'select')" title="Select & move" @click="setTool('select')">
        <Shape name="select" />
      </button>

      <!-- Shapes -->
      <div class="relative flex flex-col items-center gap-0.5 shrink-0">
        <button :class="btn(false)" :title="`Add ${activeShape}`" @click="addFigure(activeShape)">
          <span class="text-base leading-none select-none">{{ shapeIcons[activeShape] }}</span>
        </button>
        <button ref="shapeBtnRef"
                class="text-[9px] px-1 py-0.5 rounded leading-none text-zinc-400 hover:text-zinc-600 transition-colors"
                title="More shapes"
                @click.stop="openShapesMenu">
          {{ showShapes ? '▲' : '▼' }}
        </button>

        <Teleport to="body">
          <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1 scale-95"
            leave-active-class="transition duration-100 ease-in"
            leave-to-class="opacity-0 -translate-y-1 scale-95"
          >
            <div v-if="showShapes"
                 class="fixed z-[9999] flex flex-col gap-1 p-1.5 rounded-xl border border-zinc-200 bg-white shadow-lg"
                 :style="{ top: flyoutPos.top + 'px', left: flyoutPos.left + 'px' }">
              <button v-for="s in shapes" :key="s" :class="btn(activeShape === s)" :title="s" @click="addFigure(s)">
                <span class="text-base leading-none select-none">{{ shapeIcons[s] }}</span>
              </button>
            </div>
          </Transition>
        </Teleport>
      </div>

      <!-- Text -->
      <button :class="btn()" title="Add text box" @click="addTextBox">
        <span class="text-[15px] font-bold leading-none select-none" style="font-family:serif">T</span>
      </button>

      <div class="w-8 h-px my-1 bg-zinc-200 shrink-0"></div>

      <!-- Group / Ungroup button -->
      <button :class="btn()" title="Group selected / Ungroup" @click="groupOrUngroup">
        <span class="text-base leading-none select-none">⊞</span>
      </button>

      <div class="w-8 h-px my-1 bg-zinc-200 shrink-0"></div>

      <!-- Undo -->
      <button :class="btn()" title="Undo" @click="undo">
        <Shape name="undo" :size="20" />
      </button>

      <!-- Clear -->
      <button :class="btn(false, true)" title="Clear canvas" @click="clearCanvas">
        <Shape name="clear" :size="20" />
      </button>

      <div class="w-8 h-px my-1 bg-zinc-200 shrink-0"></div>

      <!-- Save -->
      <button :class="btn()" title="Save" @click="save">
        <Shape name="save" :size="20" />
      </button>

      <!-- Export PNG -->
      <button :class="btn()" title="Export PNG" @click="exportCanvas">
        <Shape name="export-image" :size="20" />
      </button>

    </aside>

    <!-- ════ CANVAS ════ -->
    <div ref="containerRef" class="flex-1 min-w-0 h-full relative">
      <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10 pointer-events-none text-[11px] text-zinc-400 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full border border-zinc-200 select-none whitespace-nowrap">
        Double-click a shape to type · Shift-click objects then ⊞ to group
      </div>
      <canvas ref="canvasRef" style="display:block; width:100%; height:100%;"></canvas>
    </div>

  </div>
</template>
