document.addEventListener("DOMContentLoaded", () => {
    // Get elements
    const canvas = document.getElementById("drawing-board"); // keep your HTML ID
    const ctx = canvas.getContext("2d");

    // Set canvas size
    canvas.width = window.innerWidth - 150;
    canvas.height = 500;

    // Tool state
    let drawing = false;
    let strokes = [];
    let currentStroke = null;
    let tool = "pen";

    const strokeColor = document.getElementById("stroke");
    const lineWidth = document.getElementById("lineWidth");

    // Mouse events
    canvas.addEventListener("mousedown", startDraw);
    canvas.addEventListener("mousemove", draw);
    canvas.addEventListener("mouseup", stopDraw);
    canvas.addEventListener("mouseleave", stopDraw);

    // Optional: touch events for mobile
    canvas.addEventListener("touchstart", (e) => startDraw(e.touches[0]));
    canvas.addEventListener("touchmove", (e) => {
        draw(e.touches[0]);
        e.preventDefault(); // prevent scrolling
    });
    canvas.addEventListener("touchend", stopDraw);

    // Start drawing
    function startDraw(e) {
        drawing = true;
        currentStroke = {
            tool: tool,
            color: strokeColor.value,
            size: parseInt(lineWidth.value, 10),
            points: [{ x: e.offsetX, y: e.offsetY }],
        };
        strokes.push(currentStroke);
    }

    // Draw stroke
    function draw(e) {
        if (!drawing) return;
        currentStroke.points.push({ x: e.offsetX, y: e.offsetY });
        redraw();
    }

    // Stop drawing
    function stopDraw() {
        drawing = false;
        currentStroke = null;
    }

    // Redraw all strokes
    function redraw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        strokes.forEach((stroke) => {
            ctx.beginPath();
            ctx.globalCompositeOperation =
                stroke.tool === "eraser" ? "destination-out" : "source-over";
            ctx.strokeStyle = stroke.color;
            ctx.lineWidth = stroke.size;
            ctx.lineCap = "round";

            stroke.points.forEach((p, i) => {
                if (i === 0) ctx.moveTo(p.x, p.y);
                else ctx.lineTo(p.x, p.y);
            });

            ctx.stroke();
            ctx.closePath();
        });
    }

    // Toolbar buttons
    document.getElementById("clear").addEventListener("click", () => {
        strokes = [];
        redraw();
    });

    document.getElementById("save").addEventListener("click", () => {
        localStorage.setItem("drawing", JSON.stringify(strokes));
        alert("Drawing saved!");
    });

    document.getElementById("undo").addEventListener("click", () => {
        strokes.pop();
        redraw();
    });

    // Tool buttons (Pen / Eraser)
    document
        .getElementById("penBtn")
        ?.addEventListener("click", () => (tool = "pen"));
    document
        .getElementById("eraserBtn")
        ?.addEventListener("click", () => (tool = "eraser"));

    // Load saved drawing
    function loadDrawing() {
        const saved = localStorage.getItem("drawing");
        if (saved) {
            strokes = JSON.parse(saved);
            redraw();
        }
    }

    loadDrawing();
});
