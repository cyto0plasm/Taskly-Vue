    <li id="task-{{ $task->id }}">
        <x-task-button :state="$task->status" :title="$task->title" :task-id="$task->id" :url="route('tasks.show', $task->id)" />
    </li>
