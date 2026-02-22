/**
 * Copy task reference to clipboard when .copy-task-ref is clicked.
 * Used on portal task listings (developers + customers).
 */
(function() {
    function copyTaskRef(e) {
        var btn = e.target.closest(".copy-task-ref");
        if (!btn) return;
        var cell = btn.closest(".task-ref-cell");
        var ref = (btn.getAttribute("data-ref") || (cell && cell.querySelector(".task-ref-text") && cell.querySelector(".task-ref-text").textContent) || "").trim();
        if (!ref) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(ref).then(function() {
                btn.classList.add("copied");
                btn.setAttribute("title", "Copied!");
                setTimeout(function() {
                    btn.classList.remove("copied");
                    btn.setAttribute("title", "Copy reference");
                }, 1500);
            });
        } else {
            var ta = document.createElement("textarea");
            ta.value = ref;
            ta.style.position = "fixed";
            ta.style.left = "-9999px";
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand("copy");
                btn.classList.add("copied");
                btn.setAttribute("title", "Copied!");
                setTimeout(function() {
                    btn.classList.remove("copied");
                    btn.setAttribute("title", "Copy reference");
                }, 1500);
            } catch (err) {}
            document.body.removeChild(ta);
        }
    }
    document.addEventListener("click", copyTaskRef);
})();
