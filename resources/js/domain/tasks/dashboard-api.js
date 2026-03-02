import  { apiRequest, postRequest, patchRequest, deleteRequest, getRequest } from "../../utils/apiHelpers.js";
/* ---------- READS ---------- */
/**
 * Aggregation stats — powers the summary banner + card strip.
 * Fast query (2 DB aggregations), called first.
 */
export const fetchDashboardData = () => getRequest("/api/dashboard/auth/stats");

/**
 * List widgets — recent activity, near-completion projects, overdue items.
 * Slightly heavier (row-level queries), loaded in parallel with stats
 * but widgets panel shows its own skeleton until resolved.
 */
export const fetchDashboardWidgets = () => getRequest("/api/dashboard/auth/widgets");

