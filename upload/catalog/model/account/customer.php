<?php
namespace Sumo;
class ModelAccountCustomer extends Model
{
    public function addCustomer($data)
    {
        $this->load->model('account/customer_group');

        if (empty($data['customer_group_id'])) {
            $data['customer_group_id'] = $this->config->get('customer_group_id');
        }

        $group_info = $this->model_account_customer_group->getCustomerGroup($data['customer_group_id']);
        $salt = substr(md5(uniqid(rand(), true) . $this->request->server['REMOTE_ADDR']), 0, 9);
        $data['birthdate'] = date('Y-m-d', strtotime($data['birthdate']));

        $this->query(
            "INSERT INTO PREFIX_customer
            SET store_id            = :store_id,
                firstname           = :firstname,
                middlename          = :middlename,
                lastname            = :lastname,
                birthdate           = :birthdate,
                email               = :email,
                telephone           = :telephone,
                mobile              = :mobile,
                gender              = :gender,
                fax                 = :fax,
                salt                = :salt,
                password            = :password,
                newsletter          = :newsletter,
                customer_group_id   = :group_id,
                language_id         = :language_id,
                ip                  = :ip,
                status              = 1,
                approved            = :approval,
                date_added          = :date",
            array(
                'store_id'          => $this->config->get('store_id'),
                'firstname'         => $data['firstname'],
                'middlename'        => $data['middlename'],
                'lastname'          => $data['lastname'],
                'birthdate'         => $data['birthdate'],
                'email'             => $data['email'],
                'telephone'         => $data['telephone'],
                'mobile'            => $data['mobile'],
                'gender'            => $data['gender'],
                'fax'               => $data['fax'],
                'salt'              => $salt,
                'password'          => sha1($salt . sha1($salt . sha1($data['password']))),
                'newsletter'        => $data['newsletter'],
                'ip'                => $this->request->server['REMOTE_ADDR'],
                'group_id'          => $data['customer_group_id'],
                'language_id'       => $this->config->get('language_id'),
                'approval'          => !$group_info['approval'],
                'date'              => date('Y-m-d H:i:s')
            )
        );
        $customer_id = $this->lastInsertId();

        if (!$customer_id) {
            Logger::error('[ACCOUNT/CUSTOMER] The customer could not be created');
            Logger::error(print_r(self::$connection->errorInfo(), true));
            return false;
        }

        $this->query(
            "INSERT INTO PREFIX_address
            SET customer_id         = :id,
                firstname           = :firstname,
                middlename          = :middlename,
                lastname            = :lastname,
                company             = :company,
                company_id          = :cid,
                tax_id              = :tax,
                address_1           = :address_1,
                number              = :number,
                addon               = :addon,
                address_2           = :address_2,
                postcode            = :postcode,
                city                = :city,
                country_id          = :country_id,
                zone_id             = :zone_id",
            array(
                'id'                => $customer_id,
                'firstname'         => $data['firstname'],
                'middlename'        => $data['middlename'],
                'lastname'          => $data['lastname'],
                'company'           => $data['company'],
                'cid'               => $data['company_id'],
                'tax'               => $data['tax_id'],
                'address_1'         => $data['address_1'],
                'number'            => $data['number'],
                'addon'             => $data['addon'],
                'address_2'         => $data['address_2'],
                'postcode'          => $data['postcode'],
                'city'              => $data['city'],
                'country_id'        => $data['country_id'],
                'zone_id'           => $data['zone_id']
            )
        );
        $address_id = $this->lastInsertId();

        $this->query("UPDATE PREFIX_customer SET address_id = :aid WHERE customer_id = :cid", array('aid' => $address_id, 'cid' => $customer_id));

        if ($group_info['approval']) {
            $message = Language::getVar('SUMO_ACCOUNT_WAIT_FOR_APPROVAL');
        }
        else {
            $message = Language::getVar('SUMO_ACCOUNT_GO_TO_LOGIN', $this->url->link('account/login', '', 'SSL'));
        }
        Mailer::setCustomer($data);
        $template = Mailer::getTemplate('account_register');
        $template['content'] = str_replace('{approvalOrLogin}', $message, $template['content']);

        Mail::setTo($data['email']);
        Mail::setSubject($template['title']);
        Mail::setHtml($template['content']);
        Mail::send();

        if ($this->config->get('admin_notify_email')) {
            $sendTo = array($this->config->get('email'));
            $extra = $this->config->get('extra_notify_email');
            if (!empty($extra)) {
                $extra = explode(',', $extra);
                foreach ($extra as $mail) {
                    if (!empty($mail) && filter_var($mail, \FILTER_VALIDATE_EMAIL)) {
                        $sendTo[] = $mail;
                    }
                }
            }
            $template = Mailer::getTemplate('account_register_admin_notify');
            if ($group_info['approval']) {
                $template['content'] = str_replace('{action}', Language::getVar('SUMO_ADMIN_ACTIVATE_ACCOUNT', $this->url->link('account/login')), $template['content']);
            }
            else {
                $template['content'] = str_replace('{action}', '', $template['content']);
            }
            foreach ($sendTo as $to) {
                Mail::setTo($to);
                Mail::setSubject($template['title']);
                Mail::setHtml($template['content']);
                Mail::send();
            }
        }
        Cache::removeAll();

        return $customer_id;
    }

