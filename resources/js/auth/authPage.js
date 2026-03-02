window.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("container");
    let routeOverrides = container.dataset.defaultForm;

    const tokenInputs = document.querySelectorAll('input[name="_token"]');
    tokenInputs.forEach((t) => t.setAttribute("value", t.value));

    document.body.classList.add("ready");

    const loginForm = document.getElementById("login-form");
    const registerForm = document.getElementById("register-form");
    const message = document.getElementById("message");
    const switchBtn = message.querySelector("button");

    const content = {
        login: {
            title: "Welcome Back!",
            text: "Manage your tasks, track progress, and stay productive. Sign in to continue where you left off.",
            button: "← Sign Up",
            bg: "linear-gradient(to left, #4f46e5, #3b82f6)",
            radius: "3rem 0 0 6rem",
        },
        register: {
            title: "Start Organizing Today!",
            text: "Create an account and take control of your tasks. Stay on top of deadlines and boost productivity.",
            button: "Sign In →",
            bg: "linear-gradient(to right, #16a34a, #22c55e)",
            radius: "0 3rem 6rem 0",
        },
    };

    let savedMode = localStorage.getItem("isLogin");
    let isLogin =
        routeOverrides === "register"
            ? false
            : savedMode === null
            ? true
            : savedMode === "true";

    const isMobile = () => window.innerWidth <= 768;

    function updateView(skipAnim = false) {
        const mode = isLogin ? "login" : "register";
        const config = content[mode];

        // Forms opacity
        requestAnimationFrame(() => {
            loginForm.style.opacity = isLogin ? "1" : "0";
            registerForm.style.opacity = isLogin ? "0" : "1";
        });
        loginForm.style.pointerEvents = isLogin ? "auto" : "none";
        registerForm.style.pointerEvents = isLogin ? "none" : "auto";

        if (isMobile()) {
            loginForm.style.transform = isLogin
                ? "translateY(0)"
                : "translateY(-100%)";
            registerForm.style.transform = isLogin
                ? "translateY(100%)"
                : "translateY(0)";
        } else {
            loginForm.style.transform = isLogin
                ? "translateX(0)"
                : "translateX(-100%)";
            registerForm.style.transform = isLogin
                ? "translateX(100%)"
                : "translateX(0)";
        }

        // Message content
        message.querySelector("h2").textContent = config.title;
        message.querySelector("p").textContent = config.text;
        switchBtn.textContent = config.button;

        // Gradient
        message.style.backgroundImage = config.bg;
        message.style.backgroundSize = "cover";
        message.style.backgroundRepeat = "no-repeat";

        // On mobile: message is always off-screen (forms cover it).
        // We rely solely on the in-form .switch-mobile buttons instead.
        const transform = isMobile()
            ? "translateY(-200%)"   // keep message fully off-screen on mobile
            : isLogin
            ? "translateX(100%)"
            : "translateX(0)";

        const radius = isMobile()
            ? "1.5rem"
            : config.radius;

        message.style.transform = transform;
        message.style.borderRadius = radius;
        message.style.transition = skipAnim ? "none" : "all 0.7s ease";
    }

    function toggle() {
        isLogin = !isLogin;
        localStorage.setItem("isLogin", JSON.stringify(isLogin));
        updateView();
    }

    // Desktop message panel switch button
    switchBtn.addEventListener("click", toggle);

    // Mobile in-form switch buttons
    document.querySelectorAll(".switch-mobile").forEach((btn) => {
        btn.addEventListener("click", toggle);
    });

    // Initial render without animation
    updateView(true);

    // Resize handler
    window.addEventListener("resize", () => {
        clearTimeout(window._resizeTimer);
        window._resizeTimer = setTimeout(() => updateView(true), 150);
    });
});
