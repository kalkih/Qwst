<?php

namespace Anax\Tag;

/**
* Tag class
*/
class Tag extends \Anax\MVC\CDatabaseModel
{

    public function findAll()
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->orderBy($this->db->getTablePrefix() . $this->getSource() .'.uses DESC');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findByLimit($limit = 5)
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->orderBy($this->db->getTablePrefix() . $this->getSource() .'.uses DESC')
                 ->limit($limit);

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findAllByTag($tag)
    {

        $this->db->select()
                 ->from('question');

        $this->db->execute();
        $questions = $this->db->fetchAll();
        $all = null;

        foreach($questions as $question)
        {
            $tags = explode(',', $question->tags);

            foreach($tags as $tag)
            {
                if($name == $tag)
                {
                    $all[] = $question;
                }
            }
        }

        return $all;
    }

    public function findByTag($tag)
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->where('text = ?');

        $this->db->execute([$tag]);
        return $this->db->fetchInto($this);
    }

    public function findQuestions($tag)
    {
        $this->db->select($this->db->getTablePrefix() . 'question.*,' . $this->db->getTablePrefix() . 'question' . '.id AS q_id , phpmvc_question.created AS created')
                 ->from('question')
                 ->join('user', $this->db->getTablePrefix() . 'user.id = ' . $this->db->getTablePrefix() . 'question.user_id')
                 ->orderBy($this->db->getTablePrefix() . 'question.created DESC');

        $this->db->execute();
        $all = $this->db->fetchAll();
        $questions = null;

        foreach ($all as $one) {
            $tags = unserialize($one->tags);

            if (!is_bool($tags)) {
                if (in_array($tag->text, $tags)) {
                    $questions[] = $one;
                }
            }
        }

        echo "<pre>";
        var_dump($questions);
        echo "</pre>";

        return $questions;
    }
}
