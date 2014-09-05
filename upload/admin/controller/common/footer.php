<?php
namespace Sumo;
class ControllerCommonFooter extends Controller
{
    protected function index()
    {
        //$this->data['language'] = $this->language->directory;

        if (isset($this->session->data['token'])) {
            $this->data['token'] = $this->session->data['token'];
        }

        $this->data['scripts'] = $this->document->getScripts();

        $this->template = 'common/footer.tpl';

        $this->render();
    }
}
