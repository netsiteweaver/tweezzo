jQuery(function(){
    function getNamePrefix(nameValue) {
        var name = (nameValue || "").toUpperCase().replace(/[^A-Z0-9\s]/g, " ").trim();
        if (name === "") {
            return "PR";
        }

        var parts = name.split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
            return ((parts[0].charAt(0) || "") + (parts[1].charAt(0) || "")).replace(/[^A-Z0-9]/g, "") || "PR";
        }

        return parts[0].substring(0, 2).replace(/[^A-Z0-9]/g, "") || "PR";
    }

    function generateProjectCode() {
        var namePrefix = getNamePrefix($('input[name="name"]').val()).slice(0, 2);
        var random = Math.random().toString(36).toUpperCase().replace(/[^A-Z0-9]/g, "");
        var suffix = random.slice(0, 4);
        if (suffix.length < 4) {
            suffix = (suffix + Date.now().toString(36).toUpperCase()).slice(0, 4);
        }
        return (namePrefix + suffix).padEnd(6, "X").slice(0, 6);
    }

    function checkProjectCodeAvailable(code, uuid) {
        return $.ajax({
            url: base_url + "projects/checkCodeAvailable",
            method: "POST",
            dataType: "JSON",
            data: {
                code: code,
                uuid: uuid
            }
        });
    }

    $("#generate_project_code").on("click", async function(){
        var $btn = $(this);
        var uuid = $('input[name="uuid"]').val() || "";
        $btn.prop("disabled", true);

        var attempts = 0;
        var maxAttempts = 8;
        var foundCode = "";

        while (attempts < maxAttempts) {
            attempts++;
            var candidate = generateProjectCode();
            try {
                var response = await checkProjectCodeAvailable(candidate, uuid);
                if (response && response.result === true && response.exists === false) {
                    foundCode = candidate;
                    break;
                }
            } catch (e) {
                break;
            }
        }

        $btn.prop("disabled", false);

        if (foundCode !== "") {
            $('input[name="code"]').val(foundCode).trigger("input").focus();
            return;
        }

        if (typeof toastr !== "undefined") {
            toastr.warning("Could not generate an available code right now. Please try again.");
        }
    });
});
