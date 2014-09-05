<?php
namespace Sumo;
class ControllerCommonImages extends Controller
{
    public function upload()
    {
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            exit;
        }

        $return = array();
        if (!$this->user->hasPermission('modify', 'common/images')) {
            $return['error'] = Language::getVar('SUMO_ADMIN_PERMISSION_DENIED');
            return $this->response->setOutput(json_encode($return));
        }

        $mode = isset($this->request->get['mode']) ? $this->request->get['mode'] : '';

        // Single file uploads are possible, but we kind of expect multiple files at once... so translate them to an array.
        if (isset($this->request->files['uploads']['tmp_name']) && !is_array($this->request->files['uploads']['tmp_name'])) {
            $this->request->files['uploads']['tmp_name']    = array($this->request->files['uploads']['tmp_name']);
            $this->request->files['uploads']['error']       = array($this->request->files['uploads']['error']);
            $this->request->files['uploads']['size']        = array($this->request->files['uploads']['size']);
            $this->request->files['uploads']['type']        = array($this->request->files['uploads']['type']);
            $this->request->files['uploads']['name']        = array($this->request->files['uploads']['name']);
        }

        foreach ($this->request->files['uploads']['tmp_name'] as $key => $value) {
            $short      = $this->request->files['uploads'];
            $tmp_name   = $short['tmp_name'][$key];
            $error      = $short['error'][$key];
            $size       = $short['size'][$key];
            $type       = $short['type'][$key];
            $name       = $short['name'][$key];
            $extension  = substr(strrchr($name, '.'), 1);

            if (!is_uploaded_file($tmp_name) || $error != UPLOAD_ERR_OK || !file_exists($tmp_name) || getimagesize($tmp_name) === false) {
                $return['errors'][$key] = Language::getVar('SUMO_ADMIN_UPLOAD_IMAGE_FAILED');
                continue;
            }

            $allowed = array('jpg', 'png', 'gif', 'jpeg');
            $filetypes = explode("\n", $this->config->get('file_extension_allowed'));
            foreach ($filetypes as $type) {
                $allowed[] = trim($type);
            }

            if (!in_array($extension, $allowed)) {
                $return['errors'][$key] = Language::getVar('SUMO_ADMIN_UPLOAD_IMAGE_INVALID_TYPE');
                continue;
            }

            $new_name = '';

            if (isset($this->request->get['store']) && (!empty($this->request->get['category']) || isset($this->request->get['is_category']))) {
                if (!empty($this->request->get['product']) && !empty($this->request->get['category'])) {
                    $new_location   = DIR_IMAGE . (int)$this->request->get['store'] . '/' . (int)$this->request->get['category'] . '/';
                    $existing       = glob($new_location . 'p' . (int)$this->request->get['product'] . '*');
                    $new_number     = 1;
                    foreach ($existing as $file) {
                        $search     = explode('-', $file);
                        if (isset($search[1])) {
                            $search = explode('.', $search[1]);
                            if ($search[0] + 1 > $new_number) {
                                $new_number = $search[0] + 1;
                            }
                        }
                    }
                    $new_name       = 'p' . (int)$this->request->get['product'] . '-' . $new_number . '.' . $extension;
                }
                else if (isset($this->request->get['is_category'])) {
                    if (empty($this->request->get['category_id'])) {
                        $new_location   = DIR_IMAGE . 'data/categories/';
                    }
                    else {
                        $new_location   = DIR_IMAGE . (int)$this->request->get['store'] . '/' . (int)$this->request->get['category_id'] . '/';
                        $new_name       = 'c' . (int)$this->request->get['category_id'] . '.' . $extension;
                    }
                }
                else {
                    $new_location   = DIR_IMAGE . 'data/products/';
                }
            }
            else {
                $new_location = DIR_IMAGE . 'data/';
                if (isset($this->request->post['store_id'])) {
                    $new_location .= (int)$this->request->post['store_id'] . '/';
                }

                if (isset($this->request->post['theme_id'])) {
                    $new_name = 'themes-' . (int)$this->request->post['theme_id'] . '-';
                }
                if (isset($this->request->post['slider'])) {
                    $new_name .= 'slide-' . (int)$this->request->post['slider'] . '.' . $extension;
                }
            }

            if (!is_dir($new_location)) {
                @mkdir($new_location);
                if (!is_dir($new_location)) {
                    $return['errors'][$key] = Language::getVar('SUMO_ADMIN_UPLOAD_DIRECTORY_CREATE_FAILED', $new_location);
                    continue;
                }
            }

            if (empty($new_name)) {
                $new_name = substr(md5(mt_rand()), 0, rand(5, 15)) . substr(md5($_SERVER['REMOTE_ADDR']), 0, rand(5, 15)) . '.' . $extension;
            }

            if(!@move_uploaded_file($tmp_name, $new_location . $new_name)) {
                $return['errors'][$key] = Language::getVar('SUMO_ADMIN_UPLOAD_COULD_NOT_MOVE', $new_location . $new_name);
                continue;
            }

            $return['success'][$key] = array(
                'extension' => $extension,
                'location' => str_replace(DIR_IMAGE, '', $new_location . $new_name),
                'filename' => $new_name,
                'original_filename' => $name,
                'size' => $size,
                'md5' => md5(file_get_contents($new_location . $new_name)),
                'message' => Language::getVar('SUMO_ADMIN_UPLOAD_SUCCESFULLY_UPLOADED', str_replace(DIR_IMAGE, '', $new_location . $new_name))
            );

        }

        if ($mode == 'redactor') {
            // Redactor only handles one file at a time
            $shop_base = ($this->config->get('base_default') == 'https') ? $this->config->get('base_https') : $this->config->get('base_http');

            exit(json_encode(array('filelink' => $shop_base . 'image/' . $return['success'][0]['location'])));
        }
        else {
            exit(json_encode($return));
        }
    }
}
