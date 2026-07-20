<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller'] = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method'] = $this->uri->segment(2, "index");
        $this->load->model('Reports_model');
        $this->load->model('accesscontrol_model');
        $this->data['perms']['developer'] = $this->accesscontrol_model->authorised('reports', 'developer');
        $this->data['perms']['client'] = $this->accesscontrol_model->authorised('reports', 'client');
        $this->data['perms']['saved'] = $this->accesscontrol_model->authorised('reports', 'saved');
        $this->data['perms']['save'] = $this->accesscontrol_model->authorised('reports', 'save');
        $this->data['perms']['view_saved'] = $this->accesscontrol_model->authorised('reports', 'view_saved');
        $this->data['perms']['delete_saved'] = $this->accesscontrol_model->authorised('reports', 'delete_saved');
    }

    public function index()
    {
        if ($this->data['perms']['saved']) {
            redirect(base_url('reports/saved'));
        }
        if ($this->data['perms']['developer']) {
            redirect(base_url('reports/developer'));
        }
        if ($this->data['perms']['client']) {
            redirect(base_url('reports/client'));
        }
        redirect(base_url('dashboard'));
    }

    public function developer()
    {
        if (!isAuthorised(get_class(), 'developer')) {
            return false;
        }

        $filters = $this->collectFilters();
        $rate = $this->parseRate();
        $currency = $this->parseCurrency();
        $generate = $this->shouldGenerate($filters['developer_id'], $rate);

        $this->mybreadcrumb->add('Reports', base_url('reports/saved'));
        $this->mybreadcrumb->add('Developer', base_url('reports/developer'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = 'Developer timesheet report';

        $this->loadFilterLookups($filters);
        $this->data['filters'] = $filters;
        $this->data['rate'] = $rate;
        $this->data['currency'] = $currency;
        $this->data['generated'] = $generate;
        $this->data['rows'] = [];
        $this->data['totals'] = $this->emptyTotals();
        $this->data['subject'] = null;

        if ($generate) {
            $rows = $this->Reports_model->developerTimesheetRows($filters);
            $this->data['rows'] = $rows;
            $this->data['totals'] = $this->sumDeveloperRows($rows, $rate);
            $this->data['subject'] = $this->findDeveloper($filters['developer_id']);
        }

        if ($this->input->get('output') === 'pdf' && $generate) {
            $this->streamPdf('reports/pdf_developer', 'developer-timesheet-report', $this->data);
            return;
        }

        $this->data['content'] = $this->load->view('reports/developer', $this->data, true);
        $this->load->view('layouts/default', $this->data);
    }

    public function client()
    {
        if (!isAuthorised(get_class(), 'client')) {
            return false;
        }

        $filters = $this->collectFilters();
        $filters['billable_only'] = $this->input->get('billable_only') ? 1 : 0;
        $rate = $this->parseRate();
        $currency = $this->parseCurrency();
        $generate = $this->shouldGenerate($filters['customer_id'], $rate);

        $this->mybreadcrumb->add('Reports', base_url('reports/saved'));
        $this->mybreadcrumb->add('Client', base_url('reports/client'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = 'Client timesheet report';

        $this->loadFilterLookups($filters);
        $this->data['filters'] = $filters;
        $this->data['rate'] = $rate;
        $this->data['currency'] = $currency;
        $this->data['generated'] = $generate;
        $this->data['rows'] = [];
        $this->data['totals'] = $this->emptyTotals();
        $this->data['subject'] = null;

        if ($generate) {
            $rows = $this->Reports_model->clientTaskHours($filters);
            $this->data['rows'] = $rows;
            $this->data['totals'] = $this->sumClientRows($rows, $rate);
            $this->data['subject'] = $this->findCustomer($filters['customer_id']);
        }

        if ($this->input->get('output') === 'pdf' && $generate) {
            $this->streamPdf('reports/pdf_client', 'client-timesheet-report', $this->data);
            return;
        }

        $this->data['content'] = $this->load->view('reports/client', $this->data, true);
        $this->load->view('layouts/default', $this->data);
    }

    public function save()
    {
        if (!isAuthorised(get_class(), 'save')) {
            return false;
        }

        $reportType = $this->input->post('report_type');
        if (!in_array($reportType, ['developer', 'client'], true)) {
            flashDanger('Invalid report type.');
            redirect(base_url('reports/saved'));
            return;
        }

        if ($reportType === 'developer' && !$this->data['perms']['developer']) {
            flashDanger('Not authorised to save developer reports.');
            redirect(base_url('reports/saved'));
            return;
        }
        if ($reportType === 'client' && !$this->data['perms']['client']) {
            flashDanger('Not authorised to save client reports.');
            redirect(base_url('reports/saved'));
            return;
        }

        $filters = [
            'from'         => $this->input->post('from'),
            'to'           => $this->input->post('to'),
            'developer_id' => $this->input->post('developer_id'),
            'customer_id'  => $this->input->post('customer_id'),
            'project_id'   => $this->input->post('project_id'),
            'sprint_id'    => $this->input->post('sprint_id'),
            'billable_only'=> $this->input->post('billable_only') ? 1 : 0,
        ];
        $rate = (float) $this->input->post('rate');
        $currency = trim((string) $this->input->post('currency'));
        if ($currency === '') {
            $currency = 'MUR';
        } else {
            $currency = strtoupper($currency);
        }
        $title = trim((string) $this->input->post('title'));

        if ($reportType === 'developer') {
            if (empty($filters['developer_id']) || $rate < 0) {
                flashDanger('Developer and rate are required to save.');
                redirect(base_url('reports/developer'));
                return;
            }
            $rows = $this->Reports_model->developerTimesheetRows($filters);
            $totals = $this->sumDeveloperRows($rows, $rate);
            $subject = $this->findDeveloper($filters['developer_id']);
            $lines = $this->buildDeveloperLines($rows, $rate);
            $subjectName = $subject ? $subject->name : '';
            $subjectEmail = $subject ? $subject->email : '';
            if ($title === '') {
                $title = 'Developer — ' . $subjectName . ' (' . $filters['from'] . ' to ' . $filters['to'] . ')';
            }
        } else {
            if (empty($filters['customer_id']) || $rate < 0) {
                flashDanger('Customer and rate are required to save.');
                redirect(base_url('reports/client'));
                return;
            }
            $rows = $this->Reports_model->clientTaskHours($filters);
            $totals = $this->sumClientRows($rows, $rate);
            $subject = $this->findCustomer($filters['customer_id']);
            $lines = $this->buildClientLines($rows, $rate);
            $subjectName = $subject ? $subject->company_name : '';
            $subjectEmail = $subject && !empty($subject->email) ? $subject->email : '';
            if ($title === '') {
                $title = 'Client — ' . $subjectName . ' (' . $filters['from'] . ' to ' . $filters['to'] . ')';
            }
        }

        $result = $this->Reports_model->saveReport($reportType, [
            'title'         => $title,
            'date_from'     => $filters['from'],
            'date_to'       => $filters['to'],
            'developer_id'  => $filters['developer_id'],
            'customer_id'   => $filters['customer_id'],
            'project_id'    => $filters['project_id'],
            'sprint_id'     => $filters['sprint_id'],
            'billable_only' => $filters['billable_only'],
            'rate'          => $rate,
            'currency'      => $currency,
            'subject_name'  => $subjectName,
            'subject_email' => $subjectEmail,
            'total_minutes' => $totals['minutes'],
            'total_hours'   => $totals['hours'],
            'total_amount'  => $totals['amount'],
            'entry_count'   => $totals['entries'],
        ], $lines);

        if (!$result['result']) {
            flashDanger(!empty($result['reason']) ? $result['reason'] : 'Could not save report.');
            redirect(base_url('reports/' . $reportType));
            return;
        }

        flashSuccess('Report saved as ' . $result['report_code'] . '.');
        redirect(base_url('reports/view_saved/' . $result['uuid']));
    }

    public function saved()
    {
        if (!isAuthorised(get_class(), 'saved')) {
            return false;
        }

        $type = $this->input->get('type');
        if (!in_array($type, ['developer', 'client'], true)) {
            $type = '';
        }

        $this->mybreadcrumb->add('Reports', base_url('reports/saved'));
        $this->mybreadcrumb->add('Saved', base_url('reports/saved'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = 'Saved reports';
        $this->data['type'] = $type;
        $this->data['reports'] = $this->Reports_model->listSaved($type);

        $this->data['content'] = $this->load->view('reports/saved', $this->data, true);
        $this->load->view('layouts/default', $this->data);
    }

    public function view_saved()
    {
        if (!isAuthorised(get_class(), 'view_saved')) {
            return false;
        }

        $uuid = $this->uri->segment(3);
        $report = $this->Reports_model->getSavedByUuid($uuid);
        if (empty($report)) {
            flashDanger('Saved report not found.');
            redirect(base_url('reports/saved'));
            return;
        }

        $this->data['report'] = $report;
        $this->data['filters'] = [
            'from' => $report->date_from,
            'to'   => $report->date_to,
        ];
        $this->data['rate'] = (float) $report->rate;
        $this->data['currency'] = $report->currency;
        $this->data['totals'] = [
            'minutes' => (int) $report->total_minutes,
            'hours'   => (float) $report->total_hours,
            'amount'  => (float) $report->total_amount,
            'entries' => (int) $report->entry_count,
        ];
        $this->data['subject'] = (object) [
            'name'         => $report->subject_name,
            'email'        => $report->subject_email,
            'company_name' => $report->subject_name,
        ];

        if ($this->input->get('output') === 'pdf') {
            $view = $report->report_type === 'developer' ? 'reports/pdf_saved_developer' : 'reports/pdf_saved_client';
            $prefix = $report->report_type === 'developer' ? 'developer-timesheet-report' : 'client-timesheet-report';
            $this->streamPdf($view, $prefix, $this->data);
            return;
        }

        $this->mybreadcrumb->add('Reports', base_url('reports/saved'));
        $this->mybreadcrumb->add('Saved', base_url('reports/saved'));
        $this->mybreadcrumb->add('View', base_url('reports/view_saved/' . $uuid));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = !empty($report->title) ? $report->title : 'Saved report';

        $this->data['content'] = $this->load->view('reports/view_saved', $this->data, true);
        $this->load->view('layouts/default', $this->data);
    }

    public function delete_saved()
    {
        if (!isAuthorised(get_class(), 'delete_saved')) {
            return false;
        }

        $uuid = $this->uri->segment(3);
        if ($this->uri->segment(4) === 'confirm') {
            if ($this->Reports_model->deleteSaved($uuid)) {
                flashSuccess('Saved report deleted.');
            } else {
                flashDanger('Saved report not found.');
            }
            redirect(base_url('reports/saved'));
            return;
        }

        $report = $this->Reports_model->getSavedByUuid($uuid);
        if (empty($report)) {
            flashDanger('Saved report not found.');
            redirect(base_url('reports/saved'));
            return;
        }

        $this->data['report'] = $report;
        $this->mybreadcrumb->add('Reports', base_url('reports/saved'));
        $this->mybreadcrumb->add('Delete', base_url('reports/delete_saved/' . $uuid));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = 'Delete saved report';
        $this->data['content'] = $this->load->view('reports/delete_saved', $this->data, true);
        $this->load->view('layouts/default', $this->data);
    }

    private function buildDeveloperLines($rows, $rate)
    {
        $lines = [];
        foreach ($rows as $row) {
            $mins = $this->Reports_model->rowMinutes($row);
            $hrs = round($mins / 60, 2);
            $lines[] = [
                'line_date'        => !empty($row->start_time) ? substr($row->start_time, 0, 10) : null,
                'task_ref'         => !empty($row->taskRef) ? $row->taskRef : $row->taskNumber,
                'task_name'        => $row->taskName,
                'task_uuid'        => $row->taskUuid,
                'customer_name'    => $row->customerName,
                'project_name'     => $row->projectName,
                'sprint_name'      => $row->sprintName,
                'notes'            => $row->notes,
                'start_time'       => $row->start_time,
                'finish_time'      => $row->finish_time,
                'work_type'        => null,
                'entry_count'      => 1,
                'duration_minutes' => $mins,
                'hours'            => $hrs,
                'amount'           => $this->Reports_model->amount($hrs, $rate),
            ];
        }
        return $lines;
    }

    private function buildClientLines($rows, $rate)
    {
        $lines = [];
        foreach ($rows as $row) {
            $lines[] = [
                'line_date'        => null,
                'task_ref'         => !empty($row->taskRef) ? $row->taskRef : $row->taskNumber,
                'task_name'        => $row->taskName,
                'task_uuid'        => $row->taskUuid,
                'customer_name'    => $row->customerName,
                'project_name'     => $row->projectName,
                'sprint_name'      => $row->sprintName,
                'notes'            => null,
                'start_time'       => null,
                'finish_time'      => null,
                'work_type'        => !empty($row->workType) ? $row->workType : null,
                'entry_count'      => (int) $row->entryCount,
                'duration_minutes' => (int) $row->totalMinutes,
                'hours'            => (float) $row->totalHours,
                'amount'           => $this->Reports_model->amount($row->totalHours, $rate),
            ];
        }
        return $lines;
    }

    private function collectFilters()
    {
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        if (empty($from)) {
            $from = date('Y-m-01');
        }
        if (empty($to)) {
            $to = date('Y-m-t');
        }

        return [
            'from'         => $from,
            'to'           => $to,
            'developer_id' => $this->input->get('developer_id'),
            'customer_id'  => $this->input->get('customer_id'),
            'project_id'   => $this->input->get('project_id'),
            'sprint_id'    => $this->input->get('sprint_id'),
        ];
    }

    private function parseRate()
    {
        $rate = $this->input->get('rate');
        if ($rate === null || $rate === '') {
            return null;
        }
        return (float) $rate;
    }

    private function parseCurrency()
    {
        $currency = trim((string) $this->input->get('currency'));
        return $currency !== '' ? strtoupper($currency) : 'MUR';
    }

    private function shouldGenerate($requiredId, $rate)
    {
        if (empty($requiredId)) {
            return false;
        }
        if ($rate === null || $rate < 0) {
            return false;
        }
        return $this->input->get('rate') !== null && $this->input->get('rate') !== '';
    }

    private function loadFilterLookups($filters)
    {
        $this->load->model('Developers_model');
        $this->data['developers'] = $this->Developers_model->lookup();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup(!empty($filters['customer_id']) ? $filters['customer_id'] : '');

        $this->load->model('Sprints_model');
        $this->data['sprints'] = $this->Sprints_model->lookup2(!empty($filters['project_id']) ? $filters['project_id'] : '');
    }

    private function emptyTotals()
    {
        return [
            'minutes' => 0,
            'hours'   => 0,
            'amount'  => 0,
            'entries' => 0,
        ];
    }

    private function sumDeveloperRows($rows, $rate)
    {
        $minutes = 0;
        foreach ($rows as $row) {
            $minutes += $this->Reports_model->rowMinutes($row);
        }
        $hours = round($minutes / 60, 2);
        return [
            'minutes' => $minutes,
            'hours'   => $hours,
            'amount'  => $this->Reports_model->amount($hours, $rate),
            'entries' => count($rows),
        ];
    }

    private function sumClientRows($rows, $rate)
    {
        $minutes = 0;
        foreach ($rows as $row) {
            $minutes += (int) $row->totalMinutes;
        }
        $hours = round($minutes / 60, 2);
        return [
            'minutes' => $minutes,
            'hours'   => $hours,
            'amount'  => $this->Reports_model->amount($hours, $rate),
            'entries' => count($rows),
        ];
    }

    private function findDeveloper($id)
    {
        return $this->db->select('id, name, email')
            ->from('users')
            ->where(['id' => (int) $id, 'user_type' => 'developer'])
            ->get()->row();
    }

    private function findCustomer($id)
    {
        return $this->db->select('customer_id, company_name, email')
            ->from('customers')
            ->where('customer_id', (int) $id)
            ->get()->row();
    }

    private function streamPdf($view, $filenamePrefix, $data)
    {
        $this->load->library('Pdf');
        $html = $this->load->view($view, $data, true);
        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $filenamePrefix);
        $this->pdf->load($html, $safeName, date('Y-m-d'));
    }
}
