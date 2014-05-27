<?php

namespace Anax\Users;

/**
 * Model for Users
 *
 */
class User extends \Anax\MVC\CDatabaseModel
{
    public $online;
    public $permission;
    public $username;
    public $id;
    public $password;

    public function __construct($permission = 0)
    {
        $this->permission = $permission;
        $this->online = 0;
        $this->username = "Unknown";
    }

    /**
     * Find and return specific from name.
     *
     * @return this
     */
    public function findByName($acronym)
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->where("acronym = ?");

        $this->db->execute([$acronym]);
        return $this->db->fetchInto($this);
    }

    public function permissionToString()
    {
        if ($this->permission == 3) {
            return 'Administrator';
        }

        if ($this->permission == 2) {
            return 'Moderator';
        }

        if ($this->permission == 1) {
            return 'User';
        }
    }

    /**
     * Find and return latest.
     *
     * @return array
     */
    public function findLatest($nr)
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->orderBy($this->db->getTablePrefix() . $this->getSource() .'.created DESC')
                 ->limit($nr);

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    /**
     * Find and return top.
     *
     * @return array
     */
    public function findTop($limit = 5)
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->orderBy($this->db->getTablePrefix() . $this->getSource() .'.score DESC')
                 ->limit($limit);

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    /**
     * Question score
     *
     * @return array
     */
    public function getQuestionScore($id)
    {
        $this->db->select('phpmvc_question.score')
                 ->from('question')
                 ->where($this->db->getTablePrefix() . 'question' . ".user_id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchAll();
    }

    /**
     * Answer score
     *
     * @return array
     */
    public function getAnswerScore($id)
    {
        $this->db->select('phpmvc_answer.score')
                 ->from('answer')
                 ->where($this->db->getTablePrefix() . 'answer' . ".user_id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchAll();
    }

}
