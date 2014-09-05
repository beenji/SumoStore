<?php
namespace Sumo;
class ControllerAccountReward extends Controller
{
    public function index()
    {
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/reward', '', 'SSL');
            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->document->setTitle(Language::getVar('SUMO_ACCOUNT_REWARD_TITLE'));

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
            'text'      => Language::getVar('SUMO_ACCOUNT_REWARD_TITLE'),
            'href'      => $this->url->link('account/reward', '', 'SSL'),

        );

        $this->load->model('account/reward');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['rewards'] = array();

        $data = array(
            'sort'  => 'date_added',
            'order' => 'DESC',
            'start' => ($page - 1) * 10,
            'limit' => 10
        );

        $reward_total = $this->model_account_reward->getTotalRewards($data);

        foreach ($this->model_account_reward->getRewards($data) as $result) {
            $this->data['rewards'][] = array(
                'order_id'    => $result['order_id'],
                'points'      => $result['points'],
                'description' => $result['description'],
                'date_added'  => Formatter::date($result['date_added']),
                'order'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
            );
        }

        $pagination = new Pagination();
        $pagination->total = $reward_total;
        $pagination->page  = $page;
        $pagination->limit = 10;
        $pagination->url   = $this->url->link('account/reward', 'page={page}', 'SSL');

        $this->data['pagination'] = $pagination->render();
        $this->data['total']      = (int)$this->customer->getRewardPoints();

        $this->data['settings'] = $this->config->get('details_account_' . $this->config->get('template'));
        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'accountTree', 'data' => array()));
        }
        $this->template = 'account/reward.tpl';
        $this->children = array(
            'common/footer',
            'common/header'
        );
        $this->response->setOutput($this->render());
    }
}
