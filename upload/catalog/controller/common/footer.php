<?php
namespace Sumo;
class ControllerCommonFooter extends Controller
{
    protected function index()
    {
        $this->data['google_analytics'] = html_entity_decode($this->config->get('google_analytics'), ENT_QUOTES, 'UTF-8');

        // Whos Online
            $this->load->model('tool/online');

            if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->whosonline($ip, $this->customer->getId(), $url, $referer);


        // Cookie
        $this->data['cookie'] = array(
            'location'  => $this->config->get('cookie_location'),
            'page'      => $this->config->get('cookie_page'),
            'text'      => $this->config->get('cookie_text')
        );
        $this->data['cookie']['text'] = $this->data['cookie']['text'][$this->config->get('language_id')];
        $this->data['cookie']['page'] = $this->url->link('information/information', 'information_id=' . $this->data['cookie']['page'][$this->config->get('language_id')]);

        $this->template = 'common/footer.tpl';

        $this->render();
    }
}
