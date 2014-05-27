<?php

namespace Anax\Authentication;

/**
*   Simple authentication class
*/
class Authentication extends \Anax\MVC\CDatabaseModel
{

    private $dbname;

    public function __construct($dbname) {
        $this->dbname = $dbname;
    }

    /**
     * Initialize the session
     * @return void
     */
    public function initialize()
    {
        if (is_null($this->session->get('user'))) {
            $user = new \Anax\Users\User();
            $this->session->set('user', $user);
        }
    }

    /**
     * Authenticate and login
     * @param string $username
     * @param string $password
     *
     * @return status
     */
    public function authenticate($username, $password)
    {
        $this->initialize();

        $user = $this->session->get('user');

        $this->db->select()
                 ->from($this->dbname)
                 ->where('acronym = ?');

        $this->db->execute([$username]);
        $ret = $this->db->fetchAll();

        if ($ret != null && password_verify($password, $ret[0]->password)) {
            $user->id = $ret[0]->id;
            $user->username = $ret[0]->acronym;
            $user->permission = $ret[0]->permission;
            $user->online = true;

            $this->session->noSet('user', $user);
            // Add user to session
            $this->session->set('user', $user);

            return true;
        }

        return false;
    }

    public function isAuthenticated()
    {
        if (!is_null($this->session->get('user'))) {
            $user = $this->session->get('user');
            return $user->online;
        }

        return false;
    }

    public function getPermission()
    {
        $user = $this->session->get('user');

        return $user->permission;

    }

    public function isAdmin()
    {
        $user = $this->session->get('user');

        if (is_object($user)) {
            if ($user->permission == 3) {
                return true;
            }
        }

        return false;
    }

    public function isUser()
    {
        $user = $this->session->get('user');

        if ($user->permission == 1) {
            return true;
        }
        return false;
    }

    public function username()
    {
        $user = $this->session->get('user');

        return $user->username;
    }

    public function id()
    {
        $user = $this->session->get('user');

        return $user->id;
    }

    public function password()
    {
        $user = $this->session->get('user');

        return $user->password;
    }

    public function logOut()
    {
        $this->session->noSet('user');
        $this->initialize();
    }
}
