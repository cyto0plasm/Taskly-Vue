import EntityState from "../../utils/EntityState.js";
import {
    renderTaskItem,
    renderTaskEmpty,
    renderTaskSkeleton,
} from "../../tasks/ui/TaskListUI.js";
import { renderTaskDetails } from "../../tasks/ui/TaskDetailsUI.js";
import { renderEntityList } from "../../tasks/ui/EntityListUi.js";

class TaskState extends EntityState {
    constructor() {
        super("task");
        this.state.loading = true;

        // subscribe to update list & details
        this.subscribe(({ state, entities, selectedEntity, selectedId }) => {
            this._renderList(entities, selectedId, state.loading);
            renderTaskDetails({ task: selectedEntity, loading: state.loading });
        });
    }

    _renderList(entities, selectedId, loading) {
        const container = document.querySelector("#sortable-list");
        if (!container) return;

        container.innerHTML = "";

        if (loading) {
            container.appendChild(renderTaskSkeleton(5));
            return;
        }

        if (!entities || entities.length === 0) {
            container.appendChild(renderTaskEmpty());
            return;
        }

        renderEntityList({
            entities,
            selectedId,
            container,
            renderItem: renderTaskItem,
        });
    }

    /** helper: add + select new task */
    addAndSelect(entity) {
        this.setEntity(entity);
        this.select(entity.id);
    }
}

// Export singleton instance
const taskState = new TaskState();
export default taskState;
