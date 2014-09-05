<?php
namespace Sumo;
class ModelUserUser extends Model
{
    public function addUser($data)
    {
        $salt = substr(md5(uniqid(rand(), true)), 0, 9);

        $this->query("INSERT INTO PREFIX_user SET 
            username = :username, 
            salt = :salt, 
            password = :password, 
            firstname = :firstname,
            lastname = :lastname,
            email = :email, 
            user_group_id = 1, 
            status = :status, 
            date_added = NOW()", array(
            'status'        => (int)$data['status'],
            'username'      => $data['username'],
            'salt'          => $salt,
            'password'      => sha1($salt . sha1($salt . sha1($data['password']))),
            'firstname'     => $data['firstname'],
            'lastname'      => $data['lastname'],
            'email'         => $data['email']));
    }

    public function editUser($userID, $data)
    {
        $this->query("UPDATE PREFIX_user SET 
            username = :username, 
            firstname = :firstname, 
            lastname = :lastname, 
            email = :email, 
            user_group_id = 1, 
            status = :status 
            WHERE user_id = :userID", array(
                'username'     => $data['username'],
                'firstname'    => $data['firstname'],
                'lastname'     => $data['lastname'],
                'email'        => $data['email'],
                'status'       => $data['status'],
                'userID'       => $userID));

        if ($data['password']) {
            $salt = substr(md5(uniqid(rand(), true)), 0, 9);

            $this->query("UPDATE PREFIX_user SET 
                salt = :salt,
                password = :password
                WHERE user_id = :userID",
                array(
                    'salt'     => $salt,
                    'password' => sha1($salt . sha1($salt . sha1($data['password']))),
                    'userID'   => $userID
                ));
        }
    }

    public function editPassword($userID, $password)
    {
        $salt = substr(md5(uniqid(rand(), true)), 0, 9);

        $this->query("UPDATE PREFIX_user SET 
            salt = :salt, 
            password = :password, 
            code = '' 
            WHERE user_id = :userID", array(
                'salt'      => $salt,
                'password'  => sha1($salt . sha1($salt . sha1($password))),
                'userID'    => $userID));
    }

    public function editCode($email, $code)
    {
        $this->query("UPDATE PREFIX_user SET 
            code = :code
            WHERE LCASE(email) = :email",
            array(
                'code'  => $code,
                'email' => utf8_strtolower($email)
            ));
    }

    public function deleteUser($userID)
    {
        $this->query("DELETE FROM PREFIX_user WHERE user_id = :userID", array('userID' => $userID));
    }

    public function getUser($userID)
    {
        return $this->query("SELECT * FROM PREFIX_user WHERE user_id = :userID", array('userID' => $userID))->fetch();
    }

    public function getUserByUsername($username)
    {
        return $this->query("SELECT * FROM PREFIX_user WHERE username = :username", array('username' => $username))->fetch();
    }

    public function getUserByCode($code)
    {
        return $this->query("SELECT * FROM PREFIX_user WHERE code = :code AND code != ''", array('code' => $code))->fetch();
    }

    public function getUsers($data = array())
    {
        $sql = "SELECT * FROM PREFIX_user";

        $sort_data = array(
            'username',
            'status',
            'date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY username";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        return $this->query($sql)->fetchAll();
    }

    public function getTotalUsers()
    {
        $query = $this->query("SELECT COUNT(*) AS total FROM PREFIX_user")->fetch();

        return $query['total'];
    }

    public function getTotalUsersByGroupId($userGroupID)
    {
        $query = $this->query("SELECT COUNT(*) AS total 
            FROM PREFIX_user 
            WHERE user_group_id = :userGroupID", array('userGroupID' => $userGroupID))->fetch();

        return $query['total'];
    }

    public function getTotalUsersByEmail($email)
    {
        $query = $this->query("SELECT COUNT(*) AS total 
            FROM PREFIX_user 
            WHERE LCASE(email) = :email", array('email' => utf8_strtolower($email)))->fetch();

        return $query['total'];
    }

    public function addTodo($todo)
    {
        $this->query("INSERT INTO PREFIX_todo SET 
            creator_id = :creatorID,
            closer_id = 0,
            content = :content,
            date_added = NOW()", array(
                'creatorID'     => $this->user->getId(),
                'content'       => $todo
            ));

        return $this->lastInsertId();
    }

    public function completeTodo($todoID) {
        $this->query("UPDATE PREFIX_todo SET closer_id = :closerID WHERE todo_id = :todoID", array(
            'closerID'  => $this->user->getId(),
            'todoID'    => $todoID 
        ));
    }

    public function getTodos()
    {
        return $this->fetchAll("SELECT * FROM PREFIX_todo WHERE closer_id = 0 ORDER BY date_added ASC");
    }
}
