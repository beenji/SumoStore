<?php
namespace Widgetsimplesidebar;
use App;
use Sumo;
class ControllerWidgetsimplesidebar extends App\Controller
{
    public function index()
    {
        $this->load->model('settings/stores');
        $this->load->appModel('Settings');


        $this->document->setTitle(Sumo\Language::getVar('APP_WIDGET_SS_TITLE'));
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('common/apps', '', 'SSL'),
            'text' => Sumo\Language::getVar('SUMO_ADMIN_APPS_DASHBOARD')
        ));
        $this->document->addBreadcrumbs(array(
            'text' => Sumo\Language::getVar('APP_WIDGET_SS_TITLE')
        ));
        $this->data['stores'] = $this->model_settings_stores->getStores();
        foreach ($this->data['stores'] as $list) {
            $this->widgetsimplesidebar_model_settings->setAppStatus($list['store_id'], 1);
        }

        $this->template = 'index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function banner()
    {
        if (empty($this->request->get['store_id'])) {
            $this->request->get['store_id'] = 0;
        }

        $this->load->appModel('Banner');
        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        $this->document->setTitle(Sumo\Language::getVar('APP_WIDGET_SS_BANNER_PLURAL_EDIT'));
        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('common/apps', '', 'SSL'),
            'text' => Sumo\Language::getVar('SUMO_ADMIN_APPS_DASHBOARD')
        ));
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('app/widgetsimplesidebar', '', 'SSL'),
            'text' => Sumo\Language::getVar('APP_WIDGET_SS_TITLE')
        ));
        $this->document->addBreadcrumbs(array(
            'text' => Sumo\Language::getVar('APP_WIDGET_SS_BANNER_PLURAL_EDIT')
        ));
        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['current_store'] = $this->request->get['store_id'];
        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        $this->data['banners'] = array();

        $banners = $this->widgetsimplesidebar_model_banner->getBanners($this->request->get['store_id']);
        foreach ($banners as $id => $list) {
            if (!empty($list['image'])) {
                $list['image'] = $this->model_tool_image->resize($list['image'], 60, 60);
            }
            else {
                $list['image'] = 'niet-gevonden';
                unset($list['image']);
            }
            $this->data['banners'][$id] = $list;
        }
        $this->template = 'banner.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function usp()
    {
        if (empty($this->request->get['store_id'])) {
            $this->request->get['store_id'] = 0;
        }

        $this->load->appModel('Usp');
        $this->load->model('localisation/language');
        $this->load->model('tool/image');

        $this->document->setTitle(Sumo\Language::getVar('APP_WIDGET_SS_USP_PLURAL_EDIT'));
        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('common/apps', '', 'SSL'),
            'text' => Sumo\Language::getVar('SUMO_ADMIN_APPS_DASHBOARD')
        ));
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('app/widgetsimplesidebar', '', 'SSL'),
            'text' => Sumo\Language::getVar('APP_WIDGET_SS_TITLE')
        ));
        $this->document->addBreadcrumbs(array(
            'text' => Sumo\Language::getVar('APP_WIDGET_SS_USP_PLURAL_EDIT')
        ));
        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['current_store'] = $this->request->get['store_id'];
        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        $this->data['usps'] = array();

        $usps = $this->widgetsimplesidebar_model_usp->getUsps($this->request->get['store_id']);
        foreach ($usps as $id => $list) {
            if (!empty($list['image'])) {
                $list['image'] = $this->model_tool_image->resize($list['image'], 60, 60);
            }
            else {
                $list['image'] = 'niet-gevonden';
                unset($list['image']);
            }
            $this->data['usps'][$id] = $list;
        }
        $this->template = 'usp.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    public function ajax()
    {
        $json = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (empty($this->request->get['store_id'])) {
                $this->request->get['store_id'] = 0;
            }
            switch ($this->request->post['request']) {
                default:
                    break;

                case 'total':
                    if ($this->request->post['type'] == 'banner') {
                        $this->load->appModel('Banner');
                        $json['total'] = $this->widgetsimplesidebar_model_banner->count($this->request->get['store_id']);
                    }
                    else {
                        $this->load->appModel('USP');
                        $json['total'] = $this->widgetsimplesidebar_model_usp->count($this->request->get['store_id']);
                    }
                    break;

                case 'banner':
                    $this->load->appModel('Banner');
                    parse_str(str_replace('&amp;', '&', $this->request->post['data']), $post);
                    $edit = $post['banner'];

                    $banners = $this->widgetsimplesidebar_model_banner->getBanners($this->request->get['store_id']);

                    if ($edit['id']) {
                        $banners[$edit['id']] = $edit;
                    }
                    else {
                        $banners[count($banners) + 1] = $edit;
                    }

                    $this->widgetsimplesidebar_model_banner->setBanners($this->request->get['store_id'], $banners);
                    $json['success'] = true;
                    break;

                case 'banner-edit':
                    $this->load->appModel('Banner');
                    $banners = $this->widgetsimplesidebar_model_banner->getBanners($this->request->get['store_id']);
                    if (isset($banners[$this->request->post['id']])) {
                        $json['data'] = $banners[$this->request->post['id']];
                    }
                    else {
                        $json['error'] = 'NON_EXISTING';
                    }
                    break;



                case 'banner-delete':
                    $this->load->appModel('Banner');
                    $banners = $this->widgetsimplesidebar_model_banner->getBanners($this->request->get['store_id']);
                    if (isset($banners[$this->request->post['id']])) {
                        unset($banners[$this->request->post['id']]);
                    }
                    $new = array();
                    $count = 1;
                    foreach ($banners as $id => $data) {
                        $new[$count] = $data;
                        $count++;
                    }

                    $this->widgetsimplesidebar_model_banner->setBanners($this->request->get['store_id'], $new);
                    $json['success'] = true;
                    break;

                case 'usp':
                    $this->load->appModel('Usp');
                    parse_str(str_replace('&amp;', '&', $this->request->post['data']), $post);
                    $edit = $post['usp'];

                    $usps = $this->widgetsimplesidebar_model_usp->getUsps($this->request->get['store_id']);

                    if ($edit['id']) {
                        $usps[$edit['id']] = $edit;
                    }
                    else {
                        $usps[count($usps) + 1] = $edit;
                    }

                    $this->widgetsimplesidebar_model_usp->setUsps($this->request->get['store_id'], $usps);
                    $json['success'] = true;
                    break;

                case 'usp-edit':
                    $this->load->appModel('Usp');
                    $usps = $this->widgetsimplesidebar_model_usp->getUsps($this->request->get['store_id']);
                    if (isset($usps[$this->request->post['id']])) {
                        $json['data'] = $usps[$this->request->post['id']];
                    }
                    else {
                        $json['error'] = 'NON_EXISTING';
                    }
                    break;

                case 'usp-delete':
                    $this->load->appModel('Usp');
                    $banners = $this->widgetsimplesidebar_model_usp->getUsps($this->request->get['store_id']);
                    if (isset($usps[$this->request->post['id']])) {
                        unset($usps[$this->request->post['id']]);
                    }
                    $new = array();
                    $count = 1;
                    foreach ($usps as $id => $data) {
                        $new[$count] = $data;
                        $count++;
                    }

                    $this->widgetsimplesidebar_model_usp->setUsps($this->request->get['store_id'], $new);
                    $json['success'] = true;
                    break;
            }
        }
        else {
            $json['error'] = 'REQUEST_METHOD != POST';
        }
        $this->response->setOutput(json_encode($json));
    }
}
