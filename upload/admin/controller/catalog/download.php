<?php
namespace Sumo;
class ControllerCatalogDownload extends Controller
{
    private $error = array();

    public function index()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_DOWNLOADS'));

        $this->load->model('catalog/download');

        $this->getList();
    }

    public function insert()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_DOWNLOADS'));

        $this->load->model('catalog/download');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_download->addDownload($this->request->post);

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/download', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
      }

    public function update()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_DOWNLOADS'));

        $this->load->model('catalog/download');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_download->editDownload($this->request->get['download_id'], $this->request->post);

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/download', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    public function delete()
    {
        $this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_DOWNLOADS'));

        $this->load->model('catalog/download');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $download_id) {
                $this->model_catalog_download->deleteDownload($download_id);
            }

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->redirect($this->url->link('catalog/download', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList()
    {
        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DOWNLOADS'),
        ));

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'dd.name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['insert'] = $this->url->link('catalog/download/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $this->data['delete'] = $this->url->link('catalog/download/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->data['downloads'] = array();

        $data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * 25,
            'limit' => 25
        );

        $download_total = $this->model_catalog_download->getTotalDownloads();

        $results = $this->model_catalog_download->getDownloads($data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'href' => $this->url->link('catalog/download/update', 'token=' . $this->session->data['token'] . '&download_id=' . $result['download_id'] . $url, 'SSL')
            );

            $this->data['downloads'][] = array(
                'download_id' => $result['download_id'],
                'name'        => $result['name'],
                'remaining'   => $result['remaining'],
                'selected'    => isset($this->request->post['selected']) && in_array($result['download_id'], $this->request->post['selected']),
                'action'      => $action
            );
        }

        $this->data['error_warning'] = '';
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        }

        $this->data['success'] = '';
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $url = '';
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        }
        else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = $this->url->link('catalog/download', 'token=' . $this->session->data['token'] . '&sort=dd.name' . $url, 'SSL');
        $this->data['sort_remaining'] = $this->url->link('catalog/download', 'token=' . $this->session->data['token'] . '&sort=d.remaining' . $url, 'SSL');

        $url = '';
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $download_total;
        $pagination->page  = $page;
        $pagination->limit = 25;
        $pagination->text  = '';
        $pagination->url   = $this->url->link('catalog/download', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $this->data['pagination'] = $pagination->renderAdmin();
        $this->data['token'] = $this->session->data['token'];

        $this->getForm();

        $this->document->addScript('view/js/pages/download_list.js');

        $this->template = 'catalog/download_list.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function getForm()
    {
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = array();
        }

        if (isset($this->error['filename'])) {
            $this->data['error_filename'] = $this->error['filename'];
        } else {
            $this->data['error_filename'] = '';
        }

        if (isset($this->error['mask'])) {
            $this->data['error_mask'] = $this->error['mask'];
        } else {
            $this->data['error_mask'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (!isset($this->request->get['download_id'])) {
            $this->data['action'] = $this->url->link('catalog/download/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $this->data['action'] = $this->url->link('catalog/download/update', 'token=' . $this->session->data['token'] . '&download_id=' . $this->request->get['download_id'] . $url, 'SSL');
        }

        $this->data['cancel'] = $this->url->link('catalog/download', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $this->load->model('localisation/language');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->get['download_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $download_info = $this->model_catalog_download->getDownload($this->request->get['download_id']);
        }

          $this->data['token'] = $this->session->data['token'];

          if (isset($this->request->get['download_id'])) {
            $this->data['download_id'] = $this->request->get['download_id'];
        } else {
            $this->data['download_id'] = 0;
        }

        if (isset($this->request->post['download_description'])) {
            $this->data['download_description'] = $this->request->post['download_description'];
        } elseif (isset($this->request->get['download_id'])) {
            $this->data['download_description'] = $this->model_catalog_download->getDownloadDescriptions($this->request->get['download_id']);
        } else {
            $this->data['download_description'] = array();
        }

        if (isset($this->request->post['filename'])) {
            $this->data['filename'] = $this->request->post['filename'];
        } elseif (!empty($download_info)) {
              $this->data['filename'] = $download_info['filename'];
        } else {
            $this->data['filename'] = '';
        }

        if (isset($this->request->post['mask'])) {
            $this->data['mask'] = $this->request->post['mask'];
        } elseif (!empty($download_info)) {
              $this->data['mask'] = $download_info['mask'];
        } else {
            $this->data['mask'] = '';
        }

        if (isset($this->request->post['remaining'])) {
              $this->data['remaining'] = $this->request->post['remaining'];
        } elseif (!empty($download_info)) {
              $this->data['remaining'] = $download_info['remaining'];
        } else {
              $this->data['remaining'] = 1;
        }

        if (isset($this->request->post['update'])) {
              $this->data['update'] = $this->request->post['update'];
        } else {
              $this->data['update'] = false;
        }

        $this->document->addScript('view/js/jquery/jquery.ajaxupload.js');

        /*$this->template = 'catalog/download_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());*/
    }

    protected function validateForm()
    {
        foreach ($this->request->post['download_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 64)) {
                $this->error['name'][$language_id] = Language::getVar('SUMO_ERROR_NAME');
            }
        }

        if ((utf8_strlen($this->request->post['filename']) < 3) || (utf8_strlen($this->request->post['filename']) > 128)) {
            $this->error['filename'] = Language::getVar('SUMO_ERROR_FILENAME');
        }

        if (!file_exists(DIR_DOWNLOAD . $this->request->post['filename']) && !is_file(DIR_DOWNLOAD . $this->request->post['filename'])) {
            $this->error['filename'] = Language::getVar('SUMO_ERROR_EXISTS');
        }

        if ((utf8_strlen($this->request->post['mask']) < 3) || (utf8_strlen($this->request->post['mask']) > 128)) {
            $this->error['mask'] = Language::getVar('SUMO_ERROR_MASK');
        }

        if (!$this->error) {
            return true;
        }
        return false;

    }

    protected function validateDelete()
    {
        $this->load->model('catalog/product');

        foreach ($this->request->post['selected'] as $download_id) {
            $product_total = $this->model_catalog_product->getTotalProductsByDownloadId($download_id);

            if ($product_total) {
                $this->error['warning'] = sprintf(Language::getVar('SUMO_ERROR_DOWNLOAD_HAS_PRODUCTS'), $product_total);
            }
        }

        if (!$this->error) {
            return true;
        }
        return false;
    }

    public function upload()
    {
        $json = array();

        if (!isset($json['error'])) {
            if (!empty($this->request->files['file']['name'])) {
                $filename = strtolower(basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

                if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
                    $json['error'] = Language::getVar('SUMO_ERROR_FILENAME');
                }

                // Allowed file extension types
                $allowed = array('jpg', 'png', 'gif', 'jpeg');
                $filetypes = explode("\n", $this->config->get('file_extension_allowed'));
                foreach ($filetypes as $type) {
                    $allowed[] = trim($type);
                }

                if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
                    //$json['error'] = Language::getVar('SUMO_ERROR_FILETYPE');
                }

                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = Language::getVar('SUMO_ERROR_UPLOAD_' . mb_strtoupper($this->request->files['file']['error']));
                }

                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = Language::getVar('SUMO_ERROR_UPLOAD_' . mb_strtoupper($this->request->files['file']['error']));
                }
            } else {
                $json['error'] = Language::getVar('SUMO_ERROR_UPLOAD');
            }
        }

        if (!isset($json['error'])) {
            if (is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
                $ext = md5(mt_rand());

                $json['filename'] = $filename . '.' . $ext;
                $json['mask'] = $filename;

                move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $filename . '.' . $ext);
            }

            $json['success'] = Language::getVar('SUMO_NOUN_UPLOAD');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete()
    {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('catalog/download');

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 20
            );

            $results = $this->model_catalog_download->getDownloads($data);

            foreach ($results as $result) {
                $json[] = array(
                    'download_id' => $result['download_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->setOutput(json_encode($json));
    }

    public function download()
    {
        $this->load->model('catalog/download');
        $id = $this->request->get['download_id'];
        if (empty($id)) {
            exit('EMPTY');
        }

        $info = $this->model_catalog_download->getDownload($id);
        $mask = $info['mask'];
        $file = DIR_DOWNLOAD . $info['filename'];
        if (file_exists($file)) {
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file));
            header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            readfile($file);
            exit;
        }
        exit('NOT_FOUND');
    }
}
