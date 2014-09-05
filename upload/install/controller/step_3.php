<?php
namespace Sumo;
class ControllerStep3 extends Controller
{
    private $error = array();

    public function index()
    {
        $this->data['action'] = HTTP_SERVER . '?route=step_3';
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $output  = '<?php' . PHP_EOL;
            $output .= '// SumoStore configuration, created ' . date('d-m-Y H:i:s') . PHP_EOL;
            $output .= 'define(\'DB_HOSTNAME\',     \'' . addslashes($this->request->post['db_host']) . '\');' . PHP_EOL;
            $output .= 'define(\'DB_USERNAME\',     \'' . addslashes($this->request->post['db_user']) . '\');' . PHP_EOL;
            $output .= 'define(\'DB_PASSWORD\',     \'' . addslashes($this->request->post['db_password']) . '\');' . PHP_EOL;
            $output .= 'define(\'DB_DATABASE\',     \'' . addslashes($this->request->post['db_name']) . '\');' . PHP_EOL;
            $output .= 'define(\'DB_PREFIX\',       \'' . addslashes($this->request->post['db_prefix']) . '\');';

            $file = fopen(DIR_SUMOSTORE . 'config.mysql.php', 'w');
            fwrite($file, $output);
            fclose($file);

            $file = fopen(DIR_SUMOSTORE . 'license.php', 'w');
            if (!empty($this->request->post['license_key'])) {
                fwrite($file, "<?php define('LICENSE_KEY', '" . $this->request->post['license_key'] . "'); ?>");
            }
            else {
                fwrite($file, "<?php define('LICENSE_KEY', ''); ?>");
            }
            fclose($file);

            try {
                Database::setup(array(
                    'hostname'  => $this->request->post['db_host'],
                    'username'  => $this->request->post['db_user'],
                    'password'  => $this->request->post['db_password'],
                    'database'  => $this->request->post['db_name'],
                    'prefix'    => $this->request->post['db_prefix']
                ));
            }
            catch (\Exception $e) {
                exit($e->getMessage());
            }

            $this->load->model('install');
            $this->model_install->start($this->request->post);

            $this->redirect(HTTP_SERVER . '?route=step_4');
        }

        if (count($this->error['warning'])) {
            $this->data['error'] = implode($this->error['warning'], '<br />');
        }

        $this->data['db_host'] = 'localhost';
        if (isset($this->request->post['db_host'])) {
            $this->data['db_host'] = $this->request->post['db_host'];
        }

        $this->data['db_user'] = '';
        if (isset($this->request->post['db_user'])) {
            $this->data['db_user'] = html_entity_decode($this->request->post['db_user']);
        }

        $this->data['db_password'] = '';
        if (isset($this->request->post['db_password'])) {
            $this->data['db_password'] = html_entity_decode($this->request->post['db_password']);
        }

        $this->data['db_name'] = '';
        if (isset($this->request->post['db_name'])) {
            $this->data['db_name'] = html_entity_decode($this->request->post['db_name']);
        }

        $this->data['db_prefix'] = 'sumo_';

        $this->data['username'] = '';
        if (isset($this->request->post['username'])) {
            $this->data['username'] = $this->request->post['username'];
        }

        $this->data['email'] = '';
        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        }

        $this->data['category'] = '';
        if (isset($this->request->post['category'])) {
            $this->data['category'] = $this->request->post['category'];
        }

        $this->data['store_name'] = '';
        if (isset($this->request->post['store_name'])) {
            $this->data['store_name'] = $this->request->post['store_name'];
        }

        $this->data['store_email'] = '';
        if (isset($this->request->post['store_email'])) {
            $this->data['store_email'] = $this->request->post['store_email'];
        }

        $this->data['license_key'] = '';
        if (isset($this->request->post['license_key'])) {
            $this->data['license_key'] = $this->request->post['license_key'];
        }
        else if (defined('LICENSE_KEY')) {
            $this->data['license_key'] = LICENSE_KEY;
        }


        $this->data['back'] = HTTP_SERVER . '?route=step_2';

        $this->template = 'step_3.tpl';
        $this->children = array(
            'header',
            'footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate($array = array())
    {
        if (count($array)) {
            $this->request->post = $array;
        }

        if (empty($this->request->post['db_host'])) {
            $errors['db_host'] = $this->config->get('LANG_ERROR_DB_HOSTNAME');
        }

        if (empty($this->request->post['db_user'])) {
            $errors['db_user'] = $this->config->get('LANG_ERROR_DB_USERNAME');
        }

        if (empty($this->request->post['db_prefix'])) {
            $errors['db_prefix'] = $this->config->get('LANG_ERROR_DB_PREFIX');
        }

        if (empty($this->request->post['db_name'])) {
            $errors['db_database'] = $this->config->get('LANG_ERROR_DB_DBNAME');
        }

        if (!empty($this->request->post['db_host']) && !empty($this->request->post['db_user']) && !empty($this->request->post['db_prefix']) && !empty($this->request->post['db_name'])) {
            try {
                Database::setup(array(
                    'hostname'  => $this->request->post['db_host'],
                    'username'  => $this->request->post['db_user'],
                    'password'  => $this->request->post['db_password'],
                    'database'  => $this->request->post['db_name'],
                    'prefix'    => $this->request->post['db_prefix']
                ));
            }
            catch (\Exception $e) {
                $errors['db_connection'] = $e->getMessage();
            }
        }

        if (empty($this->request->post['username'])) {
            $errors['username'] = $this->config->get('LANG_ERROR_USERNAME_EMPTY');
        }
        else if (in_array(strtolower($this->request->post['username']), array('admin', 'administrator', 'webmaster', 'shop', 'gebruiker', 'username', 'beheerder', 'sysadmin', 'sumo'))) {
            $errors['username'] = $this->config->get('LANG_ERROR_USERNAME_WEAK');
        }

        if (empty($this->request->post['password'])) {
            $errors['password'] = $this->config->get('LANG_ERROR_PASSWORD_EMPTY');
        }
        else if (strlen($this->request->post['password']) <= 5 || ctype_alpha($this->request->post['password']) || stristr($this->request->post['username'], $this->request->post['password'])) {
            $errors['password'] = $this->config->get('LANG_ERROR_PASSWORD_WEAK');
        }

        if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = $this->config->get('LANG_ERROR_EMAIL_INVALID');
        }

        if (empty($this->request->post['category'])) {
            $errors['category'] = $this->config->get('LANG_ERROR_CATEGORY');
        }
        if (empty($this->request->post['country_id'])) {
            $errors['country_id'] = $this->config->get('LANG_ERROR_COUNTRY');
        }
        else if ($this->request->post['country_id'] > 239) {
            $errors['country_id'] = $this->config->get('LANG_ERROR_COUNTRY');
        }

        if (empty($this->request->post['store_name'])) {
            $errors['store_name'] = $this->config->get('LANG_ERROR_NAME');
        }

        if (empty($this->request->post['store_email']) || !filter_var($this->request->post['store_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['store_email'] = $this->config->get('LANG_ERROR_STORE_MAIL');
        }

        
        if (count($errors)) {
            $this->errors = $errors;
            return false;
        }
        return true;
    }

    public function ajax()
    {
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            return;
        }
        $this->validate($this->request->post);

        $action = isset($this->request->post['action']) ? $this->request->post['action'] : '';

        if (count($this->errors)) {
            $this->response->setOutput(json_encode($this->errors));
            return;
        }

        $this->response->setOutput('OK');
    }
}
