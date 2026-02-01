@props([
    'class' => '',
])

<img src="images/tasklyb.svg" @class([$class, 'block', 'dark:hidden']) alt="Logo" />

<img src="images/tasklyw.svg" @class([$class, 'hidden', 'dark:block']) alt="Logo" />
