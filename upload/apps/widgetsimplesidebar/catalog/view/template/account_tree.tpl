<?php namespace Sumo?>
<div class="sidebar sidebar-category-tree">
    <div class="block">
        <div class="header">
            <h3><?php echo Language::getVar('SUMO_ACCOUNT_TITLE')?></h3>
        </div>
        <div class="content">
            <ul class="menu-accordion">
                <?php if ($this->customer->isLogged()): ?>
                <li><a <?php if ($this->request->get['_route_'] == 'account/edit') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/edit')?>"><?php echo Language::getVar('SUMO_ACCOUNT_EDIT_TITLE'); ?></a></li>
                <li><a <?php if ($this->request->get['_route_'] == 'account/password') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/password', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_PASSWORD'); ?></a></li>
                <li><a <?php if (stristr($this->request->get['_route_'], 'account/address')) { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/address', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_ADDRESS'); ?></a></li>
                <li><a <?php if ($this->request->get['_route_'] == 'account/order') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/order', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_ORDER'); ?></a></li>
                <?php /*
                <li><a <?php if ($this->request->get['_route_'] == 'account/download') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/download', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_DOWNLOAD'); ?></a></li>
                */ ?>
                <li><a <?php if ($this->request->get['_route_'] == 'account/reward') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/reward', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_REWARD'); ?></a></li>
                <?php else: ?>
                <li><a <?php if ($this->request->get['_route_'] == 'account/login') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/login', '', 'SSL')?>"><?php echo Language::getVar('SUMO_ACCOUNT_LOGIN_TITLE'); ?></a></li>
                <li><a <?php if ($this->request->get['_route_'] == 'account/register') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/register', '', 'SSL')?>"><?php echo Language::getVar('SUMO_ACCOUNT_REGISTER_TITLE'); ?></a></li>
                <li><a <?php if ($this->request->get['_route_'] == 'account/forgotten') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/forgotten', '', 'SSL')?>"><?php echo Language::getVar('SUMO_ACCOUNT_FORGOTTEN_TITLE'); ?></a></li>
                <?php endif?>
                <li><a <?php if ($this->request->get['_route_'] == 'account/return') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/return', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_RETURN'); ?></a></li>
                <li><a <?php if ($this->request->get['_route_'] == 'account/transaction') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/transaction', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_TRANSACTION'); ?></a></li>
                <li><a <?php if ($this->request->get['_route_'] == 'account/wishlist') { echo 'class="active"'; } ?> href="<?php echo $this->url->link('account/wishlist', '', 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_WISHLIST'); ?></a></li>
                <?php if ($this->customer->isLogged()): ?>
                <li><a <?php if ($this->request->get['_route_'] == 'account/logout') { echo 'class="active"'; } ?> href="<?php $token = md5(sha1($_SERVER['REMOTE_ADDR']) . microtime(true) . md5($this->customer->getId())); $this->session->data['logout'] = $token; echo $this->url->link('account/logout', 'logout=' . $token, 'SSL')?>"><?php echo Language::getVar('SUMO_NOUN_ACCOUNT_LOGOUT')?></a></li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</div>
