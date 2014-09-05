<?php
namespace Sumo;
class ControllerCatalogReview extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_REVIEW'));

        $this->load->model('catalog/review');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_REVIEW_ADD'));

        $this->load->model('catalog/review');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_review->addReview($this->request->post);

            $this->redirect($this->url->link('catalog/review', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_REVIEW_UPDATE'));

        $this->load->model('catalog/review');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_review->editReview($this->request->get['review_id'], $this->request->post);

            $this->redirect($this->url->link('catalog/review', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_REVIEW_DELETE'));

        $this->load->model('catalog/review');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $review_id) {
                $this->model_catalog_review->deleteReview($review_id);
            }

            $this->redirect($this->url->link('catalog/review', 'token=' . $this->session->data['token'], 'SSL'));
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
            'text'      => Language::getVar('SUMO_ADMIN_REVIEW'),
        ));

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        }
        else {
            $page = 1;
        }

        $data = array(
            'start' => ($page - 1) * $this->config->get('admin_limit'),
            'limit' => $this->config->get('admin_limit')
        );

        $reviewTotal = $this->model_catalog_review->getTotalReviews();

        $pagination = new Pagination();
        $pagination->total = $reviewTotal;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('admin_limit');
        $pagination->text  = '';
        $pagination->url   = $this->url->link('catalog/review', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $this->data = array_merge($this->data, array(
            'reviews'       => array(),
            'insert'        => $this->url->link('catalog/review/insert', 'token=' . $this->session->data['token'], 'SSL'),
            'delete'        => $this->url->link('catalog/review/delete', 'token=' . $this->session->data['token'], 'SSL'),
            'pagination'    => $pagination->renderAdmin()
        ));

        foreach ($this->model_catalog_review->getReviews($data) as $result) {
            $this->data['reviews'][] = array_merge($result, array(
                'status'        => $result['status'] ? Language::getVar('SUMO_NOUN_ENABLED') : Language::getVar('SUMO_NOUN_DISABLED'),
                'date_added'    => Formatter::date($result['date_added']),
                'edit'          => $this->url->link('catalog/review/update', 'token=' . $this->session->data['token'] . '&review_id=' . $result['review_id'], 'SSL')
            ));
        }

        $this->template = 'catalog/review_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_REVIEW'),
            'href'      => $this->url->link('catalog/review')
        ));

        if (!isset($this->request->get['review_id'])) {
            $reviewID   = 0;
            $action     = $this->url->link('catalog/review/insert', 'token=' . $this->session->data['token'], 'SSL');

            $this->document->addBreadcrumbs(array(
                'text'      => Language::getVar('SUMO_ADMIN_REVIEW_ADD')
            ));
        } else {
            $reviewID   = $this->request->get['review_id'];
            $reviewInfo = $this->model_catalog_review->getReview($reviewID);
            $action     = $this->url->link('catalog/review/update', 'token=' . $this->session->data['token'] . '&review_id=' . $reviewID, 'SSL');

            $this->document->addBreadcrumbs(array(
                'text'      => Language::getVar('SUMO_ADMIN_REVIEW_UPDATE')
            ));
        }

        $fields = array(
            'product_id' => 0,
            'product'    => '',
            'author'     => '',
            'text'       => '',
            'rating'     => '',
            'status'     => '',
            'date_added' => Formatter::date(time())
        );

        foreach ($fields as $field => $defaultValue) {
            if (isset($this->request->post[$field])) {
                $fields[$field] = $this->request->post[$field];
            }
            elseif (isset($reviewInfo[$field])) {
                if ($field == 'date_added') {
                    $fields[$field] = Formatter::date($reviewInfo[$field]);
                }
                else {
                    $fields[$field] = $reviewInfo[$field];
                }
            }
        }

        $this->data = array_merge($this->data, $fields, array(
            'action'     => $action,
            'error'      => implode('<br />', $this->error),
            'cancel'     => $this->url->link('catalog/review', 'token=' . $this->session->data['token'], 'SSL'),
            'token'      => $this->session->data['token']
        ));

        $this->document->addScript('view/js/jquery/jquery.autocomplete.js');
        $this->document->addScript('view/js/pages/review_form.js');

        $this->template = 'catalog/review_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'catalog/review')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if (!$this->request->post['product_id']) {
            $this->error['product'] = Language::getVar('SUMO_ERROR_NO_PRODUCT');
        }

        if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
            $this->error['author'] = Language::getVar('SUMO_ERROR_AUTHOR');
        }

        if (utf8_strlen($this->request->post['text']) < 1) {
            $this->error['text'] = Language::getVar('SUMO_ERROR_TEXT');
        }

        if (!isset($this->request->post['rating'])) {
            $this->error['rating'] = Language::getVar('SUMO_ERROR_NO_RATING');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'catalog/review')) {
            $this->error['warning'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
