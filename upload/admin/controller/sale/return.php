<?php
namespace Sumo;
class ControllerSaleReturn extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_RETURN'));

        $this->load->model('sale/return');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_RETURN'));

        $this->load->model('sale/return');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $returnData = $this->request->post;

            $this->model_sale_return->addReturn($returnData);

            $this->redirect($this->url->link('sale/return', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_RETURN'));

        $this->load->model('sale/return');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $returnData = $this->request->post;

            $this->model_sale_return->editReturn($this->request->get['return_id'], $returnData);

            $this->redirect($this->url->link('sale/return', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_RETURN'));

        $this->load->model('sale/return');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $return_id) {
                $this->model_sale_return->deleteReturn($return_id);
            }

            $this->redirect($this->url->link('sale/return', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function get_order_info()
    {
        $orderID   = isset($this->request->get['order_id']) ? ltrim($this->request->get['order_id'], 0) : 0;
        $orderInfo = array();

        if ($orderID > 0) {
            $this->load->model('sale/orders');
            $orderInfo = $this->model_sale_orders->getOrder($orderID);
        }

        $orderInfo['order_date'] = Formatter::date($orderInfo['order_date']);

        $this->response->setOutput(json_encode($orderInfo));
    }

    public function get_order_products()
    {
        $orderID       = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
        $orderProducts = array();

        if ($orderID > 0) {
            $this->load->model('sale/order');
            $orderProducts = $this->model_sale_order->getOrderProducts($orderID);
        }

        $this->response->setOutput(json_encode($orderProducts));
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_RETURN'),
        ));

        $this->data = array_merge($this->data, array(
            'insert'        => $this->url->link('sale/return/insert', 'token=' . $this->session->data['token'], 'SSL'),
            'delete'        => $this->url->link('sale/return/delete', 'token=' . $this->session->data['token'], 'SSL'),
            'returns'       => array(),
            'token'         => $this->session->data['token']
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

        $return_total = $this->model_sale_return->getTotalReturns($data);

        foreach ($this->model_sale_return->getReturns($data) as $result) {
            $this->data['returns'][] = array(
                'return_id'     => $result['return_id'],
                'order_id'      => $result['order_id'],
                'customer'      => $result['customer'],
                'product'       => $result['product'],
                'model'         => $result['model'],
                'status'        => $result['status'],
                'date_added'    => Formatter::date($result['date_added']),
                'date_modified' => Formatter::date($result['date_modified']),
                'edit'          => $this->url->link('sale/return/update', 'token=' . $this->session->data['token'] . '&return_id=' . $result['return_id'], 'SSL'),
                'info'          => $this->url->link('sale/return/info', 'token=' . $this->session->data['token'] . '&return_id=' . $result['return_id'], 'SSL'),
            );
        }

        $pagination = new Pagination();
        $pagination->total = $return_total;
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('sale/return', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();

        $this->template = 'sale/return_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        $this->load->model('localisation/return_reason');
        $this->load->model('localisation/return_action');
        $this->load->model('localisation/return_status');
        $this->load->model('sale/orders');

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_RETURN'),
        ));

        if (!isset($this->request->get['return_id'])) {
            $returnID   = 0;
            $action     = $this->url->link('sale/return/insert', 'token=' . $this->session->data['token'], 'SSL');
            $returnInfo = array();
        } else {
            $returnID   = $this->request->get['return_id'];
            $action     = $this->url->link('sale/return/update', 'token=' . $this->session->data['token'] . '&return_id=' . $returnID, 'SSL');
            $returnInfo = $this->model_sale_return->getReturn($returnID);
        }

        $fields = array(
            'order_id'         => '',
            'date_ordered'     => Formatter::date(time()),
            'customer'         => '',
            'customer_id'      => '',
            'firstname'        => '',
            'lastname'         => '',
            'email'            => '',
            'telephone'        => '',
            'product'          => '',
            'product_id'       => '',
            'model'            => '',
            'quantity'         => '',
            'opened'           => '',
            'return_reason_id' => '',
            'return_action_id' => '',
            'comment'          => '',
            'return_status_id' => '',
        );

        foreach ($fields as $field => $defaultValue) {
            if (isset($this->reqeust->post[$field])) {
                $fields[$field] = $this->request->post[$field];
            }
            elseif (isset($returnInfo[$field])) {
                if ($field == 'date_ordered') {
                    $fields[$field] = Formatter::date($returnInfo[$field]);
                }
                else {
                    $fields[$field] = $returnInfo[$field];
                }
            }
        }

        $this->data = array_merge($this->data, $fields, array(
            'order_id'          => str_pad($fields['order_id'], 5, 0, STR_PAD_LEFT),
            'raw_order_id'      => $fields['order_id'],
            'token'             => $this->session->data['token'],
            'action'            => $action,
            'error_warning'     => isset($this->error['warning']) ? $this->error['warning'] : '',
            'cancel'            => $this->url->link('sale/return', 'token=' . $this->session->data['token'], 'SSL'),
            'products'          => $this->model_sale_orders->getOrderProducts($fields['order_id']),
            'return_reasons'    => $this->model_localisation_return_reason->getReturnReasons(),
            'return_actions'    => $this->model_localisation_return_action->getReturnActions(),
            'return_statuses'   => $this->model_localisation_return_status->getReturnStatuses()
        ));

        $this->document->addScript('view/js/pages/return_form.js');

        $this->template = 'sale/return_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function info()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_RETURN'),
            'href'      => $this->url->link('sale/return'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_RETURN_VIEW'),
        ));

        $this->load->model('sale/return');
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_RETURN'));

        if (isset($this->request->get['return_id'])) {
            $returnID = $this->request->get['return_id'];
        }
        else {
            $returnID = 0;
        }

        // List the current history before loading the return info
        $this->history();

        $returnInfo = $this->model_sale_return->getReturn($returnID);

        if ($returnInfo) {
            // Load required models
            $this->load->model('localisation/return_status');
            $this->load->model('localisation/return_action');
            $this->load->model('localisation/return_reason');

            $returnStatusInfo = $this->model_localisation_return_status->getReturnStatus($returnInfo['return_status_id']);
            $returnReasonInfo = $this->model_localisation_return_reason->getReturnReason($returnInfo['return_reason_id']);

            $this->data = array_merge($this->data, $returnInfo, array(
                'return_id'         => 'RID.' . str_pad($returnInfo['return_id'], 5, 0, STR_PAD_LEFT),
                'order_id'          => 'OID.' . str_pad($returnInfo['order_id'], 5, 0, STR_PAD_LEFT),
                'raw_order_id'      => $returnInfo['order_id'],
                'cancel'            => $this->url->link('sale/return', 'token=' . $this->session->data['token'], 'SSL'),
                'token'             => $this->session->data['token'],
                'date_ordered'      => Formatter::date($returnInfo['date_ordered']),
                'date_added'        => Formatter::date($returnInfo['date_added']),
                'date_modified'     => Formatter::date($returnInfo['date_modified']),
                'return_status'     => $returnStatusInfo ? $returnStatusInfo['name'] : '',
                'return_reason'     => $returnReasonInfo ? $returnReasonInfo['name'] : '',

                'return_actions'    => $this->model_localisation_return_action->getReturnActions(),
                'return_statuses'   => $this->model_localisation_return_status->getReturnStatuses()
            ));

            $this->template = 'sale/return_info.tpl';
        }
        else {
            $this->redirect('sale/return');
        }

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
            $this->error['firstname'] = Language::getVar('SUMO_ERROR_FIRSTNAME');
        }

        if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
            $this->error['lastname'] = Language::getVar('SUMO_ERROR_LASTNAME');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
            $this->error['email'] = Language::getVar('SUMO_ERROR_EMAIL');
        }

        if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
            $this->error['telephone'] = Language::getVar('SUMO_ERROR_PHONE');
        }

        if ((utf8_strlen($this->request->post['product']) < 1) || (utf8_strlen($this->request->post['product']) > 255)) {
            $this->error['product'] = Language::getVar('SUMO_ERROR_PRODUCT');
        }

        if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
            $this->error['model'] = Language::getVar('SUMO_ERROR_MODEL');
        }

        if (empty($this->request->post['return_reason_id'])) {
            $this->error['reason'] = Language::getVar('SUMO_ERROR_REASON');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = implode('<br />', $this->error);
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    protected function validateDelete()
    {
        return true;
    }

    public function action()
    {
        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('sale/return');
            $this->model_sale_return->editReturnAction($this->request->get['return_id'], $this->request->post['return_action_id']);
        }

        $this->response->setOutput(json_encode($json));
    }

    public function history()
    {
        $this->load->model('sale/return');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_sale_return->addReturnHistory($this->request->get['return_id'], $this->request->post);
        }

        $this->data['histories'] = array();

        $results = $this->model_sale_return->getReturnHistories($this->request->get['return_id'], 0, 10);

        foreach ($results as $result) {
            $this->data['histories'][] = array(
                'notify'     => $result['notify'] ? Language::getVar('SUMO_NOUN_YES') : Language::getVar('SUMO_NOUN_NO'),
                'status'     => $result['status'],
                'comment'    => nl2br($result['comment']),
                'date_added' => Formatter::date($result['date_added'])
            );
        }
    }
}
