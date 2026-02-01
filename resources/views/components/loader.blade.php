<!-- LOADER -->
<div id="page-loader">
    <div class="loader-circle"></div>
</div>
<style>
    #page-loader {
        position: fixed;
        inset: 0;
        background: #fff;
        /* or dark theme */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: opacity .3s;
    }

    .loader-circle {
        width: 60px;
        height: 60px;
        border: 6px solid #ddd;
        border-top-color: #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<script>
    window.addEventListener("load", () => {
        const loader = document.getElementById("page-loader");
        loader.style.opacity = "0";
        setTimeout(() => loader.remove(), 30);
    });
</script>
