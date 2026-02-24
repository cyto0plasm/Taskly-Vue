import { computed, ref, watch } from "vue";
import {
  Canvas, PencilBrush, Rect, Circle, Triangle, Ellipse,
  Textbox, Group, Line, Path, ActiveSelection, util, Point,
} from "fabric";

let _seq = 0;
const uid = () => `s${++_seq}_${Date.now()}`;

export const SHAPES = [
  { key: "process",  icon: "▭", label: "Process"      },
  { key: "rounded",  icon: "▢", label: "Rounded Rect" },
  { key: "decision", icon: "◇", label: "Decision"     },
  { key: "terminal", icon: "⬭", label: "Terminal"     },
  { key: "circle",   icon: "○", label: "Circle"       },
  { key: "ellipse",  icon: "⬯", label: "Ellipse"      },
  { key: "triangle", icon: "△", label: "Triangle"     },
];

export const FONTS = [
  "sans-serif", "serif", "monospace", "Georgia", "Arial",
  "Verdana", "Courier New", "Times New Roman", "Trebuchet MS", "Impact",
];

const GRID = 20;
const snapV = v => Math.round(v / GRID) * GRID;
const ANCHOR_PAD = 10;
const toPixel = (vpt, x, y) => ({
  x: vpt[0] * x + vpt[2] * y + vpt[4],
  y: vpt[1] * x + vpt[3] * y + vpt[5],
});

function buildPrimitive(type, color) {
  const base = { originX: "center", originY: "center", fill: color + "18", stroke: color, strokeWidth: 2 };
  const map = {
    process:  () => new Rect({ ...base, width: 160, height: 72,  rx: 4,  ry: 4  }),
    rounded:  () => new Rect({ ...base, width: 160, height: 72,  rx: 24, ry: 24 }),
    decision: () => new Rect({ ...base, width: 100, height: 100, rx: 4,  ry: 4, angle: 45, scaleX: 0.9, scaleY: 0.6 }),
    terminal: () => new Rect({ ...base, width: 160, height: 56,  rx: 28, ry: 28 }),
    circle:   () => new Circle({ ...base, radius: 52 }),
    ellipse:  () => new Ellipse({ ...base, rx: 82, ry: 48 }),
    triangle: () => new Triangle({ ...base, width: 130, height: 110 }),
  };
  return (map[type] ?? map.process)();
}

function buildLabel(shapeId, cx, cy) {
  const tb = new Textbox("", {
    left: cx, top: cy, originX: "center", originY: "center",
    width: 130, fontSize: 13, fontFamily: "sans-serif",
    fill: "#1f2937", textAlign: "center", editable: true,
    selectable: false, evented: false,
    hasControls: false, hasBorders: false,
    lockMovementX: true, lockMovementY: true,
  });
  tb._labelFor = shapeId;
  return tb;
}

function syncLabel(shape, label) {
  const c = shape.getCenterPoint();
  label.set({ left: c.x, top: c.y, angle: shape.angle ?? 0 });
  label.setCoords();
}

function anchors(obj) {
  const { left: l, top: t, width: w, height: h } = obj.getBoundingRect(true);
  return {
    top:    { x: l + w / 2,          y: t - ANCHOR_PAD     },
    right:  { x: l + w + ANCHOR_PAD, y: t + h / 2          },
    bottom: { x: l + w / 2,          y: t + h + ANCHOR_PAD },
    left:   { x: l - ANCHOR_PAD,     y: t + h / 2          },
  };
}

function nearestAnchor(obj, px, py) {
  return Object.entries(anchors(obj))
    .map(([k, v]) => ({ k, d: Math.hypot(v.x - px, v.y - py), x: v.x, y: v.y }))
    .sort((a, b) => a.d - b.d)[0];
}

