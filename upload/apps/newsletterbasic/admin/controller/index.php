<?php
namespace Newsletterbasic;
use Sumo;
use App;
class ControllerNewsletterbasic extends App\Controller
{
    public function index()
    {
        $this->load->appModel('Setup');
        $this->load->model('localisation/country');
        $this->load->model('localisation/language');
        $this->load->model('sale/customer_group');

        $this->newsletterbasic_model_setup->activate(isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0);

        $this->document->setTitle(Sumo\Language::getVar('APP_NEWSLETTERBASIC_TITLE'));
        $this->document->addBreadcrumbs(array(
            'href' => $this->url->link('common/apps', '', 'SSL'),
            'text' => Sumo\Language::getVar('SUMO_ADMIN_APPS_DASHBOARD')
        ));
        $this->document->addBreadcrumbs(array(
            'text' => Sumo\Language::getVar('APP_NEWSLETTERBASIC_TITLE')
        ));
        $this->document->addScript('../app/newsletterbasic/admin/view/js/page.js');

        $this->data['stores'] = $this->model_settings_stores->getStores();
        $this->data['current_store'] = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;

        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        $this->data['countries'] = $this->model_localisation_country->getCountries();
        $this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

        $this->template = 'index.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function filter()
    {
        $data = $this->getData();

        $count = count($data);

        if ($count >= 2) {
            $this->response->setOutput(Sumo\Language::getVar('APP_NEWSLETTERBASIC_RECEIVERS_AMOUNT_PLURAL', $count));
        }
        else if ($count) {
            $this->response->setOutput(Sumo\Language::getVar('APP_NEWSLETTERBASIC_RECEIVERS_AMOUNT_SINGULAR'));
        }
        else {
            $this->response->setOutput(Sumo\Language::getVar('APP_NEWSLETTERBASIC_RECEIVERS_AMOUNT_NONE'));
        }
    }

    public function testMail()
    {
        $to     = $this->config->get('email');
        $mail   = $this->request->post['mail'];
        $lang   = $this->config->get('language_id');
        $store  = $this->model_settings_stores->getStore(isset($this->request->post['store_id']) ? (int)$this->request->post['store_id'] : 0);
        $data   = array(
            'email'     => $to,
            'firstname' => '',
            'lastname'  => $this->user->getUsername(),
            'name'      => $store['name'],
            'url'       => 'http://' . rtrim($store['base_http'], '/') . '/'
        );
        if ($this->sendMail($data, '[TESTMAIL] ' . $mail[$lang]['subject'], html_entity_decode($mail[$lang]['message']))) {
            $this->response->setOutput(Sumo\Language::getVar('APP_NEWSLETTERBASIC_TEST_MAIL_SENT', $to));
        }
        else {
            $this->response->setOutput(Sumo\Language::getVar('APP_NEWSLETTERBASIC_TEST_MAIL_FAILED', $to));
        }
    }

    public function sendBatchMail()
    {
        $json   = array();
        $store  = $this->model_settings_stores->getStore(isset($this->request->post['store_id']) ? (int)$this->request->post['store_id'] : 0);
        $page   = 1;
        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        }

        $this->request->post['start'] = ($page - 1) * 10;
        $this->request->post['limit'] = 10;

        $emails = $this->getData();
        if (count($emails)) {
            $json['next']       = str_replace('&amp;', '&', $this->url->link('app/newsletterbasic/sendbatchmail', 'token=' . $this->session->data['token'] . '&page=' . ($page + 1), 'SSL'));
            $json['success']    = Sumo\Language::getVar('APP_NEWSLETTERBASIC_BATCH_EMAILS_SENT', $page);
            $mail               = $this->request->post['mail'];
            foreach ($emails as $data) {
                $language = $this->config->get('language_id');
                if (isset($data['language_id']) && !empty($data['language_id'])) {
                    $language   = (int)$data['language_id'];
                }
                $data['name']   = $store['name'];
                $data['url']    = 'http://' . rtrim($store['base_http'], '/') . '/';
                $this->sendMail($data, $mail[$language]['subject'], html_entity_decode($mail[$language]['message']) . '<!-- Email send with SumoStore, leading E-commerce software -->');
            }
        }
        else {
            $json['next'] = '';
            $json['success'] = Sumo\Language::getVar('APP_NEWSLETTERBASIC_ALL_EMAILS_SENT');
        }
        $json['debug'] = $this->request->post;
        $json['debug_get'] = $this->request->get;
        $this->response->setOutput(json_encode($json));
    }

    private function getData()
    {
        $cache = 'customer.newsletterbasic.' . json_encode($this->request->post);
        $data = Sumo\Cache::find($cache);
        if (!is_array($data) || !count($data)) {
            $data = array();
            $advanced = !empty($this->request->post['filter']) ? 1 : 0;
            $sql = "SELECT c.customer_id, c.firstname, c.lastname, c.email, c.gender, c.birthdate, a.country_id, c.newsletter, c.customer_group_id FROM PREFIX_customer AS c LEFT JOIN PREFIX_address AS a ON c.address_id = a.address_id WHERE status = 1";
            if ($advanced) {
                // Add filters
                if (empty($this->request->post['mail_type'])) {
                    $sql .= " AND newsletter = 1";
                }

                if (!empty($this->request->post['country'])) {
                    $sql .= " AND country_id = " . (int)$this->request->post['country'];
                }

                if (!empty($this->request->post['gender']) && $this->request->post['gender'] != 'b' && in_array($this->request->post['gender'], array('m', 'f'))) {
                    $sql .= " AND gender = '" . $this->request->post['gender'] . "'";
                }

                if (!empty($this->request->post['age'])) {
                    $check = explode('-', $this->request->post['age']);
                    if (count($check) == 2) {
                        $sql .= " AND (birthdate <= '" . date('Y-m-d', strtotime((int)$check[0] . ' years ago')) . "' AND birthdate >= '" . date('Y-m-d', strtotime((int)$check[1] . ' years ago')) . "')";
                    }
                    else {
                        $sql .= " AND birthdate <= '" . date('Y-m-d', strtotime('55 years ago')) . "'";
                    }
                    $sql .= " OR birthdate = '0000-00-00'";
                }
            }
            else {
                switch ($this->request->post['to']) {
                    case 'newsletter':
                        $sql .= " AND newsletter = 1";
                        break;

                    case 'customer_group':
                        $sql .= " AND customer_group_id = " . (int)$this->request->post['customer_group_id'];
                        break;

                    default:
                        break;
                }
            }
            if (isset($this->request->post['start']) && isset($this->request->post['limit'])) {
                $sql .= ' LIMIT ' . (int)$this->request->post['start'] . ', ' . (int)$this->request->post['limit'];
            }
            $data = Sumo\Database::fetchAll($sql);
            Sumo\Cache::set($cache, $data);
        }

        return $data;
    }

    private function sendMail($data, $subject, $content)
    {
        $find = array('firstname', 'lastname', 'name', 'url');
        foreach ($find as $key) {
            $subject = str_replace('{' . $key . '}', isset($data[$key]) ? $data[$key] : $key . ' is not set', $subject);
            $content = str_replace('{' . $key . '}', isset($data[$key]) ? $data[$key] : $key . ' is not set', $content);
        }

        Sumo\Mail::setTo($data['email']);
        Sumo\Mail::setSubject($subject);
        Sumo\Mail::setHTML($content);
        $text = strip_tags($content);
        $text = substr($text, 0, 128);
        Sumo\Mail::setText($text);
        return Sumo\Mail::send();
    }
}
