const IGNORE_KEYS = ["_token", "_method"];

export function hasTaskChanges(task, formData) {
    if (!task) return true;

    for (const [key, value] of formData.entries()) {
        if (IGNORE_KEYS.includes(key)) continue;

        const current = task[key] ?? "";
        if (String(current) !== String(value)) return true;
    }
    return false;
}
