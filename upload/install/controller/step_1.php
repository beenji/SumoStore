<?php
namespace Sumo;
class ControllerStep1 extends Controller
{
    private $error = array();

    public function index()
    {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->redirect(HTTP_SERVER . '?route=step_2');
        }

        $this->data['warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['warning'] = $this->error['warning'];
        }

        $this->data['action'] = HTTP_SERVER . '?route=step_1';

        $this->template = 'step_1.tpl';
        $this->children = array('header', 'footer');

        $this->response->setOutput($this->render());
    }

    private function validate()
    {
        if (!isset($this->request->post['agree'])) {
            $this->error['warning'] = $this->config->get('LANG_STEP_1_WARNING');
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }
}
