import { defineStore } from "pinia";
import * as DrawingAPI from "../../utils/drawingApi.js";
import { useFlash } from "../components/useFlash.js";
import { useLayoutStore } from "./layoutStore.js";

const layout = useLayoutStore();
const cacheKey = (type, id) => `${type}-${id}`;

export const useDrawingStore = defineStore("drawing", {
  state: () => ({
    cache:   {},
    loading: false,
    saving:  false,
  }),
  actions: {
    async loadDrawing(type, id) {
        if(!layout.layouts.tasks.detailsSections.canvas.visible) return null; // Don't load if canvas isn't visible

      const key = cacheKey(type, id);
      if (key in this.cache) return this.cache[key];

      this.loading = true;
      try {
        const res = await DrawingAPI.fetchDrawing(type, id);
        let data = null;
        if (res?.data) {
          if (typeof res.data === "string") data = res.data;
          else if (res.data?.data && typeof res.data.data === "string") data = res.data.data;
          else data = res.data;
        }
        return (this.cache[key] = data);
      } catch (err) {
        if (err?.response?.status === 404) return (this.cache[key] = null);
        useFlash().show("error", "Could not load drawing.");
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async saveDrawing({ type, id, data }) {
      const key = cacheKey(type, id);
      this.saving = true;
      try {
        const payload = typeof data === "string" ? data : JSON.stringify(data);
        const res = await DrawingAPI.upsertDrawing({ type, id, data: payload });
        const saved = res?.data ?? payload;
        this.cache[key] = saved;
        useFlash().show("success", "Drawing saved.");
        return saved;
      } catch (err) {
        useFlash().show("error", "Failed to save drawing.");
        throw err;
      } finally {
        this.saving = false;
      }
    },

    invalidate(type, id) {
      delete this.cache[cacheKey(type, id)];
    },

    clearAll() {
      this.cache = {};
    },
  },
});
