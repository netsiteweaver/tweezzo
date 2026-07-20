<script>
(function () {
    var customerSelect = document.getElementById('customer_id');
    var projectSelect = document.getElementById('project_id');
    var sprintWrap = document.getElementById('sprint_filter_wrap');
    var sprintSelect = document.getElementById('sprint_id');

    if (!customerSelect || !projectSelect || typeof base_url === 'undefined') {
        return;
    }

    customerSelect.addEventListener('change', function () {
        projectSelect.innerHTML = '<option value="">All</option>';
        if (sprintSelect) {
            sprintSelect.innerHTML = '<option value="">All</option>';
        }
        if (sprintWrap) {
            sprintWrap.classList.add('d-none');
        }
        var customerId = customerSelect.value;
        if (!customerId) {
            return;
        }
        $.post(base_url + 'projects/getByCustomerId', { customer_id: customerId }, function (res) {
            var data = (typeof res === 'string') ? JSON.parse(res) : res;
            if (!data || !data.data) {
                return;
            }
            data.data.forEach(function (p) {
                var opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.name;
                projectSelect.appendChild(opt);
            });
        });
    });

    projectSelect.addEventListener('change', function () {
        if (!sprintSelect || !sprintWrap) {
            return;
        }
        sprintSelect.innerHTML = '<option value="">All</option>';
        var projectId = projectSelect.value;
        if (!projectId) {
            sprintWrap.classList.add('d-none');
            return;
        }
        sprintWrap.classList.remove('d-none');
        $.post(base_url + 'sprints/getByProjectId', { project_id: projectId }, function (res) {
            var data = (typeof res === 'string') ? JSON.parse(res) : res;
            if (!data || !data.data) {
                return;
            }
            data.data.forEach(function (s) {
                var opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.name;
                sprintSelect.appendChild(opt);
            });
        });
    });
})();
</script>
