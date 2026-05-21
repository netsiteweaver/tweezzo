jQuery(function(){
    var sprintNamePattern = /^Sprint\s+\d+$/i;

    function setSprintNameActive(isActive) {
        var $nameInput = $('input[name="name"]');
        $nameInput.prop("disabled", !isActive);
        if (!isActive) {
            $nameInput.val("");
            $("#sprint_name_hint").text("");
        }
    }

    function fetchSuggestedSprintName(projectId) {
        return $.ajax({
            url: base_url + "sprints/suggestName",
            method: "POST",
            dataType: "JSON",
            data: { project_id: projectId }
        });
    }

    async function applySuggestedSprintName(projectId, forceReplace) {
        if (!projectId) {
            return;
        }
        try {
            var response = await fetchSuggestedSprintName(projectId);
            if (!response || response.result !== true || !response.name) {
                return;
            }
            var $nameInput = $('input[name="name"]');
            if (forceReplace || $nameInput.val().trim() === "") {
                $nameInput.val(response.name);
            }
            $nameInput.attr("placeholder", response.name);
            $("#sprint_name_hint").text("Suggested: " + response.name);
        } catch (e) {
            // ignore
        }
    }

    function checkSprintNameExists(projectId, name) {
        return $.ajax({
            url: base_url + "sprints/checkSprintExists",
            method: "POST",
            dataType: "JSON",
            data: {
                project_id: projectId,
                name: name
            }
        });
    }

    function checkEmptySprints(projectId) {
        return $.ajax({
            url: base_url + "sprints/checkEmptySprints",
            method: "POST",
            dataType: "JSON",
            data: { project_id: projectId }
        });
    }

    function promptEmptySprintsListing(response) {
        var hasEmpty = response && (
            response.has_empty
            || (response.empty_sprints && response.empty_sprints.length)
        );
        if (!hasEmpty) {
            return false;
        }
        var message = response.reason || "This project already has empty sprint(s) with no tasks.";
        $("#sprint_name_hint").text(message);

        var confirmMessage = message + "\n\nView the sprint list now?";
        if (response.listing_url && window.confirm(confirmMessage)) {
            window.location.href = response.listing_url;
            return true;
        }

        if (typeof toastr !== "undefined") {
            toastr.error(message);
        }
        return true;
    }

    async function guardAgainstEmptySprints(projectId) {
        if (!projectId) {
            return false;
        }
        try {
            var response = await checkEmptySprints(projectId);
            return promptEmptySprintsListing(response);
        } catch (e) {
            return false;
        }
    }

    function getProjectPrefix() {
        var selectedText = $('select[name="project_id"] option:selected').text() || "";
        var projectName = selectedText.split("/")[0].trim().toUpperCase().replace(/[^A-Z0-9\s]/g, " ");
        if (projectName === "" || projectName === "SELECT") {
            return "S";
        }

        var parts = projectName.split(/\s+/).filter(Boolean);
        if (parts.length >= 2) {
            return ((parts[0].charAt(0) || "") + (parts[1].charAt(0) || "")).replace(/[^A-Z0-9]/g, "") || "S";
        }

        return projectName.substring(0, 2).replace(/[^A-Z0-9]/g, "") || "S";
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

    function checkSprintCodeAvailable(projectId, code) {
        return $.ajax({
            url: base_url + "sprints/checkCodeAvailable",
            method: "POST",
            dataType: "JSON",
            data: {
                project_id: projectId,
                code: code
            }
        });
    }

    async function generateAvailableSprintCode(showProjectMessage) {
        var projectId = $('select[name="project_id"]').val();
        if (!projectId) {
            $('input[name="code"]').val("");
            setSprintNameActive(false);
            if (showProjectMessage && typeof toastr !== "undefined") {
                toastr.info("Select a project first.");
            }
            if (showProjectMessage) {
                $('select[name="project_id"]').trigger("focus");
            }
            return;
        }

        var $btn = $("#generate_sprint_code");
        $btn.prop("disabled", true);

        var attempts = 0;
        var maxAttempts = 8;
        var foundCode = "";

        while (attempts < maxAttempts) {
            attempts++;
            var candidate = generateSprintCode();
            try {
                var response = await checkSprintCodeAvailable(projectId, candidate);
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
            if (await guardAgainstEmptySprints(projectId)) {
                $('input[name="code"]').val("");
                setSprintNameActive(false);
                return;
            }
            $('input[name="code"]').val(foundCode).trigger("input");
            setSprintNameActive(true);
            await applySuggestedSprintName(projectId, true);
            $('input[name="name"]').trigger("focus");
            return;
        }

        setSprintNameActive(false);
        if (typeof toastr !== "undefined") {
            toastr.warning("Could not generate an available code right now. Please try again.");
        }
    }

    $("#generate_sprint_code").on("click", async function(){
        await generateAvailableSprintCode(true);
    });

    $('select[name="project_id"]').on("change", async function(){
        $('input[name="code"]').val("");
        setSprintNameActive(false);
        await generateAvailableSprintCode(false);
    });

    $("#add_user").on("submit", async function(e){
        var projectId = $('select[name="project_id"]').val();
        if (!projectId) {
            return;
        }
        if (await guardAgainstEmptySprints(projectId)) {
            e.preventDefault();
        }
    });

    $('input[name="name"]').on("blur", async function(){
        var projectId = $('select[name="project_id"]').val();
        var name = $(this).val().trim();
        if (!projectId || !name || !sprintNamePattern.test(name)) {
            return;
        }
        try {
            var response = await checkSprintNameExists(projectId, name);
            if (response && response.result === true && response.exists === true && response.suggested_name) {
                $("#sprint_name_hint").text("Already exists. Try: " + response.suggested_name);
                if (typeof toastr !== "undefined") {
                    toastr.warning('Sprint "' + name + '" already exists. Suggested: ' + response.suggested_name);
                }
            }
        } catch (e) {
            // ignore
        }
    });

    setSprintNameActive(false);
    $('select[name="project_id"]').trigger("focus");
    generateAvailableSprintCode(false);
});
