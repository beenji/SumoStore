<?php
namespace Sumo;
class ControllerStep2 extends Controller
{
    private $error = array();
    private $check = array(
        'files' => array(
            'config.mysql.php',
            'license.php'
        ),
        'directories' => array(
            'download',
            'image',
            'image/cache',
            'image/data',
            'system/cache',
            'system/logs',
        )
    );

    public function index()
    {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->redirect(HTTP_SERVER . '?route=step_3');
        }

        $this->data['action'] = HTTP_SERVER . '?route=step_2';

        $this->data['files'] = $this->check['files'];
        $this->data['directories'] = $this->check['directories'];
        foreach ($this->check['directories'] as $dir) {
            if ($this->checkWriteable(DIR_SUMOSTORE . $dir)) {
                $this->data['check'][DIR_SUMOSTORE . $dir] = true;
            }
            else {
                $this->data['check'][DIR_SUMOSTORE . $dir] = false;
            }
        }

        $this->data['warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['warning'] = implode('<br />', $this->error['warning']);
        }

        $this->data['back'] = HTTP_SERVER . '?route=step_1';

        $this->template = 'step_2.tpl';
        $this->children = array(
            'header',
            'footer'
        );

        $this->response->setOutput($this->render());
    }

    private function checkWriteable($dir)
    {
        $fp = @fopen($dir . '/tmp.txt', 'w');
        if (!$fp) {
            $this->error['warning'][] = 'Could not create file in ' . $dir;
            return false;
        }
        $write = @fwrite($fp, 'test');
        if (!$write) {
            $this->error['warning'][] = 'Could not write file in ' . $dir;
            return false;
        }
        $unlink = @unlink($dir . '/tmp.txt');
        if (!$unlink) {
            $this->error['warning'][] = 'Could not unlink file in ' . $dir;
            return false;
        }
        $mkdir = @mkdir($dir . '/tmp');
        if (!$mkdir) {
            $this->error['warning'][] = 'Could not create directory in ' . $dir;
            return false;
        }
        $rmdir = @rmdir($dir . '/tmp');
        if (!$rmdir) {
            $this->error['warning'][] = 'Could not remove directory in ' . $dir;
            return false;
        }

        return true;
    }

    private function validate()
    {
        if (phpversion() < '5.3') {
            $this->error['warning'][] = 'Waarschuwing: PHP 5.3 of hoger is vereist.';
        }

        if (!ini_get('file_uploads')) {
            $this->error['warning'][] = 'Waarschuwing: file_uploads moeten aanstaan.';
        }

        if (ini_get('session.auto_start')) {
            $this->error['warning'][] = 'Waarschuwing: SumoStore kan niet werken als session.auto_start aan staat!';
        }

        if (!extension_loaded('mysql')) {
            $this->error['warning'][] = 'Waarschuwing: MySQL extensie is niet gevonden.';
        }

        if (!extension_loaded('gd')) {
            $this->error['warning'][] = 'Waarschuwing: GD extensie is niet gevonden.';
        }

        if (!extension_loaded('curl')) {
            $this->error['warning'][] = 'Waarschuwing: Curl extensie is niet gevonden.';
        }

        if (!function_exists('mcrypt_encrypt')) {
            $this->error['warning'][] = 'Waarschuwing: Mcrypt extensie is niet gevonden.';
        }

        if (!extension_loaded('zlib')) {
            $this->error['warning'][] = 'Waarschuwing: Zlib extensie is niet gevonden.';
        }

        if (!class_exists('SoapClient')) {
            $this->error['warning'][] = 'Waarschuwing: SOAP extensie is niet gevonden.';
        }

        if (!file_exists(DIR_SUMOSTORE . 'config.mysql.php')) {
            $this->error['warning'][] = 'Waarschuwing: het bestand config.mysql.php is niet gevonden.';
        } elseif (!is_writable(DIR_SUMOSTORE . 'config.mysql.php')) {
            $this->error['warning'][] = 'Waarschuwing: het bestand config.mysql.php heeft geen schrijfrechten!';
        }

        if (!file_exists(DIR_SUMOSTORE . 'license.php')) {
            $this->error['warning'][] = 'Waarschuwing: het bestand license.php is niet gevonden. Dit bestand moet bestaan.';
        } elseif (!is_writable(DIR_SUMOSTORE . 'license.php')) {
            $this->error['warning'][] = 'Waarschuwing: het bestand license.php heeft geen schrijfrechten!';
        }

        if (!is_writable(DIR_SYSTEM . 'cache')) {
            $this->error['warning'][] = 'Waarschuwing: de map system/cache/ heeft geen schrijfrechten!';
        }

        if (!is_writable(DIR_SYSTEM . 'logs')) {
            $this->error['warning'][] = 'Waarschuwing: de map system/logs/ heeft geen schrijfrechten!';
        }

        if (!is_writable(DIR_SUMOSTORE . 'image')) {
            $this->error['warning'][] = 'Waarschuwing: de map image/ heeft geen schrijfrechten!';
        }

        if (!is_writable(DIR_SUMOSTORE . 'image/cache')) {
            $this->error['warning'][] = 'Waarschuwing: de map image/cache/ heeft geen schrijfrechten!';
        }

        if (!is_writable(DIR_SUMOSTORE . 'image/data')) {
            $this->error['warning'][] = 'Waarschuwing: de map image/data/ heeft geen schrijfrechten!';
        }

        if (!is_writable(DIR_SUMOSTORE . 'download')) {
            $this->error['warning'][] = 'Waarschuwing: de map download/ heeft geen schrijfrechten!';
        }

        if (!$this->error) {
              return true;
        }
        return false;
    }
}
