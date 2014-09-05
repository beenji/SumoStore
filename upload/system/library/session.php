<?php
class Session
{
    public $data = array();

    public function __construct()
    {
        if (!session_id()) {
            ini_set('session.use_cookies', 'On');
            ini_set('session.use_trans_sid', 'Off');

            session_start();
            //session_set_cookie_params(86400, '/', $_SERVER['HTTP_HOST']);
            //setcookie(session_name(), session_id(), time() + 86400);
        }

        $this->data =& $_SESSION;
    }

    public function getId()
    {
        return session_id();
    }
}
