import { getRequest, postRequest } from "./apiHelpers.js";

export function fetchDrawing(type, id, signal) {
  return getRequest("/api/drawings", { type, id }, { signal });
}

export function upsertDrawing(payload) {
  return postRequest("/api/drawings", payload);
}
