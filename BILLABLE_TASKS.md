---
name: Task work type and billable
overview: Add two task classification fields (work_type and billable) to the tasks table, wire them through the model and controller, and expose them on task add/edit, view, and listing with filters. Portal visibility is optional.
todos: []
isProject: false
---

# Task classification: work_type and billable (Option B)

## Data model

Add two columns to `tasks`:

- **work_type** – `ENUM('development','maintenance','support','other')` NULL (existing tasks stay unset).
- **billable** – `TINYINT(1)` NULL (0 = no, 1 = yes; NULL = not set).

Nulls allow existing rows to remain unchanged and optional behaviour in filters.

---

## 1. Migration

**New file:** [application/migrations/047_TaskWorkTypeAndBillable.php](application/migrations/047_TaskWorkTypeAndBillable.php)

- `up()`: `ALTER TABLE tasks` add `work_type` and `billable` (e.g. after `estimated_hours` or `section`).
- `down()`: drop both columns.

---

## 2. Model

**[application/models/Tasks_model.php](application/models/Tasks_model.php)**

- **save()** (around 264–273): add `$this->db->set('work_type', ...)` and `$this->db->set('billable', ...)` from `$data` (treat empty as NULL for work_type; for billable use 1 when checked, 0 or NULL otherwise). Apply for both insert and update branches.
- **fetchAll()**: add two optional parameters `$work_type=""`, `$billable=""` (or extend the existing optional params). If `$work_type` non-empty, `$this->db->where('t.work_type', $work_type)`. If `$billable` is `'1'` or `'0'`, `$this->db->where('t.billable', $billable)`.
- **totalRows()**: add the same two params and pass them through to `fetchAll(..., true)`.

No change needed for **fetchSingle()**; it selects `t.`* so the new columns will be present.

---

## 3. Controller

**[application/controllers/Tasks.php](application/controllers/Tasks.php)**

- **listing()**: read `$work_type = $this->input->get('work_type');` and `$billable = $this->input->get('billable');`. Pass them into `fetchAll()` and `totalRows()` (extend the existing parameter list to include these two after `search_text` or in the appropriate position).

**save()**: no change; it uses `$data = $this->input->post()` and the model will read `work_type` and `billable` from `$data`.

---

## 4. Admin views

**[application/views/tasks/add.php](application/views/tasks/add.php)**  
After the “Estimated Hours” block (or after Section), add:

- **Work type:** `<select name="work_type">` with options: empty (Select), development, maintenance, support, other.
- **Billable:** `<input type="checkbox" name="billable" value="1">` (when unchecked, nothing or 0 is sent; handle in model so 0 or NULL is stored).

**[application/views/tasks/edit.php](application/views/tasks/edit.php)**  
Same fields, with values from `$task->work_type` and `$task->billable` (pre-select option, check checkbox if billable == 1).

**[application/views/tasks/view.php](application/views/tasks/view.php)**  
After the Stage dropdown (around line 103), add read-only display:

- Work type: show `$task->work_type` (or “—” if empty).
- Billable: show “Yes” / “No” (or “—” if null) from `$task->billable`.

**[application/views/tasks/listing.php](application/views/tasks/listing.php)**  

- **Filters row:** add two dropdowns (same pattern as “Notes” / “assigned_to”):
  - Work type: id `work_type`, name `work_type`, class `monitor`, options All | Development | Maintenance | Support | Other; value from `$this->input->get('work_type')`.
  - Billable: id `billable`, name `billable`, class `monitor`, options All | Yes | No; value from `$this->input->get('billable')`.
- **Table:** add one or two columns (e.g. “Work type” and “Billable”) in the header and in the row loop, outputting `$task->work_type` and “Yes”/“No”/“—” for `$task->billable`. Place after Section or Stage so the table stays readable.

---

## 5. Listing URL and JS

**[assets/js/pages/tasks_listing.js](assets/js/pages/tasks_listing.js)**  
The listing URL is built in two places (and any other place that redirects to the tasks listing with query params):

- In the `.monitor` change handler (around 383–418): add `let work_type = $('#work_type').val();` and `let billable = $('#billable').val();`, then append `"&work_type="+work_type+"&billable="+billable` to `window.location.href`.
- In the `.applyChosenStages` block (and any similar “Apply” that rebuilds the listing URL): include `work_type` and `billable` in the same way.

Ensure the redirect URL always includes the new params so filters persist.

---

## 6. Portal (optional)

- **Developer portal** [application/views/portal/developers/tasks.php](application/views/portal/developers/tasks.php) and **task view** (if any): optionally add a column or line for work type and billable. No filter needed unless you want it.
- **Customer portal** [application/views/portal/customers/tasks.php](application/views/portal/customers/tasks.php): same optional display; often billable is hidden from clients.
- **Developersportal_model** and **Customersportal_model**: if you show the fields, the existing select of `t.`* (or equivalent) already returns new columns once the migration has run; no change required unless the portal queries use an explicit column list that omits them.

Recommendation: implement admin first; add portal columns/views in a follow-up if needed.

---

## 7. Order of work

1. Create and run migration 047 (add columns).
2. Update Tasks_model (save, fetchAll, totalRows).
3. Update Tasks controller listing().
4. Update tasks add, edit, view, and listing views.
5. Update tasks_listing.js for filter persistence.
6. (Optional) Expose work_type/billable on portal task list/view.

---

## Summary


| Area             | Change                                                                            |
| ---------------- | --------------------------------------------------------------------------------- |
| DB               | `work_type` ENUM, `billable` TINYINT(1), both nullable                            |
| Tasks_model      | save(): set both; fetchAll/totalRows(): optional filter by work_type and billable |
| Tasks controller | listing(): read get('work_type'), get('billable'); pass to model                  |
| tasks/add, edit  | Dropdown work_type + checkbox billable                                            |
| tasks/view       | Read-only work type and billable                                                  |
| tasks/listing    | Two filter dropdowns + two table columns; URL includes work_type, billable        |
| tasks_listing.js | Append work_type and billable to listing URL in all redirects                     |
| Portal           | Optional: show columns/fields; no filter required initially                       |


After this, you can filter tasks by work type and billable status and use “billable” tasks for invoicing or reporting (e.g. export or report by `billable = 1`).