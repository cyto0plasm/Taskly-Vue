<div id="darkToggle" role="switch" aria-checked="false" tabindex="0" data-toolTip="Change Theme" data-position="bottom"
    aria-label="change Theme"
    class="w-11 h-6 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center cursor-pointer p-[2px] shadow-lg transition-colors duration-300">
    <!-- Toggle Circle -->
    <div
        class="circle w-5 h-5 bg-purple-600 dark:bg-white rounded-full flex items-center justify-center transition-transform duration-300">
        <!-- Sun/Moon Icon -->
        <svg class="w-3 h-3 fill-white dark:fill-gray-800 transition-transform duration-300 dark:rotate-180 ease-in-out"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path
                d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20zm0-1.5V3.5A8.5 8.5 0 0 1 20.5 12 8.5 8.5 0 0 1 12 20.5z" />
        </svg>
    </div>
</div>

<script>
    const container = document.getElementById("darkToggle");
    const circle = container.querySelector(".circle");
    const icon = container.querySelector("svg");

    const savedTheme = localStorage.getItem('theme');
    let isDark = savedTheme === "dark" || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);

    document.documentElement.classList.toggle("dark", isDark);
    circle.style.transform = isDark ? "translateX(20px)" : "translateX(0)";
    icon.style.transform = isDark ? "rotate(180deg)" : "rotate(0deg)";
    container.setAttribute("aria-checked", isDark);

    container.addEventListener("click", () => {
        isDark = !isDark;
        document.documentElement.classList.toggle('dark', isDark);
        circle.style.transform = isDark ? "translateX(20px)" : "translateX(0)";
        icon.style.transform = isDark ? "rotate(180deg)" : "rotate(0deg)";
        container.setAttribute("aria-checked", isDark);
        localStorage.setItem("theme", isDark ? "dark" : "light");
    });
</script>
@vite('resources/js/utils/toolTip.js')
