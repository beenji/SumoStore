<?php
namespace Sumo;
class ControllerCommonHome extends Controller
{
    public function index()
    {
        $this->document->setTitle($this->config->get('title'));
        $meta = $this->config->get('meta_description');
        if (isset($meta[$this->config->get('language_id')])) {
            $this->document->setDescription($meta[$this->config->get('language_id')]);
        }
        else {
            $this->document->setDescription(reset($meta));
        }

        $this->data['heading_title'] = $this->config->get('title');

        $this->template = 'common/home.tpl';
        $this->children = array('common/header', 'common/footer');

        $this->response->setOutput($this->render());
    }
}
