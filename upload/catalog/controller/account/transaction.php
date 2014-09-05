<?php
namespace Sumo;
class ControllerAccountTransaction extends Controller
{
    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/transaction', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_TRANSACTION_TITLE'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),

        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TRANSACTION_TITLE'),
            'href'      => $this->url->link('account/transaction', '', 'SSL'),

        );

        $this->load->model('account/transaction');

        //$this->data['column_amount'] = sprintf(Language::getVar('SUMO_ACCOUNT_TRANSACTION_AMOUNT'), $this->config->get('config_currency'));

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['transactions'] = array();

        $data = array(
            'sort'  => 'date_added',
            'order' => 'DESC',
            'start' => ($page - 1) * 10,
            'limit' => 10
        );

        $transaction_total = $this->model_account_transaction->getTotalTransactions($data);

        foreach ($this->model_account_transaction->getTransactions($data) as $result) {
            $this->data['transactions'][] = array(
                'transaction_id' => str_pad($result['customer_transaction_id'], 9, 0, STR_PAD_LEFT),
                'amount'         => Formatter::currency($result['amount']),
                'description'    => $result['description'],
                'date_added'     => Formatter::date($result['date_added'])
            );
        }

        $pagination = new Pagination();
        $pagination->total = $transaction_total;
        $pagination->page  = $page;
        $pagination->limit = 10;
        $pagination->url   = $this->url->link('account/transaction', 'page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();
        $this->data['total']      = $this->currency->format($this->customer->getBalance());

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/transaction.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }
}
