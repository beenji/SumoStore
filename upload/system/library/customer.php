<?php
class Customer
{
    private $customer_id;
    private $firstname;
    private $lastname;
    private $email;
    private $telephone;
    private $fax;
    private $newsletter;
    private $customer_group_id;
    private $address_id;

    private $data;

    public function __construct($registry)
    {
        $this->config = $registry->get('config');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');

        if (isset($this->session->data['customer_id'])) {
            if (empty($this->session->data['logout'])) {
                $this->session->data['logout'] = md5(microtime(true) . $this->request->server['REMOTE_ADDR'] . $this->config->get('store_id'));
            }
            $customer = Sumo\Database::query("SELECT * FROM PREFIX_customer WHERE customer_id = :id AND status = 1", array('id' => $this->session->data['customer_id']))->fetch();
            if (!empty($customer['customer_id'])) {
                if (count($customer)) {
                    $this->customer_id          = $customer['customer_id'];
                    $this->firstname            = $customer['firstname'];
                    $this->lastname             = $customer['lastname'];
                    $this->email                = $customer['email'];
                    $this->telephone            = $customer['telephone'];
                    $this->fax                  = $customer['fax'];
                    $this->newsletter           = $customer['newsletter'];
                    $this->customer_group_id    = $customer['customer_group_id'];
                    $this->address_id           = $customer['address_id'];
                    $this->data                 = $customer;

                    Sumo\Database::query(
                        "UPDATE PREFIX_customer
                        SET cart            = :cart,
                            wishlist        = :wish,
                            ip              = :ip
                        WHERE customer_id   = :id",
                        array(
                            'cart'  => isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '',
                            'wish'  => isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '',
                            'ip'    => $this->request->server['REMOTE_ADDR'],
                            'id'    => $customer['customer_id']
                        )
                    );

                    $query = Sumo\Database::query(
                        "SELECT *
                        FROM PREFIX_customer_ip
                        WHERE customer_id   = :id
                            AND ip          = :ip",
                        array(
                            'id'    => $customer['customer_id'],
                            'ip'    => $this->request->server['REMOTE_ADDR']
                        )
                    )->fetch();

                    if (!is_array($query) || !count($query)) {
                        Sumo\Database::query(
                            "INSERT INTO PREFIX_customer_ip
                            SET customer_id = :id,
                                ip          = :ip,
                                date_added  = NOW()",
                            array(
                                'ip'    => $this->request->server['REMOTE_ADDR'],
                                'id'    => $customer['customer_id']
                            )
                        );
                    }
                }
                else {
                    $this->logout();
                }
            }
            else {
                $this->logout();
            }
        }
    }

    public function login($email, $password, $override = false)
    {
        if ($override) {
            $data = Sumo\Database::query(
                "SELECT *
                FROM PREFIX_customer
                WHERE LOWER(email) = :email",
                array(
                    'email' => $email
                )
            )->fetch();
        }
        else {
            $data = Sumo\Database::query(
                "SELECT *
                FROM PREFIX_customer
                WHERE LOWER(email) = :email
                    AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, :pass1)))) OR password = :pass2)
                    AND status = 1
                    AND approved = 1",
                array(
                    'email' => $email,
                    'pass1' => sha1($password),
                    'pass2' => md5($password)
                )
            )->fetch();
        }

        if (is_array($data) && count($data)) {
            $this->session->data['customer_id'] = $data['customer_id'];

            if ($data['cart'] && is_string($data['cart'])) {
                $cart = unserialize($data['cart']);

                foreach ($cart as $key => $value) {
                    if (!array_key_exists($key, $this->session->data['cart'])) {
                        $this->session->data['cart'][$key] = $value;
                    }
                    else {
                        $this->session->data['cart'][$key] += $value;
                    }
                }
            }

            if ($data['wishlist'] && is_string($data['wishlist'])) {
                if (!isset($this->session->data['wishlist'])) {
                    $this->session->data['wishlist'] = array();
                }

                $wishlist = unserialize($data['wishlist']);

                foreach ($wishlist as $product_id) {
                    if (!in_array($product_id, $this->session->data['wishlist'])) {
                        $this->session->data['wishlist'][] = $product_id;
                    }
                }
            }

            $this->customer_id          = $data['customer_id'];
            $this->firstname            = $data['firstname'];
            $this->lastname             = $data['lastname'];
            $this->email                = $data['email'];
            $this->telephone            = $data['telephone'];
            $this->fax                  = $data['fax'];
            $this->newsletter           = $data['newsletter'];
            $this->customer_group_id    = $data['customer_group_id'];
            $this->address_id           = $data['address_id'];
            $this->data                 = $data;

            Sumo\Database::query(
                "UPDATE PREFIX_customer SET ip = :ip WHERE customer_id = :id",
                array(
                    'ip'    => $_SERVER['REMOTE_ADDR'],
                    'id'    => $data['customer_id']
                )
            );

            return true;
        }
        return false;
    }

    public function logout()
    {
        Sumo\Database::query(
            "UPDATE PREFIX_customer
            SET cart            = :cart,
                wishlist        = :wish
            WHERE customer_id   = :id",
            array(
                'cart'  => isset($this->session->data['cart']) ? serialize($this->session->data['cart']) : '',
                'wish'  => isset($this->session->data['wishlist']) ? serialize($this->session->data['wishlist']) : '',
                'id'    => $this->customer_id
            )
        );

        unset($this->session->data['customer_id']);
        unset($this->session->data);
        unset($_SESSION);
        foreach ($_COOKIE as $name => $value) {
            //setcookie($name, '', time() - 72000, '/', $this->config->get('http_base'));
        }
        unset($_COOKIE);
        session_regenerate_id();
        session_destroy();

        $this->customer_id          = '';
        $this->firstname            = '';
        $this->lastname             = '';
        $this->email                = '';
        $this->telephone            = '';
        $this->fax                  = '';
        $this->newsletter           = '';
        $this->customer_group_id    = '';
        $this->address_id           = '';
        $this->data                 = array();
    }

    public function isLogged()
    {
        return $this->customer_id;
    }

    public function loggedIn()
    {
        return $this->customer_id;
    }

    public function getId()
    {
        return $this->customer_id;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function getLastName()
    {
        return $this->lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function getNewsletter()
    {
        return $this->newsletter;
    }

    public function getCustomerGroupId()
    {
        return $this->customer_group_id;
    }

    public function getAddressId()
    {
        return $this->address_id;
    }

    public function getData($key = '')
    {
        if (empty($key)) {
            return $this->data;
        }
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }

    public function getBalance()
    {
        $query = Sumo\Database::query("SELECT SUM(amount) AS total FROM PREFIX_customer_transaction WHERE customer_id = " . (int)$this->customer_id)->fetch();
        return $query['total'];
    }

    public function getRewardPoints()
    {
        $query = Sumo\Database::query("SELECT SUM(points) AS total FROM PREFIX_customer_reward WHERE customer_id = " . (int)$this->customer_id)->fetch();
        return $query['total'];
    }
}