function buildArrowPath(x1, y1, x2, y2, color) {
  const a = Math.atan2(y2 - y1, x2 - x1), H = 14, HA = 0.42;
  const f = n => n.toFixed(1);
  const d = `M ${f(x1)} ${f(y1)} L ${f(x2)} ${f(y2)} L ${f(x2 - H * Math.cos(a - HA))} ${f(y2 - H * Math.sin(a - HA))} M ${f(x2)} ${f(y2)} L ${f(x2 - H * Math.cos(a + HA))} ${f(y2 - H * Math.sin(a + HA))}`;
  const p = new Path(d, {
    stroke: color, strokeWidth: 2, fill: "",
    selectable: true, evented: true,
    hasControls: true, hasBorders: true,
    lockScalingX: true, lockScalingY: true, lockRotation: true,
  });
  p._isConnector = true;
  return p;
}

export function useCanvas() {
  let _fc = null, _el = null;
  const _conns = new Map(), _labels = new Map();
  let _draft = null, _hoverShape = null, _mousePos = { x: 0, y: 0 };
  let _panActive = false, _panLast = { x: 0, y: 0 };
  let _hist = [], _hi = -1, _lock = 0;

  const tool        = ref("pen");
  const strokeColor = ref("#3b82f6");
  const lineWidth   = ref(3);
  const canvasDark  = ref(false);
  const bgColor     = ref("#ffffff");
  const activeShape = ref("process");
  const gridVisible = ref(true);
  const isPanning   = ref(false);

  const editingText = ref(null);
  const toolbar     = ref({ top: 0, left: 0, visible: false });
  const showFonts   = ref(false);
  const txtFont     = ref("sans-serif");
  const txtSize     = ref(14);
  const txtBold     = ref(false);
  const txtItalic   = ref(false);
  const txtUnder    = ref(false);
  const txtAlign    = ref("center");
  const txtColor    = ref("#1f2937");

  watch(strokeColor, _applyBrush);
  watch(lineWidth,   _applyBrush);
  watch(canvasDark,  dark => { bgColor.value = dark ? "#18181b" : "#ffffff"; });

  function init(canvasEl, containerEl) {
    if (_fc) return _fc;
    if (!canvasEl || !containerEl) return null;
    _el = canvasEl;
    try {
      _fc = new Canvas(canvasEl, {
        isDrawingMode: false,
        width: containerEl.clientWidth,
        height: containerEl.clientHeight,
        preserveObjectStacking: true,
        stopContextMenu: true,
        allowTouchScrolling: false,
      });
      _attachEvents();
      setTool("pen");
      return _fc;
    } catch (err) { return null; }
  }

  function dispose() { _fc?.dispose(); _fc = null; }

  function _attachEvents() {
    _fc.on("text:editing:entered", ({ target }) => {
      editingText.value = target;
      _syncText(target);
      _placeToolbar(target);
    });
    _fc.on("text:editing:exited", () => {
      const tb = editingText.value;
      editingText.value = null;
      toolbar.value.visible = false;
      showFonts.value = false;
      if (tb?._labelFor) {
        tb.set({ selectable: false, evented: false });
        tb.setCoords();
        _fc.discardActiveObject();
      }
      _fc.renderAll();
      snap();
    });
    _fc.on("mouse:dblclick", ({ target }) => {
      if (!target?._shapeId || target._labelFor || tool.value !== "select") return;
      const label = _labels.get(target._shapeId);
      if (!label) return;
      label.set({ selectable: true, evented: true });
      label.setCoords();
      _fc.setActiveObject(label);
      label.enterEditing();
      _fc.requestRenderAll();
    });
    _fc.on("object:moving", ({ target }) => {
      if (target._isConnector) return;
      if (!target._labelFor) {
        target.set({ left: snapV(target.left), top: snapV(target.top) });
        target.setCoords();
      }
      if (target._shapeId) {
        const label = _labels.get(target._shapeId);
        if (label) syncLabel(target, label);
        _redrawConns(target);
      }
      if (editingText.value) _placeToolbar(editingText.value);
    });
    _fc.on("object:modified", ({ target }) => {
      if (!target._isConnector && !target._labelFor) {
        target.set({ left: snapV(target.left), top: snapV(target.top) });
        target.setCoords();
      }
      if (target._shapeId) {
        const label = _labels.get(target._shapeId);
        if (label) syncLabel(target, label);
        _redrawConns(target);
      }
      snap();
    });
    _fc.on("after:render", () => {
      if (editingText.value) _placeToolbar(editingText.value);
      _renderAnchorDots();
    });
    _fc.on("mouse:down", _onDown);
    _fc.on("mouse:move", _onMove);
    _fc.on("mouse:up",   _onUp);
  }
//  always clears, then conditionally draws
function drawGrid(gridCanvas) {
    if (!gridCanvas || !_fc) return;

    const dpr = window.devicePixelRatio || 1;
    const W = Math.round(gridCanvas.offsetWidth * dpr);
    const H = Math.round(gridCanvas.offsetHeight * dpr);
    if (gridCanvas.width !== W || gridCanvas.height !== H) {
        gridCanvas.width = W;
        gridCanvas.height = H;
    }

    const ctx = gridCanvas.getContext("2d");
    ctx.clearRect(0, 0, W, H); // always clear

    if (!gridVisible.value) return;

    const vpt = _fc.viewportTransform;
    const zoom = _fc.getZoom();
    const step = GRID * zoom * dpr;
    const startX = ((vpt[4] * dpr % step) + step) % step;
    const startY = ((vpt[5] * dpr % step) + step) % step;
    ctx.fillStyle = canvasDark.value ? "rgba(255,255,255,0.18)" : "rgba(0,0,0,0.15)";
    for (let sx = startX; sx < W; sx += step)
        for (let sy = startY; sy < H; sy += step) {
            ctx.beginPath();
            ctx.arc(sx, sy, dpr, 0, Math.PI * 2);
            ctx.fill();
        }
}
  function _renderAnchorDots() {
    if (tool.value !== "connector" || !_hoverShape) return;
    const ctx = _fc.getContext();
    const vpt = _fc.viewportTransform;
    const nearest = nearestAnchor(_hoverShape, _mousePos.x, _mousePos.y);
    ctx.save();
    Object.entries(anchors(_hoverShape)).forEach(([k, pt]) => {
      const { x, y } = toPixel(vpt, pt.x, pt.y);
      const hot = k === nearest.k;
      ctx.beginPath();
      ctx.arc(x, y, hot ? 8 : 5, 0, 2 * Math.PI);
      ctx.fillStyle   = hot ? "#ef4444" : "#3b82f6";
      ctx.strokeStyle = "#ffffff";
      ctx.lineWidth   = 2;
      ctx.fill();
      ctx.stroke();
    });
    ctx.restore();
  }

  function setTool(t) {
    tool.value = t;
    if (!_fc) return;
    const isSelect = t === "select", isConnector = t === "connector", isText = t === "text";
    _fc.isDrawingMode = t === "pen" || t === "eraser";
    _fc.selection = isSelect;
    _fc.defaultCursor = isConnector ? "crosshair" : isText ? "text" : isSelect ? "default" : "crosshair";
    _fc.hoverCursor   = isConnector ? "crosshair" : isText ? "text" : isSelect ? "move"    : "crosshair";
    _fc.getObjects().forEach(o => {
      if (o._labelFor) o.set({ selectable: false, evented: false, hasControls: false, hasBorders: false });
      else if (o._isConnector) o.set({ selectable: isSelect, evented: true, hasControls: isSelect, hasBorders: isSelect });
      else o.set({ selectable: isSelect, evented: true, hasControls: isSelect, hasBorders: isSelect });
      o.setCoords();
    });
    if (!isSelect) _fc.discardActiveObject();
    _applyBrush();
    _fc.renderAll();
  }

  function _applyBrush() {
    if (!_fc || (tool.value !== "pen" && tool.value !== "eraser")) return;
    const brush = new PencilBrush(_fc);
    if (tool.value === "eraser") {
      brush.color = bgColor.value || "#ffffff";
      brush.width = lineWidth.value * 3;
      _fc.off("path:created");
      _fc.on("path:created", ({ path }) => {
        if (tool.value !== "eraser") return;
        path.set({ selectable: false, evented: false, hasControls: false, hasBorders: false, stroke: bgColor.value || "#ffffff" });
        _fc.bringObjectToFront(path);
        _fc.renderAll();
        snap();
      });
    } else {
      _fc.off("path:created");
      brush.color = strokeColor.value;
      brush.width = lineWidth.value;
      _fc.on("path:created", ({ path }) => {
        if (tool.value !== "pen") return;
        path.set({ selectable: false, evented: false, hasControls: false, hasBorders: false });
        snap();
      });
    }
    _fc.freeDrawingBrush = brush;
  }

  watch(bgColor, color => { if (tool.value === "eraser" && _fc?.freeDrawingBrush) _fc.freeDrawingBrush.color = color; });

  function addShape(type) {
    if (!_fc) return;
    activeShape.value = type;
    setTool("select");
    const cx = snapV(_fc.width / 2), cy = snapV(_fc.height / 2);
    const id = uid();
    const prim = buildPrimitive(type, strokeColor.value);
    prim._shapeId = id;
    prim.set({ left: cx, top: cy });
    prim.setCoords();
    const label = buildLabel(id, cx, cy);
    _fc.add(prim);
    _fc.add(label);
    _labels.set(id, label);
    _fc.setActiveObject(prim);
    _fc.renderAll();
    snap();
  }

  function addText() { if (_fc) setTool("text"); }

  function _placeTextAt(px, py) {
    const tb = new Textbox("Text", {
      left: snapV(px), top: snapV(py),
      originX: "left", originY: "top",
      width: 200, fontSize: 18, fontFamily: "sans-serif",
      fill: strokeColor.value, textAlign: "left", editable: true,
    });
    _fc.add(tb);
    _fc.setActiveObject(tb);
    tb.enterEditing();
    _fc.renderAll();
    setTool("select");
    snap();
  }

  function _shapeAt(px, py) {
    const pt = new Point(px, py);
    return _fc.getObjects().filter(o => o._shapeId && !o._isConnector && !o._labelFor).reverse().find(o => (o.setCoords(), o.containsPoint(pt))) ?? null;
  }

  function _drawConn(conn) {
    const from = _fc.getObjects().find(o => o._shapeId === conn.fromId && !o._labelFor);
    const to   = _fc.getObjects().find(o => o._shapeId === conn.toId   && !o._labelFor);
    if (!from || !to) return;
    const fa = anchors(from)[conn.fromAnchor];
    const ta = anchors(to)[conn.toAnchor];
    if (!fa || !ta) return;
    if (conn.pathObj) _fc.remove(conn.pathObj);
    const p = buildArrowPath(fa.x, fa.y, ta.x, ta.y, conn.color);
    conn.pathObj = p;
    _fc.add(p);
    _fc.sendObjectToBack(p);
  }

  function _redrawConns(obj) {
    if (!obj?._shapeId) return;
    _conns.forEach(conn => { if (conn.fromId === obj._shapeId || conn.toId === obj._shapeId) _drawConn(conn); });
    _fc.renderAll();
  }

  function _onDown(e) {
    const native = e.e;
    if (native.ctrlKey || native.metaKey) {
      _panActive = true;
      _panLast = { x: native.clientX, y: native.clientY };
      isPanning.value = true;
      _fc.getObjects().forEach(o => o.set({ evented: false }));
      _fc.discardActiveObject();
      _fc.renderAll();
      return;
    }
    if (tool.value === "text") {
      const p = _fc.getScenePoint(native);
      _placeTextAt(p.x, p.y);
      return;
    }
    if (tool.value !== "connector") return;
    const p = _fc.getScenePoint(native);
    const hit = _shapeAt(p.x, p.y);
    if (!hit) return;
    native.stopPropagation();
    const anch = nearestAnchor(hit, p.x, p.y);
    const tempLine = new Line([anch.x, anch.y, anch.x, anch.y], { stroke: strokeColor.value, strokeWidth: 2, strokeDashArray: [6, 4], selectable: false, evented: false });
    _fc.add(tempLine);
    _draft = { fromId: hit._shapeId, fromAnchor: anch.k, tempLine };
  }

  function _onMove(e) {
    const native = e.e;
    if (_panActive) {
      const dx = native.clientX - _panLast.x;
      const dy = native.clientY - _panLast.y;
      _panLast = { x: native.clientX, y: native.clientY };
      const vpt = _fc.viewportTransform.slice();
      vpt[4] += dx; vpt[5] += dy;
      _fc.setViewportTransform(vpt);
      _fc.requestRenderAll();
      return;
    }
    if (tool.value !== "connector") return;
    const p = _fc.getScenePoint(native);
    _mousePos = { x: p.x, y: p.y };
    if (_draft?.tempLine) _draft.tempLine.set({ x2: p.x, y2: p.y });
    _hoverShape = _shapeAt(p.x, p.y);
    _fc.requestRenderAll();
  }

  function _onUp(e) {
    if (_panActive) {
      _panActive = false;
      isPanning.value = false;
      setTool(tool.value);
      return;
    }
    if (tool.value !== "connector" || !_draft) return;
    const { tempLine, fromId, fromAnchor } = _draft;
    if (tempLine) _fc.remove(tempLine);
    _draft = null;
    _hoverShape = null;
    const p = _fc.getScenePoint(e.e);
    const target = _shapeAt(p.x, p.y);
    if (target && target._shapeId !== fromId) {
      const anch = nearestAnchor(target, p.x, p.y);
      const id = uid();
      const conn = { id, fromId, toId: target._shapeId, fromAnchor, toAnchor: anch.k, color: strokeColor.value, pathObj: null };
      _conns.set(id, conn);
      _drawConn(conn);
      snap();
    }
    _fc.renderAll();
    setTool("select");
  }

  function snap() {
    if (_lock > 0 || !_fc) return;
    _lock++;
    const serialized = _serialize();
    _lock--;
    if (!serialized) return;
    const raw = JSON.stringify(serialized);
    if (_hist[_hi] === raw) return;
    _hist.splice(_hi + 1);
    _hist.push(raw);
    _hi++;
    if (_hist.length > 60) { _hist.shift(); _hi--; }
  }

  async function undo() { if (_hi > 0 && _fc) { _hi--; await _restore(JSON.parse(_hist[_hi])); } }
  async function redo() { if (_hi < _hist.length - 1 && _fc) { _hi++; await _restore(JSON.parse(_hist[_hi])); } }

  function groupOrUngroup() {
    if (!_fc) return;
    const active = _fc.getActiveObject();
    if (!active) return;
    const isMulti = active.type === "activeselection" || active.type === "activeSelection";
    if (isMulti) {
      const items = active.getObjects().filter(o => !o._labelFor);
      if (!items.length) return;
      _fc.discardActiveObject();
      items.forEach(i => _fc.remove(i));
      const g = new Group(items);
      g._shapeId = uid();
      _fc.add(g);
      _fc.setActiveObject(g);
    } else if (active.type === "group") {
      const mat = active.calcTransformMatrix();
      const items = [...active.getObjects()];
      _fc.remove(active);
      items.forEach(item => {
        const o = util.qrDecompose(util.multiplyTransformMatrices(mat, item.calcTransformMatrix()));
        item.set({ ...o, left: o.translateX, top: o.translateY, flipX: false, flipY: false, originX: "center", originY: "center", selectable: true, evented: true });
        item.setCoords();
        _fc.add(item);
      });
      _fc.setActiveObject(new ActiveSelection(items, { canvas: _fc }));
    }
    _fc.requestRenderAll();
    snap();
  }

  function deleteSelected() {
    if (!_fc) return;
    const active = _fc.getActiveObject();
    if (!active) return;
    const targets = (active.type === "activeselection" || active.type === "activeSelection") ? active.getObjects() : [active];
    targets.forEach(obj => {
      if (obj._isConnector) {
        for (const [id, c] of _conns) if (c.pathObj === obj) { _conns.delete(id); break; }
        _fc.remove(obj);
        return;
      }
      if (obj._shapeId) {
        const label = _labels.get(obj._shapeId);
        if (label) { _fc.remove(label); _labels.delete(obj._shapeId); }
        for (const [id, c] of _conns) {
          if (c.fromId === obj._shapeId || c.toId === obj._shapeId) {
            if (c.pathObj) _fc.remove(c.pathObj);
            _conns.delete(id);
          }
        }
      }
      _fc.remove(obj);
    });
    _fc.discardActiveObject();
    _fc.renderAll();
    snap();
  }

  function clear() {
    if (!_fc) return;
    _conns.clear(); _labels.clear(); _fc.clear(); _fc.renderAll(); snap();
  }

  const zoomIn    = () => _fc && (_fc.setZoom(Math.min(5, _fc.getZoom() * 1.2)), _fc.requestRenderAll());
  const zoomOut   = () => _fc && (_fc.setZoom(Math.max(0.1, _fc.getZoom() / 1.2)), _fc.requestRenderAll());
  const zoomReset = () => _fc && (_fc.setZoom(1), _fc.setViewportTransform([1,0,0,1,0,0]), _fc.requestRenderAll());

  function _syncText(obj) {
    if (!obj) return;
    txtFont.value   = obj.fontFamily ?? "sans-serif";
    txtSize.value   = obj.fontSize   ?? 14;
    txtBold.value   = obj.fontWeight === "bold";
    txtItalic.value = obj.fontStyle  === "italic";
    txtUnder.value  = obj.underline  === true;
    txtAlign.value  = obj.textAlign  ?? "center";
    txtColor.value  = obj.fill       ?? "#1f2937";
  }

  function _placeToolbar(obj) {
    if (!obj || !_el) return;
    const cr = _el.getBoundingClientRect();
    const b  = obj.getBoundingRect();
    toolbar.value = { visible: true, left: cr.left + b.left + b.width / 2, top: cr.top + b.top - 60 };
  }

  function _applyText(prop, val) { if (editingText.value && _fc) { editingText.value.set(prop, val); _fc.renderAll(); } }
  const toggleBold    = () => { txtBold.value   = !txtBold.value;   _applyText("fontWeight", txtBold.value   ? "bold"   : "normal"); };
  const toggleItalic  = () => { txtItalic.value = !txtItalic.value; _applyText("fontStyle",  txtItalic.value ? "italic" : "normal"); };
  const toggleUnder   = () => { txtUnder.value  = !txtUnder.value;  _applyText("underline",  txtUnder.value); };
  const setAlign      = a  => { txtAlign.value  = a;  _applyText("textAlign",  a); };
  const setFont       = fn => { txtFont.value   = fn; showFonts.value = false; _applyText("fontFamily", fn); };
  const changeSize    = d  => { txtSize.value   = Math.max(6, txtSize.value + d); _applyText("fontSize", txtSize.value); };
  const applyTxtColor = e  => { txtColor.value  = e.target.value; _applyText("fill", txtColor.value); };

  function exportPNG() {
    if (!_fc) return;
    const tmp = document.createElement("canvas");
    tmp.width = _fc.width; tmp.height = _fc.height;
    const ctx = tmp.getContext("2d");
    ctx.fillStyle = bgColor.value;
    ctx.fillRect(0, 0, tmp.width, tmp.height);
    const img = new Image();
    img.onload = () => { ctx.drawImage(img, 0, 0); const a = document.createElement("a"); a.download = `canvas-${Date.now()}.png`; a.href = tmp.toDataURL("image/png"); a.click(); };
    img.src = _fc.toDataURL({ format: "png", quality: 1 });
  }

  function _serialize() {
    if (!_fc) return null;
    const paths = _fc.getObjects().filter(o => o._isConnector);
    paths.forEach(p => _fc.remove(p));
    const canvasJSON = _fc.toJSON(["_shapeId", "_labelFor", "_isConnector"]);
    paths.forEach(p => { _fc.add(p); _fc.sendObjectToBack(p); });
    return {
      canvas: canvasJSON,
      connections: [..._conns.values()].map(({ id, fromId, toId, fromAnchor, toAnchor, color }) => ({ id, fromId, toId, fromAnchor, toAnchor, color })),
      bg: bgColor.value,
    };
  }

  async function _restore(data) {
    if (!_fc || !data) return;
    _lock++;
    _conns.clear(); _labels.clear();
    let canvasData = data, connList = [], bg = bgColor.value;
    if (data.canvas) { canvasData = data.canvas; connList = data.connections || []; bg = data.bg || bgColor.value; }
    if (bg) bgColor.value = bg;
    _fc.getObjects().forEach(obj => { if (!obj._isConnector) _fc.remove(obj); });
    try {
      await _fc.loadFromJSON(canvasData);
      _fc.getObjects().forEach(o => {
        const locked = !!(o._isConnector || o._labelFor);
        o.set({ selectable: locked ? false : (tool.value === "select"), evented: true, hasControls: locked ? false : (tool.value === "select"), hasBorders: locked ? false : (tool.value === "select") });
        o.setCoords();
        if (o._labelFor) _labels.set(o._labelFor, o);
      });
      connList.forEach(c => { const conn = { ...c, pathObj: null }; _conns.set(c.id, conn); _drawConn(conn); });
      _fc.renderAll();
    } catch (err) { console.error("Failed to restore canvas:", err); }
    finally { _lock--; }
  }

  function getData() { return _serialize(); }
  async function setData(raw) {
    const data = typeof raw === "string" ? JSON.parse(raw) : raw;
    await _restore(data);
    _hist.length = 0; _hi = -1; snap();
  }

  function selectAll() {
    if (!_fc) return;
    setTool("select");
    const items = _fc.getObjects().filter(o => !o._isConnector && !o._labelFor);
    if (!items.length) return;
    if (items.length === 1) _fc.setActiveObject(items[0]);
    else _fc.setActiveObject(new ActiveSelection(items, { canvas: _fc }));
    _fc.requestRenderAll();
  }

  function setupKeyboard(opts = {}) {
    const handler = e => {
      const tag = e.target.tagName;
      if (tag === "INPUT" || tag === "TEXTAREA" || e.target.isContentEditable) return;
      const ctrl = e.ctrlKey || e.metaKey, shift = e.shiftKey, key = e.key;
      if (ctrl) {
        if (key === "z" && !shift) { e.preventDefault(); undo(); return; }
        if (key === "z" &&  shift) { e.preventDefault(); redo(); return; }
        if (key === "y")           { e.preventDefault(); redo(); return; }
        if (key === "s")           { e.preventDefault(); opts.onSave?.();    return; }
        if (key === "p")           { e.preventDefault(); exportPNG();        return; }
        if (key === "a")           { e.preventDefault(); selectAll();        return; }
        if (key === "g")           { e.preventDefault(); groupOrUngroup();   return; }
        return;
      }
      switch (key) {
        case "v": setTool("select"); break;
        case "d": setTool("pen"); break;
        case "e": setTool("eraser"); break;
        case "t": setTool("text"); break;
        case "c": opts.onOpenColor?.(); break;
        case "s": opts.onOpenSize?.(); break;
        case "Delete": case "Backspace": deleteSelected(); break;
      }
    };
    document.addEventListener("keydown", handler);
    return () => document.removeEventListener("keydown", handler);
  }

  function resize(width, height) { _fc?.setDimensions({ width, height }); _fc?.renderAll(); }
  function _fc_onAfterRender(cb) { if (!_fc) return () => {}; _fc.on("after:render", cb); return () => _fc.off("after:render", cb); }
  const isReady = computed(() => _fc !== null);

  return {
    getFabricInstance: () => _fc,
    isReady,
    tool, strokeColor, lineWidth, canvasDark, bgColor, activeShape, gridVisible, isPanning,
    editingText, toolbar, showFonts,
    txtFont, txtSize, txtBold, txtItalic, txtUnder, txtAlign, txtColor,
    SHAPES, FONTS,
    init, dispose, resize,
    setTool, drawGrid, _fc_onAfterRender,
    addShape, addText, selectAll,
    undo, redo, snap, clear, deleteSelected, groupOrUngroup, exportPNG,
    zoomIn, zoomOut, zoomReset,
    toggleBold, toggleItalic, toggleUnder, setAlign, setFont, changeSize, applyTxtColor,
    getData, setData,
    setupKeyboard,
  };
}
