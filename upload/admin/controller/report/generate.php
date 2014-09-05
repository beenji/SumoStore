<?php
namespace Sumo;
class ControllerReportGenerate extends Controller
{
    public function index()
    {
        $this->redirect($this->url->link('report/dashboard'));
    }

    public function sales()
    {
        $this->generateData('sales');
    }

    public function customer()
    {
        $this->generateData('customer');
    }

    public function taxes()
    {
        $this->generateData('taxes');
    }

    public function returns()
    {
        $this->generateData('returns');
    }

    public function coupons()
    {
        $this->generateData('coupons');
    }

    public function productsViewed()
    {
        $this->generateData('products_viewed');
    }

    public function productsSales()
    {
        $this->generateData('products_sales');
    }

    private function generateData($type)
    {
        $this->data['table_head'] = $this->data['statuses'] = array();
        $this->data['disable_dates'] = false;
        $title = '';

        switch ($type) {

            case 'customer':
                $title = Language::getVar('SUMO_ADMIN_REPORT_CUSTOMER');

                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_ORDER_DATE');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_CUSTOMER_SINGULAR');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_ORDERS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_PRODUCTS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_TAX_AMOUNT');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_TOTAL');

                $this->load->model('localisation/order_status');
                foreach ($this->model_localisation_order_status->getOrderStatuses() as $list) {
                    $this->data['statuses'][] = array(
                        'status_id' => $list['order_status_id'],
                        'name'      => $list['name']
                    );
                }
                break;

            case 'sales':
                $title = Language::getVar('SUMO_ADMIN_REPORT_SALES');

                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_DATE_START');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_DATE_END');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_ORDERS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_PRODUCTS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_TAX_AMOUNT');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_TOTAL');

                $this->load->model('localisation/order_status');
                foreach ($this->model_localisation_order_status->getOrderStatuses() as $list) {
                    $this->data['statuses'][] = array(
                        'status_id' => $list['order_status_id'],
                        'name'      => $list['name']
                    );
                }
                break;

            case 'returns':
                $title = Language::getVar('SUMO_ADMIN_REPORT_RETURNS');

                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_DATE_START');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_DATE_END');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_RETURNS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_STATUS');

                $this->load->model('localisation/return_status');
                foreach ($this->model_localisation_return_status->getReturnStatuses() as $list) {
                    $this->data['statuses'][] = array(
                        'status_id' => $list['return_status_id'],
                        'name'      => $list['name']
                    );
                }
                break;

            case 'coupons':
                $title = Language::getVar('SUMO_ADMIN_REPORT_COUPONS');

                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_DATE_START');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_DATE_END');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_NAME');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_COUPON_CODE');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_ORDERS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_TOTAL');
                break;

            case 'products_viewed':
                $title = Language::getVar('SUMO_ADMIN_REPORT_PRODUCTS_VIEWED');

                $this->data['disable_dates'] = true;

                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_NAME');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_MODEL');
                $this->data['table_head'][] = Language::getvar('SUMO_NOUN_VIEWS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_PERCENTAGE');

                $this->data['statuses'][] = array(
                    'status_id' => 1,
                    'name'      => Language::getVar('SUMO_NOUN_PRODUCT_ACTIVE')
                );
                $this->data['statuses'][] = array(
                    'status_id' => 2,
                    'name'      => Language::getVar('SUMO_NOUN_PRODUCT_INACTIVE')
                );
                break;

            case 'products_sales':
                $title = Language::getVar('SUMO_ADMIN_REPORT_PRODUCTS_SALES');

                $this->data['disable_group'] = true;

                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_NAME');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_MODEL');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_VIEWS');
                $this->data['table_head'][] = Language::getvar('SUMO_NOUN_ORDERS');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_TOTAL');
                $this->data['table_head'][] = Language::getVar('SUMO_NOUN_PERCENTAGE');
                break;
        }

        $this->document->setTitle($title);
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_REPORT_DASHBOARD'),
            'href'      => $this->url->link('report/dashboard'),
        ));
        $this->document->addBreadcrumbs(array(
            'text'      => $title,
        ));

        // Load required models
        $this->load->model('report/generate');

        // Setup filters and vars
        $this->data['groups'] = array(
            array(
                'text'  => Language::getVar('SUMO_NOUN_YEAR'),
                'value' => 'year',
            ),
            array(
                'text'  => Language::getVar('SUMO_NOUN_MONTH'),
                'value' => 'month',
            ),
            array(
                'text'  => Language::getVar('SUMO_NOUN_WEEK'),
                'value' => 'week',
            ),
            array(
                'text'  => Language::getVar('SUMO_NOUN_DAY'),
                'value' => 'day',
            )
        );

        $filter = array();
        $filter['date_start']       = Formatter::date(strtotime('first day of this month'));
        $filter['date_end']         = Formatter::date(strtotime('last day of this month'));
        $filter['group']            = 'week';
        $filter['status_id']        = 0;
        $filter['page']             = 0;

        $url = '';
        foreach ($filter as $key => $value) {
            if (!empty($this->request->get[$key]) && $key != 'page') {
                $filter[$key] = $this->request->get[$key];
                $url .= '&' . $key . '=' . $this->request->get[$key];
            }
        }
        foreach ($filter as $key => $value) {
            $this->data[$key] = $value;
        }

        $total = $this->model_report_generate->getTotal($type, $filter);

        $filter['page']             = !empty($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $filter['start']            = ($filter['page'] - 1) * 25;
        $filter['limit']            = 25;

        // Generate pagination before applying the page to the url string
        $pagination                 = new Pagination();
        $pagination->total          = $total;
        $pagination->page           = $filter['page'];
        $pagination->limit          = 25;
        $pagination->text           = '';
        $pagination->url            = $this->url->link('report/generate/' . str_replace('_', '', $type), 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination']   = $pagination->renderAdmin();
        $this->data['reset']        = $this->url->link('report/generate/' . str_replace('_', '', $type), 'token=' . $this->session->data['token'], 'SSL');
        $this->data['items']        = array();
        foreach ($this->model_report_generate->getData($type, $filter) as $time => $list) {
            $this->data['items'][$time]  = $list;
        }


        $this->template = 'report/generate.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }
}
