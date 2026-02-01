export default class EntityState {
    constructor(name = "entity") {
        this.name = name;
        this.entities = new Map();
        this.selectedId = null;
        this.state = {
            loading: false,
            updating: false,
            deleting: false,
            empty: false,
        };
        this.listeners = new Set();
    }

    /* ---------------- entity operations ---------------- */

    setMany(arr = []) {
        this.entities.clear();
        arr.forEach((e) => e?.id && this.entities.set(e.id, e));
        this.selectedId = arr[0]?.id || null;
        this.state.empty = arr.length === 0;
        this._notify();
        return this;
    }

    setEntity(entity) {
        if (!entity?.id) return this;
        this.entities.set(entity.id, entity);
        this.selectedId ??= entity.id;
        this.state.empty = false;
        this._notify();
        return this;
    }

    delete(id) {
        this.entities.delete(id);
        if (this.selectedId === id) {
            const first = this.entities.keys().next().value;
            this.selectedId = first ?? null;
        }
        this.state.empty = this.entities.size === 0;
        this._notify();
        return this;
    }

    select(id, { showLoading = false } = {}) {
        if (showLoading) this.state.loading = true;
        this.selectedId = id;
        this._notify();
        return this;
    }

    setState(meta = {}) {
        Object.assign(this.state, meta);
        this._notify();
        return this;
    }

    setLoading(loading = false) {
        this.state.loading = loading;
        this._notify();
        return this;
    }

    /* ---------------- getters ---------------- */

    get(id) {
        return this.entities.get(id) || null;
    }

    getSelected() {
        return this.get(this.selectedId);
    }

    has(id) {
        return this.entities.has(id);
    }

    /* ---------------- subscriptions ---------------- */

    subscribe(fn) {
        this.listeners.add(fn);
        fn(this._snapshot()); // immediate sync
        return () => this.listeners.delete(fn);
    }

    _notify() {
        const snapshot = this._snapshot();
        this.listeners.forEach((fn) => fn(snapshot));
    }

    _snapshot() {
        return {
            state: { ...this.state },
            entities: [...this.entities.values()], // array for convenience
            selectedEntity: this.getSelected(),
            selectedId: this.selectedId,
        };
    }

    /* ---------------- ui helpers ---------------- */

    showFlash(type, message) {
        window.dispatchEvent(
            new CustomEvent("flash", {
                detail: {
                    type,
                    message,
                    entity: this.name,
                },
            })
        );
    }
}
