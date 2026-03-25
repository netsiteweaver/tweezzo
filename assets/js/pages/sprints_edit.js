jQuery(function(){
    function getProjectPrefix() {
        var selectedText = $('select[name="project_id"] option:selected').text() || "";
        var projectName = selectedText.split("/")[0].trim().toUpperCase().replace(/[^A-Z0-9\s]/g, " ");
        if (projectName === "" || projectName === "SELECT") {
            return "SP";
        }

        var parts = projectName.split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
            return ((parts[0].charAt(0) || "") + (parts[1].charAt(0) || "")).replace(/[^A-Z0-9]/g, "") || "SP";
        }

        return projectName.substring(0, 2).replace(/[^A-Z0-9]/g, "") || "SP";
    }

    function generateSprintCode() {
        var prefix = getProjectPrefix().slice(0, 2);
        var random = Math.random().toString(36).toUpperCase().replace(/[^A-Z0-9]/g, "");
        var suffix = random.slice(0, 4);
        if (suffix.length < 4) {
            suffix = (suffix + Date.now().toString(36).toUpperCase()).slice(0, 4);
        }
        return (prefix + suffix).padEnd(6, "S").slice(0, 6);
    }

    function checkSprintCodeAvailable(projectId, code, uuid) {
        return $.ajax({
            url: base_url + "sprints/checkCodeAvailable",
            method: "POST",
            dataType: "JSON",
            data: {
                project_id: projectId,
                code: code,
                uuid: uuid
            }
        });
    }

    $("#generate_sprint_code").on("click", async function(){
        var projectId = $('select[name="project_id"]').val();
        if (!projectId) {
            if (typeof toastr !== "undefined") {
                toastr.info("Select a project first.");
            }
            $('select[name="project_id"]').trigger("focus");
            return;
        }

        var $btn = $(this);
        var uuid = $('input[name="uuid"]').val() || "";
        $btn.prop("disabled", true);

        var attempts = 0;
        var maxAttempts = 8;
        var foundCode = "";

        while (attempts < maxAttempts) {
            attempts++;
            var candidate = generateSprintCode();
            try {
                var response = await checkSprintCodeAvailable(projectId, candidate, uuid);
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
