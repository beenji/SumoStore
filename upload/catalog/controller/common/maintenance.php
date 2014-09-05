<?php
namespace Sumo;
class ControllerCommonMaintenance extends Controller
{
    public function index()
    {
        if ($this->config->get('in_maintenance')) {
            $route = '';

            if (isset($this->request->get['route'])) {
                $part = explode('/', $this->request->get['route']);
                if (isset($part[0])) {
                    $route .= $part[0];
                }
            }

            // Show site if logged in as admin
            $this->load->library('user');
            $this->user = new \User($this->registry);
            $forward = true;
            if (!$this->user->isLogged()) {
                $routes = array('information', 'payment', 'contact', 'product', 'category');
                foreach ($routes as $search) {
                    if (!empty($route) && stristr($search, $route)) {
                        $forward = false;
                    }
                }
            }
            else {
                $forward = false;
            }

            if ($forward) {
                return $this->forward('common/maintenance/info');
            }
        }
    }

    public function info()
    {
        $this->document->setTitle(Language::getVar('SUMO_MAINTENANCE_TITLE'));

        $this->data['title'] = Language::getVar('SUMO_MAINTENANCE_TITLE');

        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_MAINTENANCE_TITLE'),
            'href'      => $this->url->link('common/maintenance')
        );

        $this->data['debug'] = false;

        $this->template = 'error/not_found.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}
