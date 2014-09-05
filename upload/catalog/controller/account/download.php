<?php
namespace Sumo;
class ControllerAccountDownload extends Controller
{
    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/download', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_DOWNLOAD_TITLE'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_ACCOUNT_TITLE'),
            'href'      => $this->url->link('account/account', '', 'SSL'),

        );

        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_DOWNLOAD_TITLE'),
            'href'      => $this->url->link('account/download', '', 'SSL'),

        );

        $this->load->model('account/download');

        $totalDownloads = $this->model_account_download->getTotalDownloads();

        if ($totalDownloads) {

            if (isset($this->request->get['page'])) {
                $page = $this->request->get['page'];
            } 
            else {
                $page = 1;
            }

            $this->data['downloads'] = array();

            $results = $this->model_account_download->getDownloads(($page - 1) * $this->config->get('catalog_limit'), $this->config->get('catalog_limit'));

            foreach ($results as $result) {
                if (file_exists(DIR_DOWNLOAD . $result['filename'])) {
                    $size = filesize(DIR_DOWNLOAD . $result['filename']);

                    $i = 0;

                    $suffix = array(
                        'B',
                        'KB',
                        'MB',
                        'GB',
                        'TB',
                        'PB',
                        'EB',
                        'ZB',
                        'YB'
                    );

                    while (($size / 1024) > 1) {
                        $size = $size / 1024;
                        $i++;
                    }

                    $this->data['downloads'][] = array(
                        'order_id'   => str_pad($result['order_id'], 6, 0, STR_PAD_LEFT),
                        'date'       => Formatter::date($result['order_date']),
                        'name'       => $result['name'],
                        'remaining'  => $result['remaining'],
                        'size'       => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
                        'download'   => $this->url->link('account/download/download', 'order_download_id=' . $result['order_download_id'], 'SSL')
                    );
                }
            }

            $pagination = new Pagination();
            $pagination->total = $totalDownloads;
            $pagination->page = $page;
            $pagination->limit = $this->config->get('config_catalog_limit');

            $pagination->url = $this->url->link('account/download', 'page={page}', 'SSL');

            $this->data['pagination'] = $pagination->render();
            $this->data['continue']   = $this->url->link('account/account', '', 'SSL');
        } 
        else {
            $this->data['downloads'] = $this->data['pagination'] = false;
        }

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/download.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }

    public function download()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/download', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->load->model('account/download');

        if (isset($this->request->get['order_download_id'])) {
            $orderDownloadID = $this->request->get['order_download_id'];
        } else {
            $orderDownloadID = 0;
        }

        $downloadInfo = $this->model_account_download->getDownload($orderDownloadID);

        if ($downloadInfo) {
            $file = DIR_DOWNLOAD . $downloadInfo['filename'];
            $mask = basename($downloadInfo['name']);

            if (!headers_sent()) {
                if (file_exists($file)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));

                    if (ob_get_level()) ob_end_clean();

                    readfile($file, 'rb');

                    $this->model_account_download->updateRemaining($orderDownloadID);

                    exit;
                } else {
                    exit('Error: Could not find file ' . $file . '!');
                }
            } else {
                exit('Error: Headers already sent out!');
            }
        } else {
            $this->redirect($this->url->link('account/download', '', 'SSL'));
        }
    }
}
