/**
 * Renders a list of entities
 * @param {Object} options
 *   - entities: Array of entities
 *   - selectedId: currently selected entity ID
 *   - container: DOM element (ul/ol)
 *   - renderItem: function(entity, selectedId) => HTMLElement
 */
export function renderEntityList({
    entities,
    selectedId,
    container,
    renderItem,
}) {
    container.innerHTML = "";

    for (const entity of entities) {
        const item = renderItem(entity, selectedId);
        if (entity.id === selectedId) item.classList.add("active");
        container.appendChild(item);
    }
}
