<?php
namespace Sumo;
class ControllerSettingsEmails extends Controller
{
    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_STATUS_MAILS'));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_STATUS_MAILS')));

        $emails = Database::fetchAll("SELECT mail_id, name FROM PREFIX_mails ORDER BY name ASC");
        $this->data['emails'] = array();
        foreach ($emails as $list) {
            $content = Database::fetchAll("SELECT content_id, language_id, title, content FROM PREFIX_mails_content WHERE mail_id = :id", array('id' => $list['mail_id']));
            foreach ($content as $list2) {
                $list['lang'][$list2['language_id']] = $list2;
            }
            $event = Database::query("SELECT event_key FROM PREFIX_mails_to_events WHERE mail_id = :id", array('id' => $list['mail_id']))->fetch();
            $list['event_key'] = $event['event_key'];
            $this->data['emails'][$list['mail_id']] = $list;
        }

        $this->template = 'settings/emails/overview.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function update()
    {
        $mail_id = !empty($this->request->get['mail_id']) ? $this->request->get['mail_id'] : false;

        $this->data['mail'] = array();
        if ($mail_id) {
            $title = Language::getVar('SUMO_ADMIN_STATUS_MAILS_EDIT');

            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                $continue = true;
                $check = Database::query("SELECT mail_id FROM PREFIX_mails WHERE name = :name AND mail_id != :id", array('name' => $this->request->post['name'], 'id' => $mail_id))->fetch();
                if ((is_array($check) && !empty($check['mail_id'])) || empty($this->request->post['name'])) {
                    $this->data['error'] = Language::getVar('SUMO_ADMIN_STATUS_MAILS_ERROR_NAME');
                    $continue = false;
                }

                if ($continue) {
                    $check = Database::query("SELECT mail_id FROM PREFIX_mails_to_events WHERE event_key = :key AND mail_id != :id", array('key' => $this->request->post['event_key'], 'id' => $mail_id))->fetch();
                    if ((is_array($check) && !empty($check['mail_id'])) || empty($this->request->post['event_key'])) {
                        $this->data['error'] = Language::getVar('SUMO_ADMIN_STATUS_MAILS_ERROR_EVENT');
                        $continue = false;
                    }
                }

                if ($continue) {
                    Database::query("UPDATE PREFIX_mails SET name = :name WHERE mail_id = :id", array('name' => $this->request->post['name'], 'id' => $mail_id));
                    Database::query("DELETE FROM PREFIX_mails_content WHERE mail_id = :id", array('id' => $mail_id));
                    foreach ($this->request->post['content'] as $language_id => $content) {
                        Database::query(
                            "INSERT INTO PREFIX_mails_content
                            SET mail_id     = :mail,
                                language_id = :lang,
                                title       = :title,
                                content     = :content",
                            array(
                                'mail'      => $mail_id,
                                'lang'      => $language_id,
                                'title'     => $content['title'],
                                'content'   => $content['content']
                            )
                        );
                    }
                    Database::query("UPDATE PREFIX_mails_to_events SET event_key = :event WHERE mail_id = :id", array('event' => $this->request->post['event_key'], 'id' => $mail_id));

                    $this->backToHome();
                }
            }

            $info = Database::query("SELECT name FROM PREFIX_mails WHERE mail_id = :id", array('id' => $mail_id))->fetch();
            if (!count($info) || empty($info['name'])) {
                $this->backToHome();
            }

            $this->data['mail']['name'] = $info['name'];

            $content = Database::fetchAll("SELECT content_id, language_id, title, content FROM PREFIX_mails_content WHERE mail_id = :id", array('id' => $mail_id));
            foreach ($content as $list2) {
                $this->data['mail']['content'][$list2['language_id']] = $list2;
            }
            $event = Database::query("SELECT event_key FROM PREFIX_mails_to_events WHERE mail_id = :id", array('id' => $mail_id))->fetch();
            $this->data['mail']['event_key'] = $event['event_key'];
        }
        else {
            $title = Language::getVar('SUMO_ADMIN_STATUS_MAILS_ADD');

            if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                $continue = true;

                $this->data['mail']['name'] = $this->request->post['name'];
                $this->data['mail']['event_key'] = $this->request->post['event_key'];
                $this->data['mail']['content'] = $this->request->post['content'];

                $check = Database::query("SELECT mail_id FROM PREFIX_mails WHERE name = :name", array('name' => $this->request->post['name']))->fetch();
                if ((is_array($check) && !empty($check['mail_id'])) || empty($this->request->post['name'])) {
                    $this->data['error'] = Language::getVar('SUMO_ADMIN_STATUS_MAILS_ERROR_NAME');
                    $continue = false;
                }

                if ($continue) {
                    $check = Database::query("SELECT mail_id FROM PREFIX_mails_to_events WHERE event_key = :key", array('key' => $this->request->post['event_key']))->fetch();
                    if ((is_array($check) && !empty($check['mail_id'])) || empty($this->request->post['event_key'])) {
                        $this->data['error'] = Language::getVar('SUMO_ADMIN_STATUS_MAILS_ERROR_EVENT');
                        $continue = false;
                    }
                }

                if ($continue) {
                    Database::query(
                        "INSERT INTO PREFIX_mails
                        SET name = :name",
                        array(
                            'name'  => $this->request->post['name']
                        )
                    );

                    $mail_id = Database::lastInsertId();

                    foreach ($this->request->post['content'] as $language_id => $content) {
                        Database::query(
                            "INSERT INTO PREFIX_mails_content
                            SET mail_id     = :mail,
                                language_id = :lang,
                                title       = :title,
                                content     = :content",
                            array(
                                'mail'      => $mail_id,
                                'lang'      => $language_id,
                                'title'     => $content['title'],
                                'content'   => $content['content']
                            )
                        );
                    }

                    Database::query("INSERT INTO PREFIX_mails_to_events SET mail_id = :mail, event_key = :key", array('mail' => $mail_id, 'key' => $this->request->post['event_key']));

                    $this->backToHome();
                }
            }
        }

        $this->document->setTitle($title);
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_STATUS_MAILS'), 'href' => $this->url->link('settings/emails', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => $title));

        $this->load->model('localisation/language');
        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        $this->template = 'settings/emails/form.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function preview()
    {
        $mail_id = !empty($this->request->get['mail_id']) ? $this->request->get['mail_id'] : false;

        if (!$mail_id) {
            $this->backToHome();
        }

        $content = Mailer::getTemplate($mail_id);

        $this->response->setOutput('<title>' . $content['title'] . '</title><strong>Subject: ' . $content['title'] . '</strong><br /><hr />' . html_entity_decode($content['content']));
    }

    public function remove()
    {
        $this->backToHome();
    }

    private function backToHome()
    {
        $this->redirect($this->url->link('settings/emails', '', 'SSL'));
        exit;
    }
}
