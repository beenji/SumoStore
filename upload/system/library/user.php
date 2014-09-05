<?php
class User
{
    private $user_id;
    private $group_id;
    private $username;
    private $permission = array();

    public function __construct($registry)
    {
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        if (isset($this->session->data['user_id'])) {
            $data = Sumo\Database::query("SELECT user_id, user_group_id, username FROM PREFIX_user WHERE user_id = :id AND status = 1", array('id' => $this->session->data['user_id']))->fetch();
            if (is_array($data)) {
                $this->user_id      = $data['user_id'];
                $this->group_id     = $data['user_group_id'];
                $this->username     = $data['username'];

                Sumo\Database::query("UPDATE PREFIX_user SET ip = :ip, date_last_seen = :date WHERE user_id = :id", array('ip' => $this->request->server['REMOTE_ADDR'], 'id' => $this->user_id, 'date' => date('Y-m-d H:i:s')));
            }
            else {
                $this->logout();
            }
        }
    }

    public function login($username, $password, $cookie = false)
    {
        $data = Sumo\Database::query(
            "SELECT *
            FROM PREFIX_user
            WHERE username = :username
            AND (
                password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, :unsalted))))
                OR password = :hashed
            ) AND status = 1",
            array(
                'username'  => $username,
                'unsalted'  => sha1($password),
                'hashed'    => md5($password)
            )
        )->fetch();

        if (count($data)) {
            if ($cookie) {
                // some secure way to implement a cookie
            }
            $this->session->data['user_id']     = $data['user_id'];
            $this->session->data['firstname']   = $data['firstname'];
            $this->session->data['lastname']    = $data['lastname'];
            $this->session->data['email']       = $data['email'];

            $this->user_id                      = $data['user_id'];
            $this->username                     = $data['username'];

            return true;
        }
        return false;
    }

    public function logout()
    {
        unset($this->session->data['user_id']);
        unset($this->session->data['firstname']);
        unset($this->session->data['lastname']);
        unset($this->session->data['email']);

        $this->user_id = '';
        $this->username = '';

        session_destroy();
    }

    public function hasPermission($key, $value)
    {
        #if (Sumo\Communicator::checkPermissions('authentication("' . $value . '", \'' . json_encode($this->permission[$key]) . '\')')) {
            return true;
        #}
        #if (isset($this->permission[$key])) {
        #    return in_array($value, $this->permission[$key]);
        #} else {
        #    return false;
        #}
        return false;
    }

    public function isLogged()
    {
        return $this->user_id;
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getUserGroup()
    {
        return $this->group_id;
    }
}
