<?php
namespace Sumo;
class ControllerToolCache extends Controller
{
    private $error = array();

    public function index()
    {
        $title = Language::getVar('SUMO_ADMIN_TOOL_CACHE_TITLE');
        $this->document->setTitle($title);
        $this->document->addBreadcrumbs(array('text' => Language::getVar('SUMO_ADMIN_SETTINGS_DASHBOARD'), 'href' => $this->url->link('settings/dashboard', '', 'SSL')));
        $this->document->addBreadcrumbs(array('text' => $title));

        $data = array();
        $data['count'] = $data['size'] = $data['results'] = 0;
        $files = glob(DIR_CACHE . 'cache.*');
        if (is_array($files)) {
            foreach ($files as $file) {
                $data['count']++;
                $data['size'] += filesize($file);

                $contents = file_get_contents($file);
                $test = json_decode(base64_decode($contents), true);
                if (is_array($test) && count($test)) {
                    $data['results'] += count($test);
                }
                unset($contents);
                unset($test);
            }
        }
        unset($files);


        $files = $this->scan(DIR_IMAGE . 'cache/*', array());
        $check = array();
        if (is_array($files)) {
            foreach ($files as $file) {
                if (!isset($check[$file])) {
                    $data['count']++;
                    $data['size'] += filesize($file);
                    $check[$file] = true;
                }
            }
        }
        unset($check);
        unset($files);

        $this->data['file_count']   = number_format($data['count'], 0, '.', '.');
        $this->data['file_size']    = Formatter::bytes($data['size']);
        $this->data['objects']      = number_format($data['results'], 0, '.', '.');

        $this->template = 'tool/cache.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render());
    }

    public function remove()
    {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            Cache::removeAll(true);
            Language::rebuildCacheFor($this->config->get('language_id'));
            $files = $this->scan(DIR_IMAGE . 'cache/*', array());
            $check = array();
            if (is_array($files)) {
                foreach ($files as $file) {
                    @unlink($file);
                }
            }
            $this->response->setOutput(json_encode(Logger::get('warning')));
        }
    }

    private function scan($dir, $array)
    {
        foreach (glob(rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*') as $sub) {
            if (is_array($sub)) {
                return $this->scan($sub, $array);
            }
            else {
                $array[] = $sub;
            }
        }
        return $array;
    }
}
