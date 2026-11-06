<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public support form (no login). Guests follow their ticket through a secret tracking link
 * that is only sent by email, so reading replies requires owning the address used.
 */
class Support extends CI_Controller
{
    var $data = [];

    const MIN_FILL_SECONDS = 3;

    public function __construct()
    {
        parent::__construct();
        $this->load->library("migration");
        $this->load->model("system_model");
        $this->load->model("Tickets_model");
        $this->data['logoDark'] = $this->system_model->getParam("logo-dark");
        $this->data['flash_danger'] = getFlashMessage("danger");
        $this->data['flash_success'] = getFlashMessage("success");
        $this->data['flash_warning'] = getFlashMessage("warning");
        $this->data['page_title'] = "Support";
    }

    public function index()
    {
        $_SESSION['support_form_token'] = bin2hex(random_bytes(16));
        $_SESSION['support_form_time'] = time();
        $this->data['token'] = $_SESSION['support_form_token'];
        $this->data['categories'] = $this->Tickets_model->categories();
        $this->data['old'] = isset($_SESSION['support_form_old']) ? $_SESSION['support_form_old'] : [];
        unset($_SESSION['support_form_old']);
        $this->render('portal/support/form');
    }

    public function submit()
    {
        if ($this->input->method() !== 'post') {
            redirect('portal/support');
            return;
        }

        $post = [
            'requester_name'    => trim((string) $this->input->post('name')),
            'requester_email'   => strtolower(trim((string) $this->input->post('email'))),
            'requester_company' => trim((string) $this->input->post('company')),
            'subject'           => $this->input->post('subject'),
            'body'              => $this->input->post('body'),
            'category_id'       => $this->input->post('category_id'),
            'priority'          => $this->input->post('priority'),
        ];
        $fail = function ($reason) use ($post) {
            $_SESSION['support_form_old'] = $post;
            setFlashMessage('danger', $reason);
            redirect('portal/support');
        };

        $token = (string) $this->input->post('form_token');
        $expected = isset($_SESSION['support_form_token']) ? $_SESSION['support_form_token'] : '';
        unset($_SESSION['support_form_token']);
        if ($expected === '' || !hash_equals($expected, $token)) {
            return $fail('Your session expired. Please submit the form again.');
        }

        // Bots: the hidden "website" field must stay empty and humans take a few seconds to fill the form.
        $startedAt = isset($_SESSION['support_form_time']) ? (int) $_SESSION['support_form_time'] : 0;
        if ($this->input->post('website') !== '' && $this->input->post('website') !== null) {
            redirect('portal/support/thanks');
            return;
        }
        if (time() - $startedAt < self::MIN_FILL_SECONDS) {
            return $fail('Please take a moment to review your request, then submit again.');
        }

        if ($post['requester_name'] === '') {
            return $fail('Please enter your name.');
        }
        if (!filter_var($post['requester_email'], FILTER_VALIDATE_EMAIL)) {
            return $fail('Please enter a valid email address.');
        }

        $ip = $this->input->ip_address();
        if ($this->Tickets_model->publicRateLimited($ip, $post['requester_email'])) {
            return $fail("You've submitted several requests recently. Please wait a while, or reply on one of your existing tickets.");
        }

        // Link to a company only when the email belongs to exactly one portal user who can sign in
        // (same conditions as the portal login). Otherwise the requester is a guest and follows the
        // ticket through the emailed tracking link.
        $matches = $this->db->select('ca.id, ca.customer_id')
            ->from('customer_access ca')
            ->join('customers c', 'c.customer_id = ca.customer_id')
            ->where(['ca.status' => 1, 'c.status' => 1, 'c.active' => 1])
            ->where('ca.email', $post['requester_email'])
            ->limit(2)->get()->result();
        $access = count($matches) === 1 ? $matches[0] : null;

        $data = $post;
        $data['ip_address'] = $ip;
        if ($access) {
            $data['customer_access_id'] = (int) $access->id;
            $data['customer_id'] = (int) $access->customer_id;
        }
        $actor = $access ? ['type' => 'customer', 'id' => (int) $access->id] : ['type' => 'guest', 'id' => null];

        $result = $this->Tickets_model->create($data, 'public', $actor);
        if (!$result['result']) {
            return $fail($result['reason']);
        }

        $_SESSION['support_last_ticket'] = [
            'number'        => $result['ticket']->public_ref,
            'email'         => $post['requester_email'],
            'upload_errors' => $result['upload_errors'],
            'portal_user'   => (bool) $access,
        ];
        redirect('portal/support/thanks');
    }

    public function thanks()
    {
        $this->data['last'] = isset($_SESSION['support_last_ticket']) ? $_SESSION['support_last_ticket'] : null;
        unset($_SESSION['support_last_ticket']);
        $this->render('portal/support/thanks');
    }

    public function track()
    {
        $ticket = $this->guestTicket($this->input->get('t'));
        $token = $ticket->public_token;
        $this->data['page_title'] = $ticket->public_ref;
        $this->data['portal'] = 'public';
        $this->data['ticket'] = $ticket;
        $this->data['thread'] = $this->Tickets_model->getThread($ticket, false);
        $this->data['fileUrl'] = base_url('portal/support/file/{id}?t=' . $token);
        $this->data['replyUrl'] = base_url('portal/support/reply?t=' . $token);
        $this->data['resolveUrl'] = base_url('portal/support/resolve?t=' . $token);
        $this->render('portal/helpdesk/view');
    }

    public function reply()
    {
        $ticket = $this->guestTicket($this->input->get('t'));
        $result = $this->Tickets_model->addReply($ticket, ['type' => 'guest', 'id' => null], $this->input->post('body'));
        if ($result['result']) {
            if (!empty($result['upload_errors'])) {
                setFlashMessage('warning', 'Some files were not attached: ' . htmlspecialchars(implode('; ', $result['upload_errors'])));
            }
            setFlashMessage('success', 'Your reply has been sent.');
        } else {
            setFlashMessage('danger', $result['reason']);
        }
        redirect('portal/support/track?t=' . $ticket->public_token);
    }

    public function resolve()
    {
        $ticket = $this->guestTicket($this->input->get('t'));
        if ($this->input->method() === 'post' && !in_array($ticket->status, Tickets_model::CLOSED_STATUSES, true)) {
            $this->Tickets_model->changeField($ticket, 'status', 'resolved', ['type' => 'guest', 'id' => null]);
            setFlashMessage('success', 'Thanks! The ticket has been marked as resolved.');
        }
        redirect('portal/support/track?t=' . $ticket->public_token);
    }

    public function file($id = 0)
    {
        $ticket = $this->guestTicket($this->input->get('t'));
        $attachment = $this->Tickets_model->getAttachment($id);
        if (empty($attachment) || $attachment->is_internal || (int) $attachment->ticket_id !== (int) $ticket->id) {
            show_404();
            return;
        }
        $this->Tickets_model->streamAttachment($attachment);
    }

    /** Tracking links only work for guest tickets; portal users follow theirs in the portal. */
    private function guestTicket($token)
    {
        $ticket = $this->Tickets_model->getByToken($token);
        if (empty($ticket)) {
            show_404();
            exit;
        }
        if (!empty($ticket->customer_access_id)) {
            // Staff handed the ticket over to a portal contact.
            $this->data['ticket'] = $ticket;
            $this->render('portal/support/moved');
            $this->output->_display();
            exit;
        }
        return $ticket;
    }

    private function render($view)
    {
        $this->data['content'] = $this->load->view($view, $this->data, true);
        $this->load->view('portal/support/layout', $this->data);
    }
}
