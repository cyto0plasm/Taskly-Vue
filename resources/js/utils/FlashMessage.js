export default class Flash {
    constructor() {
        this.queue = [];
        this.isShowing = false;
        this.currentMessage = null;
        this.messageCount = 0;

        let flash = document.getElementById("flash-message");
        if (!flash) {
            flash = document.createElement("div");
            flash.id = "flash-message";
            flash.className = "hidden";
            document.body.appendChild(flash);
        }
        this.flash = flash;
    }

    show(type, message, duration = 3000) {
        // Check if this message is already in the queue
        const existingIndex = this.queue.findIndex(
            item => item.type === type && item.message === message
        );

        if (existingIndex !== -1) {
            // Increment the count for existing message
            this.queue[existingIndex].count = (this.queue[existingIndex].count || 1) + 1;

            // If this message is currently showing, update it immediately
            if (this.isShowing &&
                this.currentMessage?.type === type &&
                this.currentMessage?.message === message) {
                this.messageCount++;
                this.updateDisplay(type, message, this.messageCount);
            }
            return;
        }

        // Add new message to queue
        this.queue.push({ type, message, duration, count: 1 });
        if (!this.isShowing) this.next();
    }

    updateDisplay(type, message, count) {
        const displayMessage = count > 1 ? `${message} (${count})` : message;
        this.flash.textContent = displayMessage;
    }

    next() {
        if (this.queue.length === 0) {
            this.isShowing = false;
            this.currentMessage = null;
            this.messageCount = 0;
            return;
        }

        this.isShowing = true;
        const { type, message, duration, count } = this.queue.shift();
        this.currentMessage = { type, message };
        this.messageCount = count || 1;

        const displayMessage = this.messageCount > 1
            ? `${message} (${this.messageCount})`
            : message;

        this.flash.textContent = displayMessage;
        this.flash.className =
            "fixed top-16 left-1/2 -translate-x-1/2 px-4 py-2 rounded shadow-md text-white opacity-0 transition-opacity duration-500 z-50";
        const typeClass =
            {
                success: "bg-green-500",
                error: "bg-red-500",
                info: "bg-blue-500",
            }[type] || "bg-gray-500";
        this.flash.classList.add(typeClass);
        this.flash.classList.remove("hidden");

        requestAnimationFrame(() => this.flash.classList.remove("opacity-0"));

        setTimeout(() => {
            this.flash.classList.add("opacity-0");
            setTimeout(() => {
                this.flash.classList.add("hidden");
                this.next();
            }, 500);
        }, duration);
    }
}
