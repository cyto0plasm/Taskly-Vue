import axios from "axios";

// CSRF token setup (same as before)
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

const api = axios.create({
  baseURL: "/", // optional: your API root
  headers: {
    Accept: "application/json",
    ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}),
  },
  withCredentials: true, // same as credentials: 'same-origin'
});

// generic request
export async function apiRequest(url, options = {}) {
  const { method = "GET", data = null, params = {}, signal } = options;
  try {
    const res = await api.request({ url, method, data, params, signal });
    return res.data;
  } catch (err) {
    if (axios.isCancel(err)) {
      throw { name: "AbortError", message: "Request cancelled" };
    }
    throw {
      status: err.response?.status,
      message: err.response?.data?.message || err.message,
      errors: err.response?.data?.errors || null,
    };
  }
}

// shortcuts
export const getRequest = (url, params = {}, options = {}) =>
  apiRequest(url, { method: "GET", params, ...options });

export const postRequest = (url, data) =>
  apiRequest(url, { method: "POST", data });

export const patchRequest = (url, data) =>
  apiRequest(url, { method: "PATCH", data });

export const deleteRequest = (url) =>
  apiRequest(url, { method: "DELETE" });
