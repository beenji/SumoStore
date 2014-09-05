<?php
namespace Sumo;
class ControllerInformationInformation extends Controller
{
    public function index()
    {
        $this->load->model('catalog/information');
        $this->data['type'] = 'information';
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        if (isset($this->request->get['information_id'])) {
            $information_id = (int)$this->request->get['information_id'];
        }
        else {
            $information_id = 0;
        }

        $information_info = $this->model_catalog_information->getInformation($information_id);

        if ($information_info) {
            $this->document->setTitle($information_info['title']);
            if (!empty($information_info['parent_id'])) {
                $parent = $this->model_catalog_information->getInformation($information_info['parent_id']);
                $this->data['breadcrumbs'][] = array(
                    'text'  => $parent['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $parent['information_id'])
                );
            }
            $this->data['breadcrumbs'][] = array(
                'text'      => $information_info['title'],
                'href'      => $this->url->link('information/information', 'information_id=' .  $information_id),
            );

            $this->data['heading_title'] = $information_info['title'];
            $this->data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
            $this->data['settings'] = $this->config->get('details_information_information_' . $this->config->get('template'));

            if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'informationTree', 'data' => $information_info));
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'usp', 'location' => 'information'));
                $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'banner', 'location' => 'information', 'data' => $information_info));
                $this->data['settings']['bottom'][] = $this->getChild('app/widgetsimpleproduct/', array('type' => 'latest', 'limit' => 6));
            }

            $this->template = 'information/content.tpl';
            $this->children = array(
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        }
        else {
            $this->redirect($this->url->link('error/not_found'));
        }
    }
}
