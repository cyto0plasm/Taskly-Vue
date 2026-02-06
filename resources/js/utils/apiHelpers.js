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
  const { method = "GET", data = null, params = {} } = options;
  try {
    const res = await api.request({ url, method, data, params });
    return res.data;
  } catch (err) {
    // unify error
    throw {
      status: err.response?.status,
      message: err.response?.data?.message || err.message,
      errors: err.response?.data?.errors || null,
    };
  }
}

// shortcuts
export const getRequest = (url, params = {}) =>
  apiRequest(url, { method: "GET", params });

export const postRequest = (url, data) =>
  apiRequest(url, { method: "POST", data });

export const patchRequest = (url, data) =>
  apiRequest(url, { method: "PATCH", data });

export const deleteRequest = (url) =>
  apiRequest(url, { method: "DELETE" });
