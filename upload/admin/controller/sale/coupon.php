<?php
namespace Sumo;
class ControllerSaleCoupon extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_COUPONS'));
        $this->load->model('sale/coupon');
        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_ADD_COUPON'));
        $this->load->model('sale/coupon');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_coupon->addCoupon($this->request->post);
            $this->redirect($this->url->link('sale/coupon', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_EDIT_COUPON'));
        $this->load->model('sale/coupon');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_coupon->editCoupon($this->request->get['coupon_id'], $this->request->post);

            $this->redirect($this->url->link('sale/coupon', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->getForm();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SALES_ADD_COUPON'));
        $this->load->model('sale/coupon');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $coupon_id) {
                $this->model_sale_coupon->deleteCoupon($coupon_id);
            }
            $this->redirect($this->url->link('sale/coupon', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->getList();
    }

    protected function getList()
    {
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }
        else {
            $page = 1;
        }

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_COUPONS'),
        ));

        $data = array(
            'start' => ($page - 1) * 25,
            'limit' => 25
        );

        $pagination = new Pagination();
        $pagination->total = $this->model_sale_coupon->getTotalCoupons();
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('sale/coupon', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data = array_merge($this->data, array(
            'insert'        => $this->url->link('sale/coupon/insert', 'token=' . $this->session->data['token'], 'SSL'),
            'delete'        => $this->url->link('sale/coupon/delete', 'token=' . $this->session->data['token'], 'SSL'),
            'pagination'    => $pagination->renderAdmin(),
            'coupons'       => array()
        ));

        foreach ($this->model_sale_coupon->getCoupons($data) as $result) {
            if ($result['status']) {
                if (strtotime($result['date_start']) >= time()) {
                    // Inactive
                    $status = Language::getVar('SUMO_NOUN_COUPON_NOT_ACTIVE_YET');
                }
                else if ($result['date_end'] != '0000-00-00' && strtotime($result['date_end']) <= time()) {
                    // Expired
                    $status = Language::getVar('SUMO_NOUN_COUPON_EXPIRED');
                }
                else {
                    // Active / Enabled
                    $status = Language::getVar('SUMO_NOUN_ENABLED');
                }
            }
            else {
                // Hard-disabled
                $status = Language::getVar('SUMO_NOUN_DISABLED');
            }

            $this->data['coupons'][] = array_merge($result, array(
                'date_start'    => $result['date_start'] == '0000-00-00' ? '&mdash;' : Formatter::date($result['date_start']),
                'date_end'      => $result['date_end'] == '0000-00-00' ? '&mdash;' : Formatter::date($result['date_end']),
                'status'        => $status,
                'selected'      => isset($this->request->post['selected']) && in_array($result['coupon_id'], $this->request->post['selected']),
                'edit'          => $this->url->link('sale/coupon/update', 'token=' . $this->session->data['token'] . '&coupon_id=' . $result['coupon_id'], 'SSL'),
            ));
        }

        $this->template = 'sale/coupon_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->data['tax'] = $this->config->get('tax_percentage');

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_DASHBOARD'),
            'href'      => $this->url->link('sale/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_COUPONS'),
            'href'      => $this->url->link('sale/coupon'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SALES_EDIT_COUPON'),
        ));

        if (isset($this->request->get['coupon_id'])) {
            $couponID   = $this->request->get['coupon_id'];
            $action     = $this->url->link('sale/coupon/update', 'token=' . $this->session->data['token'] . '&coupon_id=' . $couponID, 'SSL');
            $couponInfo = $this->model_sale_coupon->getCoupon($couponID);

            $couponInfo['coupon_product']  = $this->model_sale_coupon->getCouponProducts($couponID);
            $couponInfo['coupon_category'] = $this->model_sale_coupon->getCouponCategories($couponID);

            $this->history();
        }
        else {
            $couponID   = 0;
            $action     = $this->url->link('sale/coupon/insert', 'token=' . $this->session->data['token'], 'SSL');
            $couponInfo = array();
        }

        $fields = array(
            'name'            => '',
            'code'            => '',
            'type'            => '',
            'discount'        => '',
            'logged'          => '',
            'shipping'        => '',
            'total'           => '',
            'date_start'      => Formatter::date(time()),
            'date_end'        => Formatter::date(strtotime('+1 month')),
            'uses_total'      => 1,
            'uses_customer'   => 1,
            'status'          => 1,
            'coupon_product'  => array(),
            'coupon_category' => array()
        );

        foreach ($fields as $field => $defaultValue) {
            if (isset($this->request->post[$field])) {
                $fields[$field] = $this->request->post[$field];
            }
            elseif (isset($couponInfo[$field])) {
                if ($field == 'date_start' || $field == 'date_end') {
                    if ($couponInfo[$field] != '0000-00-00') {
                        $fields[$field] = Formatter::date($couponInfo[$field]);
                    }
                    else {
                        $fields[$field] = '';
                    }
                } else {
                    $fields[$field] = $couponInfo[$field];
                }
            }
        }

        $this->data = array_merge($this->data, $fields, array(
            'error'         => !empty($this->error) ? implode($this->error, '<br />') : false,
            'coupon_id'     => $couponID,
            'action'        => $action,
            'cancel'        => $this->url->link('sale/coupon', 'token=' . $this->session->data['token'], 'SSL'),
            'token'         => $this->session->data['token'],
        ));

        // List selectable products
        foreach ($this->model_catalog_product->getProducts() as $product) {
            $this->data['products'][] = array(
                'product_id' => $product['product_id'],
                'name'       => $product['name'],
                'selected'   => !in_array($product['product_id'], $fields['coupon_product']) || empty($fields['coupon_product']) ? false : true
            );
        }

        // List selectable pcategories
        foreach ($this->model_catalog_category->getCategoriesAsList() as $category) {
            $this->data['categories'][] = array(
                'category_id' => $category['category_id'],
                'name'        => $category['name'],
                'level'       => $category['level'],
                'selected'    => !in_array($category['category_id'], $fields['coupon_category']) || empty($fields['coupon_category']) ? false : true
            );
        }

        $this->document->addStyle('view/css/pages/coupon.css');

        $this->template = 'sale/coupon_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
      }

    protected function validateForm()
    {
        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 128)) {
            $this->error['name'] = Language::getVar('SUMO_ERROR_NAME');
        }

        if ((utf8_strlen($this->request->post['code']) < 3) || (utf8_strlen($this->request->post['code']) > 50)) {
            $this->error['code'] = Language::getVar('SUMO_ERROR_CODE');
        }

        if (!empty($this->request->post['date_start']) && !empty($this->request->post['date_end']) && strtotime(Formatter::dateReverse($this->request->post['date_end'])) < strtotime(Formatter::dateReverse($this->request->post['date_start']))) {
            $this->error[] = Language::getVar('SUMO_ERROR_DATE_END_BEFORE_START');
        }

        $coupon_info = $this->model_sale_coupon->getCouponByCode($this->request->post['code']);

        if ($coupon_info) {
            if (!isset($this->request->get['coupon_id'])) {
                $this->error['warning'] = Language::getVar('SUMO_ERROR_CODE_EXISTS');
            }
            elseif ($coupon_info['coupon_id'] != $this->request->get['coupon_id'])  {
                $this->error['warning'] = Language::getVar('SUMO_ERROR_CODE_EXISTS');
            }
        }

        if (!$this->error) {
              return true;
        }
        return false;
    }

    protected function validateDelete()
    {
        if (!$this->error) {
            return true;
        }
        return false;
    }

    public function history()
    {
        $this->load->model('sale/coupon');

        $this->data['histories'] = array();

        $results = $this->model_sale_coupon->getCouponHistories($this->request->get['coupon_id'], 0, 10);

        foreach ($results as $result) {
            $this->data['histories'][] = array_merge($result, array(
                'date_added' => Formatter::date($result['date_added'])
            ));
        }
    }
}
