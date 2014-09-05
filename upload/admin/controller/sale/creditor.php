<?php
namespace Sumo;
class ControllerSaleCreditor extends Controller
{
    public function index() 
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SUPPLIER_DASHBOARD'));

        $this->load->model('sale/creditor');
        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SUPPLIER_DASHBOARD'));
        $this->load->model('sale/creditor');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_creditor->addCreditor($this->request->post);
            $this->redirect($this->url->link('sale/creditor', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SUPPLIER_DASHBOARD'));
        $this->load->model('sale/creditor');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_creditor->editCreditor($this->request->get['creditor_id'], $this->request->post);
            $this->redirect($this->url->link('sale/creditor', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SUPPLIER_DASHBOARD'));
        $this->load->model('sale/creditor');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $creditorID) {
                $this->model_sale_creditor->deleteCreditor($creditorID);
            }

            $this->redirect($this->url->link('sale/creditor', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SUPPLIER_DASHBOARD'),
        ));

        $this->data = array_merge($this->data, array(
            'creditors'     => array(),
            'insert'        => $this->url->link('sale/creditor/insert', 'token=' . $this->session->data['token'], 'SSL'),
            'delete'        => $this->url->link('sale/creditor/delete', 'token=' . $this->session->data['token'], 'SSL')
        ));

        // Initiate pagination
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data = array(
            'start' => ($page - 1) * 25,
            'limit' => 25
        );

        $creditorTotal = $this->model_sale_creditor->getTotalCreditors();

        foreach ($this->model_sale_creditor->getCreditors($data) as $creditor) {
            $this->data['creditors'][] = array_merge($creditor, array(
                'update'    => $this->url->link('sale/creditor/update', 'token=' . $this->session->data['token'] . '&creditor_id=' . $creditor['creditor_id'], 'SSL')
            ));
        }

        $pagination = new Pagination();
        $pagination->total = $creditorTotal;
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('sale/creditor', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();

        $this->template = 'sale/creditor_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SUPPLIER_DASHBOARD'),
            'href'      => $this->url->link('sale/creditor')
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SUPPLIER'),
        ));

        $creditorID = isset($this->request->get['creditor_id']) ? $this->request->get['creditor_id'] : 0;

        if ($creditorID > 0) {
            $creditorData = $this->model_sale_creditor->getCreditor($creditorID);
        }

        $formFields = array(
            'companyname'       => '',
            'contact_gender'    => 'm',
            'contact_name'      => '', 
            'contact_surname'   => '',
            'address'           => '',
            'city'              => '',
            'postcode'          => '',
            'country_id'        => 0,
            'contact_email'     => '',
            'contact_fax'       => '',
            'contact_phone'     => '',
            'contact_mobile'    => '',
            'customer_number'   => '',
            'bank_iban'         => '',
            'bank_account'      => '',
            'bank_name'         => '',
            'bank_city'         => '',
            'bank_bic'          => '',
            'term'              => 0,
            'notes'             => ''
        );

        // Prefill form fields
        foreach ($formFields as $field => $defaultValue) {
            if (isset($this->request->post[$field])) {
                $formFields[$field] = $this->request->post[$field];
            } elseif (isset($creditorData[$field])) {
                $formFields[$field] = $creditorData[$field];
            }
        }

        $this->load->model('localisation/country');

        $this->data = array_merge($this->data, $formFields, array(
            'cancel'        => $this->url->link('sale/creditor', 'token=' . $this->session->data['token'], 'SSL'),
            'countries'     => $this->model_localisation_country->getCountries()
        ));

        $this->template = 'sale/creditor_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        // [todo]: Do some permission checks?
        return true;
    }

    protected function validateDelete()
    {
        // [todo]: Another permission check
        return true;
    }
}