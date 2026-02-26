import { nextTick } from "vue";

export function scrollToTask(taskListRef, taskId) {
    nextTick(() => {
        if (!taskListRef.value) return;

        const el = document.getElementById(`task-${taskId}`);
        if (!el) return;

        const scrollTop =
            el.offsetTop -
            taskListRef.value.clientHeight / 2 +
            el.offsetHeight / 2;

        taskListRef.value.scrollTo({
            top: scrollTop,
            behavior: "smooth",
        });
    });
}
