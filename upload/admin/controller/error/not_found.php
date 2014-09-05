<?php
namespace Sumo;
class ControllerErrorNotFound extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ERROR_NOT_FOUND_TITLE'));

        $this->data['heading_title'] = Language::getVar('SUMO_ERROR_NOT_FOUND_TITLE');

        $this->data['text_not_found'] = Language::getVar('SUMO_ERROR_NOT_FOUND_CONTENT');

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ERROR_NOT_FOUND_TITLE'),
        ));

        $this->template = 'error/not_found.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }
}
