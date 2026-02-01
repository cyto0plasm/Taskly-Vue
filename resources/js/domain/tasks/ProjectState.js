import { EntityState } from "../../utils/EntityState.js";
//import render project list
//import render project details
class ProjectState extends EntityState {
    constructor() {
        super();
        this._setupSubscription();
    }

    _setupSubscription() {
        this.subscribe(({ state, entity, selectedId, selectedEntity }) => {
            //render project list
            //render project details
        });
    }
}
// Export singleton instance
const projectState = new ProjectState();
export default projectState;
