<?php
namespace Sumo;
class ControllerToolBackup extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_SETTINGS_BACKUP'));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'),
            'href'      => $this->url->link('settings/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_SETTINGS_BACKUP'),
        ));

        $this->load->model('tool/backup');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'tool/backup')) {
            if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
                $content = file_get_contents($this->request->files['import']['tmp_name']);
            } else {
                $content = false;
            }

            $content = false;

            if ($content) {
                $this->model_tool_backup->restore($content);

                $this->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
            } else {
                $this->error['warning'] = Language::getVar('SUMO_ERROR_EMPTY_FILE');
            }
        }

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];

            unset($this->session->data['error']);
        }
        elseif (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        }
        else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        }
        else {
            $this->data['success'] = '';
        }

        $this->data = array_merge($this->data, array(
            'restore'   => $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'),
            'backup'    => $this->url->link('tool/backup/backup', 'token=' . $this->session->data['token'], 'SSL'),
            'tables'    => $this->model_tool_backup->getTables()
        ));

        $this->document->addScript('view/js/pages/backup.js');

        $this->template = 'tool/backup.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function backup()
    {
        if (!isset($this->request->post['backup'])) {
            $this->session->data['error'] = Language::getVar('SUMO_ERROR_BACKUP');

            $this->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
        }
        elseif ($this->user->hasPermission('modify', 'tool/backup')) {
            $this->response->addheader('Pragma: public');
            $this->response->addheader('Expires: 0');
            $this->response->addheader('Content-Description: File Transfer');
            $this->response->addheader('Content-Type: application/octet-stream');
            $this->response->addheader('Content-Disposition: attachment; filename=sumostore_backup_' . date('Y-m-d_H-i-s', time()).'.sql');
            $this->response->addheader('Content-Transfer-Encoding: binary');

            $this->load->model('tool/backup');

            $this->response->setOutput($this->model_tool_backup->backup($this->request->post['backup']));
        }
        else {
            $this->session->data['error'] = Language::getVar('SUMO_ERROR_NO_PERMISSION');
            $this->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
        }
    }
}
