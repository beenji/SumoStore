<?php
namespace Sumo;
class ControllerErrorNotFound extends Controller
{
    public function index()
    {
        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');
        $this->document->setTitle(Language::getVar('SUMO_ERROR_NOT_FOUND_TITLE'));

        $this->data['title'] = Language::getVar('SUMO_ERROR_NOT_FOUND_TITLE');
        $this->data['content'] = Language::getVar('SUMO_ERROR_NOT_FOUND_CONTENT');
        $this->data['continue'] = $this->url->link('common/home');

        $this->template = 'error/not_found.tpl';

        $this->children = array(
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}
