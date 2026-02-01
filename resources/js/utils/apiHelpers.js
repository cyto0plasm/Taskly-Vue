/**
 * Generic API request wrapper
 * Handles JSON/FormData automatically
 */
//1. apiRequest(url, options) → generic fetch (JSON/FormData auto-handled)
//2. patchRequest(url, data, csrf) → PATCH with JSON + CSRF
//3. deleteRequest(url, csrf) → DELETE with CSRF
//4. (body can be FormData, headers optional)
//5. returns parsed JSON or null; throws on non-ok
//6. caching handled in EntityState, not here

export async function apiRequest(url, options = {}) {
    const headers = options.headers || {};
    if (!(options.body instanceof FormData))
        headers["Content-Type"] = "application/json";
    headers["Accept"] = "application/json";

    const res = await fetch(url, { ...options, headers });
    if (!res.ok) {
        const t = await res.text();
        throw new Error(`HTTP ${res.status}: ${res.statusText} - ${t}`);
    }
    const text = await res.text();
    return text ? JSON.parse(text) : null;
}

/** PATCH helper */
export async function patchRequest(url, data, csrfToken) {
    return apiRequest(url, {
        method: "PATCH",
        headers: { "X-CSRF-TOKEN": csrfToken },
        body: JSON.stringify(data),
    });
}

/** DELETE helper */
export async function deleteRequest(url, csrfToken) {
    return apiRequest(url, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": csrfToken },
    });
}
