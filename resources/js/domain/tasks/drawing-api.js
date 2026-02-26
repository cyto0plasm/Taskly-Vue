import { getRequest, postRequest } from "../../utils/apiHelpers.js";

export function fetchDrawing(type, id, signal) {
  return getRequest("/api/drawings", { type, id }, { signal });
}

export function upsertDrawing(payload) {
  return postRequest("/api/drawings", payload);
}
