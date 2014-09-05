<?php
namespace Sumo;
class ControllerSaleInvoice extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_INVOICES'));
        $this->load->model('sale/invoice');

        $this->getList();
    }

    public function process_payment()
    {
        $amount    = isset($this->request->get['amount']) ? floatval($this->request->get['amount']) : 0;
        $invoiceID = isset($this->request->get['invoiceID']) ? intval($this->request->get['invoiceID']) : 0;

        // Nothing to process...
        if (empty($amount) || empty($invoiceID)) {
            return;
        }

        $this->load->model('sale/invoice');
        $remainingAmount = $this->model_sale_invoice->addPartialPayment($invoiceID, $amount);

        $return = array(
            'success'  => $remainingAmount !== false ? true : false,
            'amount'   => Formatter::currency($remainingAmount),
            'message'  => 'De deelbetaling is succesvol doorgevoerd.'
        );

        $this->response->setOutput(json_encode($return));
    }

    public function view()
    {
        $this->load->model('sale/invoice');

        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_INVOICES'));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_INVOICES'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_VIEW_INVOICE'),
        ));

        // Assemble invoice information
        $invoiceData = $this->model_sale_invoice->getInvoice($this->request->get['invoice_id']);

        foreach ($invoiceData['amount'] as $line => $amount) {
            $invoiceData['amount'][$line] = Formatter::currency($amount);
        }

        $this->data = array_merge($invoiceData, array(
            'token'           => $this->session->data['token'],
            'print'           => $this->url->link('sale/invoice/download', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoiceData['invoice_id'], 'SSL'),
            'send'            => $this->url->link('sale/invoice/send', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoiceData['invoice_id'], 'SSL'),
            'date'            => Formatter::date($invoiceData['invoice_date']),
            'sent_date'       => $invoiceData['sent_date'] != '0000-00-00' ? Formatter::date($invoiceData['sent_date']) : '&mdash;',
            'pay_date'        => Formatter::date(strtotime('+' . $invoiceData['term'] . ' days', strtotime($invoiceData['invoice_date']))),
            'total_amount'    => Formatter::currency($invoiceData['total_amount']),
            'total_open'      => Formatter::currency($invoiceData['total_amount'] - $invoiceData['total_amount_paid']),
            'process_payment' => $this->url->link('sale/invoice/process_payment', 'token=' . $this->session->data['token'], 'SSL')
        ));

        $this->document->addScript('view/js/pages/invoice_view.js');

        $this->template = 'sale/invoice_view.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function send()
    {
        $this->load->model('sale/invoice');
        $invoiceData = $this->model_sale_invoice->getInvoice($this->request->get['invoice_id']);

        $this->load->model('sale/customer');
        $customerData = $this->model_sale_customer->getCustomer($invoiceData['customer_id']);

        // Save invoice in cache
        $pdf = $this->download(true);

        $handle = fopen(DIR_CACHE . $invoiceData['invoice_no'] . '.pdf', 'w+');
        fwrite($handle, $pdf);
        fclose($handle);

        // Get template
        Mailer::setInvoice($invoiceData);
        Mailer::setCustomer($customerData);

        $template = Mailer::getTemplate('send_invoice');

        Mail::setTo($invoiceData['customer_email']);
        Mail::setSubject($template['title']);
        Mail::setHtml($template['content']);
        Mail::addAttachment(DIR_CACHE . $invoiceData['invoice_no'] . '.pdf');
        Mail::send();

        // Mark invoice as sent
        $this->model_sale_invoice->markSent($invoiceData['invoice_id']);

        // Change status (if necessary)
        if ($invoiceData['status'] == 'CONCEPT') {
            $this->model_sale_invoice->changeStatus($invoiceData['invoice_id'], 'sent');
        }

        // Remove invoice
        @unlink(DIR_CACHE . $invoiceData['invoice_no'] . '.pdf');

        $this->redirect($this->url->link('sale/invoice', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function download($return = false)
    {
        $this->load->model('sale/invoice');

        $invoiceData = $this->model_sale_invoice->getInvoice($this->request->get['invoice_id']);
        // Modify this array a little
        foreach ($invoiceData['amount'] as $k => $amount)
        {
            $invoiceData['amount'][$k] = Formatter::currency($amount / 100 * (100 + $invoiceData['tax_percentage'][$k]));
        }
        $invoiceData['store_id'] = 0;

        // Load store info
        $this->load->model('settings/stores');

        $storeData = array_merge(
            $this->model_settings_stores->getStore($invoiceData['store_id']),
            $this->model_settings_stores->getSettings($invoiceData['store_id'])
        );

        $this->data = array_merge($invoiceData, array(
            'invoice_date'          => Formatter::date($invoiceData['invoice_date']),
            'total_sub'             => Formatter::currency($invoiceData['total_sub'], false),
            'total_amount'          => Formatter::currency($invoiceData['total_amount'], false),
            'logo'                  => !empty($storeData['logo']) ? DIR_IMAGE . $storeData['logo'] : false,
            'store_address'         => nl2br($storeData['address']),
            'store'                 => $storeData['name'],
            'store_email'           => $storeData['email'],
            'store_website'         => rtrim($storeData['base_http'], '/'),
            'store_company_number'  => isset($storeData['coc_number']) && !empty($storeData['coc_number']) ? $storeData['coc_number'] : $this->config->get('coc_number'),
            'store_tax_number'      => isset($storeData['vat_number']) && !empty($storeData['vat_number']) ? $storeData['vat_number'] : $this->config->get('vat_number'),
            'store_iban'            => isset($storeData['iban']) && !empty($storeData['iban']) ? $storeData['iban'] : $this->config->get('iban'),
            'store_bic'             => isset($storeData['bic']) && !empty($storeData['bic']) ? $storeData['bic'] : $this->config->get('bic'),
            'invoice_footer'        => $storeData['invoice_footer']
        ));

        foreach ($invoiceData['total_tax'] as $taxPercentage => $taxAmount) {
            $this->data['total_tax'][$taxPercentage] = Formatter::currency($taxAmount, false);
        }

        $this->template = 'sale/invoice_pdf.tpl';

        $pdf = new \DOMPDF();
        $pdf->load_html($this->render());
        $pdf->set_paper("letter", "portrait");
        $pdf->render();

        if ($return) {
            return $pdf->output();
        }
        else {
            $pdf->stream($invoiceData['invoice_no'] . ".pdf", array('Attachment' => 0));
        }
    }

    public function insert()
    {
        $this->load->model('sale/invoice');

        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_INVOICES'));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_INVOICES'),
        ));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_invoice->addInvoice($this->request->post);

            // Back to overview
            $this->redirect($this->url->link('sale/invoice', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->load->model('sale/invoice');
        $this->getForm();
    }

    public function update()
    {
        $this->load->model('sale/invoice');

        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_INVOICES'));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_INVOICES'),
        ));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_invoice->updateInvoice($this->request->get['invoice_id'], $this->request->post);

            // Back to overview
            $this->redirect($this->url->link('sale/invoice', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->load->model('sale/invoice');
        $this->getForm();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_INVOICES'));
        $this->load->model('sale/invoice');

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $invoiceID) {
                // We can only delete concept-invoices
                $invoiceData = $this->model_sale_invoice->getInvoice($invoiceID);

                switch ($invoiceData['status']) {
                    case 'SENT':
                    case 'PARTIALLY_PAID':
                    case 'PAID':
                        // Generate credit invoice
                        $creditInvoiceData = $invoiceData;
                        $line = 0;

                        foreach ($creditInvoiceData['amount'] as $line => $amount) {
                            $creditInvoiceData['amount'][$line] = 0 - $amount;
                        }

                        $line++;

                        // Add extra line
                        $creditInvoiceData['product_id'][$line] = 0;
                        $creditInvoiceData['product'][$line] = '';
                        $creditInvoiceData['quantity'][$line] = 0;
                        $creditInvoiceData['amount'][$line] = 0;
                        $creditInvoiceData['tax'][$line] = 0;
                        $creditInvoiceData['tax_percentage'][$line] = 0;
                        $creditInvoiceData['description'][$line] = sprintf(Language::getVar('SUMO_CORRSPONDING_INVOICE'), $invoiceData['invoice_no']);

                        $creditInvoiceID = $this->model_sale_invoice->addInvoice($creditInvoiceData);
                        $this->model_sale_invoice->changeStatus($creditInvoiceID, 'CREDIT');

                    case 'CONCEPT':
                        // Mark as expired
                        $this->model_sale_invoice->changeStatus($invoiceID, 'EXPIRED');
                    break;
                }
            }

            // Back to overview
            //$this->redirect($this->url->link('sale/invoice', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    /**
    * Private shared functions, not directly accessible
    */
    private function getForm()
    {
        if (isset($this->request->get['invoice_id'])) {
            $invoiceData = $this->model_sale_invoice->getInvoice($this->request->get['invoice_id']);

            // No invoiceData? Send back to overview
            if (!$invoiceData) {
                $this->redirect($this->url->link('sale/invoice', 'token=' . $this->session->data['token'], 'SSL'));
            }
        }

        if (isset($invoiceData)) {
            $invoice_no = $invoiceData['invoice_no'];
        }
        else {
            $invoice_no = $this->model_sale_invoice->getNextInvoiceNo();
        }

        $formFields = array(
            'customer'       => '',
            'customer_id'    => 0,
            'customer_no'    => '',
            'reference'      => '',
            'quantity'       => array(),
            'amount'         => array(),
            'description'    => array(),
            'product'        => array(),
            'product_id'     => array(),
            'tax_percentage' => array(),
            'discount'       => array(),
            'totals'         => array(),
            'shipping_amount'=> 0,
            'payment_amount' => 0,
            'date'           => Formatter::date(time()),
            'notes'          => '',
            'term'           => 14,
            'template'       => 'invoice',
            'sent_date'      => Formatter::date(time()),
            'auto'           => 0
        );

        foreach ($formFields as $formField => $defaultValue) {
            if (isset($this->request->post[$formField])) {
                $this->data[$formField] = $this->request->post[$formField];
            }
            elseif (isset($invoiceData[$formField])) {
                if ($formField == 'sent_date' || $formField == 'date') {
                    $this->data[$formField] = Formatter::date($invoiceData[$formField]);
                }
                else {
                    $this->data[$formField] = $invoiceData[$formField];
                }
            }
            else {
                $this->data[$formField] = $defaultValue;
            }
        }

        $this->data = array_merge($this->data, array(
            'invoice_no'        => $invoice_no,
            'sent'              => isset($invoiceData['sent']) ? $invoiceData['sent'] : false,
            'token'             => $this->session->data['token'],
            'shipping_amount'   => $this->data['shipping_amount'] > 0 ? number_format($this->data['shipping_amount'], 2, '.', ',') : '',
            'payment_amount'    => $this->data['payment_amount'] > 0 ? number_format($this->data['payment_amount'], 2, '.', ',') : ''
        ));

        if (isset($this->data['discount']['discount'])) {
            if (!empty($this->data['discount']['discount'])) {
                $this->data['discount']['discount'] = round($this->data['discount']['discount'], 4);
            }
            else {
                $this->data['discount']['discount'] = '';
            }
        }

        // List tax percentages
        $this->load->model('settings/general');

        foreach ($this->model_settings_general->getSetting('tax_percentage') as $tp) {
            if (is_array($tp)) {
                // Extra
                foreach ($tp as $tpExtra) {
                    $this->data['tax_percentages'][] = $tpExtra;
                }
            }
            else {
                // Default
                $this->data['tax_percentages'][] = $tp;
            }
        }

        $this->document->addScript('view/js/jquery/jquery.autocomplete.js');
        $this->document->addScript('view/js/pages/invoice_form.js');

        $this->template = 'sale/invoice_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_INVOICES'),
        ));

        // Find invoices
        $allowedFilters = array('concept', 'sent', 'partially_paid', 'paid', 'credit', 'expired');

        if (isset($this->request->get['filter']) && in_array($this->request->get['filter'], $allowedFilters)) {
            $status = mb_strtoupper($this->request->get['filter']);
        }
        else {
            $status = array('CONCEPT', 'SENT', 'PARTIALLY_PAID', 'PAID', 'CREDIT');
        }

        $limit = 20;
        $page_total_ex = $page_total_in = 0;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

        $filter = array(
            'status'   => $status,
            'limit'    => $limit,
            'start'    => ($page - 1) * $limit
        );

        foreach ($this->model_sale_invoice->getInvoices($filter) as $invoice) {

            // Customer is corporate or private?
            if (!empty($invoice['customer_company_name'])) {
                $customer = $invoice['customer_company_name'];
            }
            else {
                $customer = $invoice['customer_name'];
            }

            // Sent to the view
            $this->data['invoices'][] = array(
                'invoice_id'        => $invoice['invoice_id'],
                'invoice_no'        => $invoice['invoice_no'],
                'customer'          => $customer,
                'date'              => Formatter::date($invoice['invoice_date']),
                'amount'            => Formatter::currency($invoice['total_amount']),
                'status'            => $invoice['status'],

                // Links
                'send'              => $this->url->link('sale/invoice/send', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoice['invoice_id'], 'SSL'),
                'view'              => $this->url->link('sale/invoice/view', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoice['invoice_id'], 'SSL'),
                'download'          => $this->url->link('sale/invoice/download', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoice['invoice_id'], 'SSL'),
                'update'            => $this->url->link('sale/invoice/update', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoice['invoice_id'], 'SSL')
            );

            $page_total_ex += ($invoice['amount'] - $invoice['tax']);
            $page_total_in += $invoice['amount'];
        }

        // In need of pagination?
        $invoiceTotal = $this->model_sale_invoice->getTotalInvoices($filter);

        if ($invoiceTotal > $limit) {
            $pagination = new Pagination();
            $pagination->total = $product_total;
            $pagination->limit = $limit;
            $pagination->page = $page;

            $pagination->url = $this->url->link('sale/invoice', 'token=' . $this->session->data['token'] . '&page={page}' . (!is_array($status) ? '&status=' . $status : ''));

            $this->data['pagination'] = $pagination->renderAdmin();
        }
        else {
            $this->data['pagination'] = false;
        }

        $this->data = array_merge($this->data, array(
            'page_total_ex' => Formatter::currency($page_total_ex),
            'page_total_in' => Formatter::currency($page_total_in),
            'status'        => !is_array($status) ? Language::getVar('SUMO_NOUN_' . mb_strtoupper($status)) : Language::getVar('SUMO_NOUN_DEFAULT'),

            // Links
            'delete'        => $this->url->link('sale/invoice/delete', 'token=' . $this->session->data['token'], 'SSL'),
            'insert'        => $this->url->link('sale/invoice/insert', 'token=' . $this->session->data['token'], 'SSL'),
            'filter'        => $this->url->link('sale/invoice', 'token=' . $this->session->data['token'] . '&filter=', 'SSL'),
            'overview'      => $this->url->link('sale/invoice', 'token=' . $this->session->data['token'], 'SSL'),
        ));

        $this->template = 'sale/invoice_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validateForm()
    {
        // Do some validation and add extra info

        /**
        * Required:
        * - customer_id
        * - at least one invoice record
        */
        $this->load->model('sale/customer');
        $customerData = $this->model_sale_customer->getCustomer($this->request->post['customer_id']);

        if (!$customerData) {
            $this->error[] = Language::getVar('SUMO_ERROR_NO_CUSTOMER');
        }
        else {
            if (!empty($customerData['middlename'])) {
                $customerName = $customerData['firstname'] . ' ' . $customerData['middlename'] . ' ' . $customerData['lastname'];
            }
            else {
                $customerName = $customerData['firstname'] . ' ' . $customerData['lastname'];
            }

            // Add extra customer info
            if (!empty($customerData['address_id'])) {
                $customerData = array_merge($customerData, $this->model_sale_customer->getAddress($customerData['address_id']));
            }
            else {
                // No default address? Get all addresses and add first address
                $addresses = $this->model_sale_customer->getAddresses($customerData['customer_id']);

                if (!empty($addresses)) {
                    $customerData = array_merge($customerData, array_shift($addresses));
                }
            }

            $this->request->post = array_merge($this->request->post, array(
                'customer_no'       => 'CID.'.str_pad($customerData['customer_id'], 5, 0, STR_PAD_LEFT),
                'customer_name'     => $customerName,
                'customer_address'  => $customerData['address_1'],
                'customer_postcode' => $customerData['postcode'],
                'customer_city'     => $customerData['city'],
                'customer_country'  => $customerData['country'],
                'customer_email'    => $customerData['email']
            ));
        }

        $this->request->post['total_amount'] = $this->request->post['total_tax'] = 0;

        foreach ($this->request->post['amount'] as $line => $amount) {
            // Only add line if we have a quantity and description
            if (empty($this->request->post['quantity'][$line]) || empty($this->request->post['description'][$line])) {
                unset($this->request->post['quantity'][$line]);
                unset($this->request->post['amount'][$line]);
                unset($this->request->post['tax_percentage'][$line]);
                unset($this->request->post['description'][$line]);

                // Next item in the loop please
                continue;
            }

            if (!empty($amount)) {
                // Set tax amount
                $tax = $this->request->post['quantity'][$line] * $amount / 100 * $this->request->post['tax_percentage'][$line];

                $this->request->post['tax'][$line] = $tax;
                $this->request->post['total_amount'] += ($amount * $this->request->post['quantity'][$line]) + $tax;
                $this->request->post['total_tax'] += $tax;
            }
            else {
                $this->request->post['tax'][$line] = 0;

                // We may not have set this info already, therefore always call it with +0
                // even if there is not a price present
                $this->request->post['total_tax'] = $this->request->post['total_amount'] += 0;
            }
        }

        if (!sizeof($this->request->post['amount'])) {
            $this->error[] = Language::getVar('SUMO_ERROR_NO_INVOICE_LINE');
        }

        if (!sizeof($this->error)) {
            return true;
        }

        $this->data['form_error'] = implode('<br />', $this->error);
        return false;
    }
}