    public function editCustomer($data)
    {
        $data['birthdate'] = date('Y-m-d', strtotime($data['birthdate']));
        $this->query(
            "UPDATE PREFIX_customer
            SET firstname           = :firstname,
                middlename          = :middlename,
                lastname            = :lastname,
                birthdate           = :birthdate,
                email               = :email,
                telephone           = :telephone,
                mobile              = :mobile,
                fax                 = :fax
            WHERE customer_id       = :id",
            array(
                'firstname'         => $data['firstname'],
                'middlename'        => $data['middlename'],
                'lastname'          => $data['lastname'],
                'birthdate'         => $data['birthdate'],
                'email'             => $data['email'],
                'telephone'         => $data['telephone'],
                'mobile'            => $data['mobile'],
                'fax'               => $data['fax'],
                'id'                => $this->customer->getId()
            )
        );
        Cache::removeAll();
    }

    public function editPassword($email, $password)
    {
        $salt = substr(md5(uniqid(rand(), true) . $this->request->server['REMOTE_ADDR']), 0, 9);
        $this->query(
            "UPDATE PREFIX_customer
            SET salt            = :salt,
                password        = :pass
            WHERE LOWER(email)  = :email",
            array(
                'salt'          => $salt,
                'pass'          => sha1($salt . sha1($salt . sha1($password))),
                'email'         => strtolower($email)
            )
        );
    }

    public function editNewsletter($newsletter)
    {
        $this->query("UPDATE PREFIX_customer SET newsletter = :news WHERE customer_id = :cid", array('news' => $newsletter, 'cid' => $this->customer->getId()));
        Cache::removeAll();
    }

    public function getCustomer($customer_id)
    {
        return $this->query("SELECT * FROM PREFIX_customer WHERE customer_id = :id", array('id' => $customer_id))->fetch();
    }

    public function getCustomerByEmail($email)
    {
        return $this->query("SELECT * FROM PREFIX_customer WHERE LOWER(email) = :email", array('email' => strtolower($email)))->fetch();
    }

    public function getCustomerByToken($token)
    {

        $return = $this->query("SELECT * FROM PREFIX_customer WHERE token = :token AND token != ''", array('token' => $token))->fetch();
        $this->query("UPDATE PREFIX_customer SET token = ''");
        return $return;
    }

    public function getTotalCustomersByEmail($email)
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_customer WHERE LOWER(email) = :email", array('email' => strtolower($email)))->fetch();
        return $data['total'];
    }

    public function getIps($customer_id)
    {
        return $this->fetchAll("SELECT * FROM PREFIX_customer_ip WHERE customer_id = :id", array('id' => $customer_id))->fetch();
    }

    public function isBanIp($ip)
    {
        return $this->query("SELECT * FROM PREFIX_customer_ban_ip WHERE ip = :ip", array('ip' => $ip))->fetch();
    }
}
