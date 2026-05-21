jQuery(function(){
    var sprintMaxDays = 14;

    function toDateInputValue(dateObj) {
        var y = dateObj.getFullYear();
        var m = String(dateObj.getMonth() + 1).padStart(2, "0");
        var d = String(dateObj.getDate()).padStart(2, "0");
        return y + "-" + m + "-" + d;
    }

    function addDaysToDateString(dateStr, days) {
        if (!dateStr) {
            return "";
        }
        var parts = dateStr.split("-");
        var dt = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        dt.setDate(dt.getDate() + days);
        return toDateInputValue(dt);
    }

    function maxEndDateForStart(startStr) {
        if (!startStr) {
            return "";
        }
        return addDaysToDateString(startStr, sprintMaxDays);
    }

    function syncSprintEndConstraints() {
        var startVal = $('input[name="start_date"]').val();
        var $end = $('input[name="end_date"]');
        if (!startVal) {
            $end.removeAttr("min max");
            return;
        }
        var maxEnd = maxEndDateForStart(startVal);
        $end.attr("min", startVal);
        $end.attr("max", maxEnd);
        if ($end.val() && ($end.val() < startVal || $end.val() > maxEnd)) {
            $end.val(maxEnd);
        }
    }

    $('input[name="start_date"]').on("change", function(){
        syncSprintEndConstraints();
        var startVal = $(this).val();
        var $end = $('input[name="end_date"]');
        if (!startVal) {
            return;
        }
        if (!$end.val() || $end.val() < startVal) {
            $end.val(startVal);
        }
    });

    $('input[name="end_date"]').on("change", syncSprintEndConstraints);
    syncSprintEndConstraints();

    $("#add_user").on("submit", function(e){
        var startVal = $('input[name="start_date"]').val();
        var endVal = $('input[name="end_date"]').val();
        if ((startVal && !endVal) || (!startVal && endVal)) {
            e.preventDefault();
            alert("Set both sprint start and end dates, or clear both.");
            return;
        }
        if (startVal && endVal) {
            var maxEnd = maxEndDateForStart(startVal);
            if (endVal < startVal || endVal > maxEnd) {
                e.preventDefault();
                alert("Sprint duration cannot exceed " + sprintMaxDays + " days.");
            }
        }
    });

    function updateValidationHelp() {
        var enabled = $("#ready_for_validation").is(":checked");
        var $help = $("#ready_for_validation_help");
        if ($help.length === 0) return;

        if (enabled) {
            $help
                .removeClass("text-muted")
                .addClass("text-success")
                .text("Weekly reminders are enabled for this sprint.");
            return;
        }

        $help
            .removeClass("text-success")
            .addClass("text-muted")
            .text("Keep this OFF while tasks are still being pushed to staging.");
    }

    $("#ready_for_validation").on("change", updateValidationHelp);
    updateValidationHelp();

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
