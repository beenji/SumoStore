<?php
namespace Sumo;
class ControllerCatalogSpecial extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_SPECIAL'));
        $this->load->model('catalog/special');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_SPECIAL'));
        $this->load->model('catalog/special');

        if (!empty($this->request->post) && ($formData = $this->validateForm($this->request->post)) !== false) {
            $this->model_catalog_special->addSpecial($formData);

            // Redirect to overview
            $this->redirect($this->url->link('catalog/special', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_SPECIAL'));
        $this->load->model('catalog/special');

        if (!empty($this->request->post) && ($formData = $this->validateForm($this->request->post)) !== false) {
            $this->model_catalog_special->editSpecial($this->request->get['product_special_id'], $formData);

            // Redirect to overview
            $this->redirect($this->url->link('catalog/special', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getList();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_SPECIAL'));
        $this->load->model('catalog/special');

        if (!empty($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $productSpecialID) {
                $this->model_catalog_special->deleteSpecial($productSpecialID);
            }
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_SPECIAL'),
        ));

        $this->data = array_merge($this->data, array(
            'delete'        => $this->url->link('catalog/special/delete', 'token=' . $this->session->data['token'], 'SSL'),
            'specials'      => array(),
            'error'         => isset($this->data['error']) ? $this->data['error'] : ''
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

        $special_total = $this->model_catalog_special->getTotalSpecials();

        foreach ($this->model_catalog_special->getSpecials($data) as $special) {
            $this->data['specials'][] = array_merge($special, array(
                'product_special_no'    => 'SID.' . str_pad($special['product_special_id'], 5, 0, STR_PAD_LEFT),
                'price'                 => Formatter::currency($special['price']),
                'product_price'         => Formatter::currency($special['product_price']),
                'edit'                  => $this->url->link('catalog/special/update', 'token=' . $this->session->data['token'] . '&product_special_id=' . $special['product_special_id'], 'SSL')
            ));
        }

        $pagination = new Pagination();
        $pagination->total = $special_total;
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('catalog/special', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();

        $this->getForm();

        $this->template = 'catalog/special.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        if (isset($this->request->get['product_special_id'])) {
            $productSpecialData = $this->model_catalog_special->getSpecial($this->request->get['product_special_id']);
            $formAction = $this->url->link('catalog/special/update', 'token=' . $this->session->data['token'] . '&product_special_id=' . $this->request->get['product_special_id'], 'SSL');
        } else {
            $formAction = $this->url->link('catalog/special/insert', 'token=' . $this->session->data['token'], 'SSL');
        }

        $fields = array(
            'product'           => '',
            'product_id'        => 0,
            'discount'          => '',
            'discount_suffix'   => '',
            'date_start'        => Formatter::date(time()),
            'date_end'          => Formatter::date(strtotime('+1 month'))
        );

        // Find value for field in A: Post-array or B: Existing specialdata array
        foreach ($fields as $key => $defaultVal) {
            if (isset($this->request->post[$key])) {
                $fields[$key] = $this->request->post[$key];
            }
            elseif (isset($productSpecialData[$key])) {
                if ($key == 'date_start' || $key == 'date_end') {
                    $fields[$key] = Formatter::date($productSpecialData[$key]);
                } else {
                    $fields[$key] = $productSpecialData[$key];
                }
            }
        }

        $this->data = array_merge($this->data, $fields, array(
            'cancel'        => $this->url->link('catalog/special', 'token=' . $this->session->data['token'], 'SSL'),
            'action'        => $formAction,
            'token'         => $this->request->get['token']
        ));

        $this->document->addScript('view/js/jquery/jquery.autocomplete.js');
        $this->document->addScript('view/js/pages/special.js');
    }

    protected function validateForm($data)
    {
        // Set new product price
        $this->load->model('catalog/product');

        $productData = $this->model_catalog_product->getProduct($data['product_id']);

        if (!$productData) {
            $this->data['error'] = Language::getVar('SUMO_ERROR_NO_PRODUCT_FOR_DISCOUNT');

            return false;
        }

        $this->request->post['price'] = $productData['price'];

        // Comma's, decimals..
        $data['discount'] = str_replace(',', '.', $data['discount']);

        if (mb_substr(trim($data['discount']), -1) == '%') {
            $discount = trim($data['discount']);
            $discount = mb_substr($discount, 0, mb_strlen($discount) - 1);
            $discount = floatval($discount);

            if ($discount <= 100) {
                $data['price'] = $productData['price'] - ($productData['price'] * ($discount / 100));
            }

            $data['discount_suffix'] = '%';
        } else {
            if (floatval($data['discount']) > $productData['price']) {
                $data['price'] = 0;
            } else {
                $data['price'] = $productData['price'] - floatval($data['discount']);
            }

            // Amount, no suffix necessary
            $data['discount_suffix'] = '';
        }

        // Check if dates are correct
        $data['date_start'] = Formatter::dateReverse($data['date_start']);
        $data['date_end'] = Formatter::dateReverse($data['date_end']);

        if (strtotime($data['date_end']) < strtotime($data['date_start'])) {
            $this->data['error'] = Language::getVar('SUMO_ERROR_END_BEFORE_START');
            return false;
        }

        return $data;
    }
}
