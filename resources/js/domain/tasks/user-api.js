// user-api.js
import { getRequest, patchRequest } from "../../utils/apiHelpers.js";

export const fetchUser = () =>
    getRequest("/api/user");

export const updateUser = (data) =>
    patchRequest("/api/user", data);

export const updateUserPreferences = (data) =>
    patchRequest("/api/user/preferences", data);
