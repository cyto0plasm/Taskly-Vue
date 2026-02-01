export function buildFormData(form) {
    return new FormData(form);
}

export function formDataToObject(formData) {
    return Object.fromEntries(formData.entries());
}
