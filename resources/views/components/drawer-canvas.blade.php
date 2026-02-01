<section class=" bg-slate-100 w-[100%] h-[600px] shadow-md mx-auto my-4">
    <div id="toolBar" class="">
        <h1 class=" text-16">Task One</h1>
        <label for="stroke">Stroke</label>
        <input type="color" id="stroke" name="stroke">
        <label for="stroke">Line Width</label>
        <input type="number" id="lineWidth" name="lineWidth" value="5">
        <button id="clear">Clear</button>
        <button id="undo">Undo</button>
        <button id="save">Save</button>
    </div>
    <div>
        <canvas id="drawing-board"></canvas>
    </div>
</section>
@vite(['resources/js/tasks/drawer.js'])
