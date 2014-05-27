<?php

namespace Anax\Question;

/**
* Question class
*/
class Question extends \Anax\MVC\CDatabaseModel
{

    /**
     * Find and return all.
     *
     * @return array
     */
    public function findAll()
    {
        $this->db->select('*, ' . $this->db->getTablePrefix() . $this->getSource() . '.id AS q_id, ' . $this->db->getTablePrefix() . $this->getSource() . '.score AS q_score' . ', phpmvc_question.created AS created')
                 ->from($this->getSource())
                 ->join('user', $this->db->getTablePrefix() . 'user.id = ' . $this->db->getTablePrefix() . $this->getSource() . '.user_id')
                 ->orderBy($this->db->getTablePrefix() . $this->getSource() .'.created DESC');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    /**
     * Find and return latest.
     *
     * @return array
     */
    public function findLatest($nr)
    {
        $this->db->select('*, ' . $this->db->getTablePrefix() . $this->getSource() . '.id AS q_id , phpmvc_question.created AS created')
                 ->from($this->getSource())
                 ->join('user', $this->db->getTablePrefix() . 'user.id = ' . $this->db->getTablePrefix() . $this->getSource() . '.user_id')
                 ->orderBy($this->db->getTablePrefix() . $this->getSource() .'.created DESC')
                 ->limit($nr);

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function findByUser($user, $limit = null)
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->where('user_id = ?')
                 ->orderBy($this->db->getTablePrefix() . $this->getSource() . '.created DESC')
                 ->limit($limit);

        $this->db->execute([$user]);
        return $this->db->fetchAll();
    }

    public function findAnswersByUser($user, $limit = null)
    {
        $this->db->select('phpmvc_answer.*, phpmvc_question.title AS title, phpmvc_question.id AS q_id, phpmvc_question.slug AS q_slug')
                 ->from('answer')
                 ->where('phpmvc_answer.user_id = ?')
                 ->join('question', 'phpmvc_answer.question_id = phpmvc_question.id')
                 ->orderBy($this->db->getTablePrefix() . 'answer' . '.created DESC')
                 ->limit($limit);

        $this->db->execute([$user]);
        return $this->db->fetchAll();
    }

    public function findCommentsByUser($user, $type, $limit = null)
    {
        $this->db->select('phpmvc_' . $type . '_comment.*, phpmvc_question.title AS title, phpmvc_question.id AS q_id, phpmvc_question.slug AS q_slug')
                 ->from($type . '_comment')
                 ->where('phpmvc_' . $type . '_comment.user_id = ?')
                 ->join('question', 'phpmvc_' . $type . '_comment.reference_id = phpmvc_question.id')
                 ->orderBy($this->db->getTablePrefix() . $type . '_comment' . '.created DESC')
                 ->limit($limit);

        $this->db->execute([$user]);
        return $this->db->fetchAll();
    }

    /**
     * Find and return.
     *
     * @return array
     */
    public function findQuestion($id)
    {
        $this->db->select('*, ' . $this->db->getTablePrefix() . $this->getSource() . '.id AS q_id, ' . $this->db->getTablePrefix() . $this->getSource() . '.score AS q_score' . ', phpmvc_question.created AS created')
                 ->from($this->getSource())
                 ->where($this->db->getTablePrefix() . $this->getSource() . ".id = ?")
                 ->join('user', 'phpmvc_user.id = phpmvc_question.user_id');

        $this->db->execute([$id]);
        return $this->db->fetchInto($this);
    }

    public function findAnswers($id, $sort = 'score')
    {
        $this->db->select('phpmvc_answer.*, phpmvc_user.acronym AS acronym, phpmvc_user.email AS email')
                 ->from('answer')
                 ->where('question_id = ?')
                 ->join('user', 'phpmvc_user.id = phpmvc_answer.user_id')
                 ->orderBy($this->db->getTablePrefix() . 'answer.' . $sort . ' DESC');

        $this->db->execute([$id]);
        return $this->db->fetchAll();
    }

    public function findAnswerCount($id)
    {
        $this->db->select('count(*) AS count')
                 ->from('answer')
                 ->where('question_id = ?');

        $this->db->execute([$id]);
        return $this->db->fetchOne();
    }

    public function findAnswer($id)
    {
        $this->db->select()
                 ->from('answer')
                 ->where($this->db->getTablePrefix() . 'answer' . ".id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchInto($this);
    }

    /**
     * Find and return latest.
     *
     * @return array
     */
    public function findLatestAnswers($nr)
    {
        $this->db->select('phpmvc_answer.*, phpmvc_user.acronym AS acronym, phpmvc_user.email AS email, phpmvc_question.slug AS slug, phpmvc_question.title AS title')
                 ->from('answer')
                 ->join('user', 'phpmvc_user.id = phpmvc_answer.user_id')
                 ->join('question', 'phpmvc_question.id = phpmvc_answer.question_id')
                 ->orderBy($this->db->getTablePrefix() . 'answer.created DESC')
                 ->limit($nr);

        $this->db->execute([$nr]);
        return $this->db->fetchAll();
    }

    /**
     * Find and return latest.
     *
     * @return array
     */
    public function findLatestComments($limit, $type)
    {
        $this->db->select('phpmvc_' . $type . '_comments.*, phpmvc_user.acronym AS acronym, phpmvc_user.email AS email, phpmvc_question.slug AS slug, phpmvc_question.title AS title')
                 ->from($type . '_comments')
                 ->join('user', 'phpmvc_user.id = phpmvc_answer.user_id')
                 ->join('question', 'phpmvc_question.id = phpmvc_' . $type . '_comments.reference_id')
                 ->orderBy($this->db->getTablePrefix() . 'answer.created DESC')
                 ->limit($limit);

        $this->db->execute([$limit]);
        return $this->db->fetchAll();
    }

    public function saveAnswer($values = [])
    {
        $this->db->insert(
            'answer',
            ['user_id', 'question_id', 'content', 'created']
        );

        $this->db->execute([
            $values['user_id'],
            $values['question_id'],
            $values['content'],
            $values['created'],
        ]);
    }

    public function updateAnswer($values = [])
    {
        $this->setProperties($values);
        $values = $this->getProperties();

        if (isset($values['id'])) {
            $this->updateTable($values, 'answer');
        } else {
            return $this->create($values);
        }
    }

    public function setAnswer($values = [])
    {
        $this->db->insert(
            'answer',
            ['correct_answer']
        );

        $this->db->execute([
            $values['correct_answer'],
        ]);

        $keys   = array_keys($values);
        $values = array_values($values);

        // Its update, remove id and use as where-clause
        unset($keys['id']);
        $values[] = $this->id;

        $this->db->update(
            'answer',
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function findComments($type, $id)
    {
        $this->db->select('phpmvc_' . $type . '_comment' . '.*, phpmvc_user.acronym AS acronym, phpmvc_user.email AS email')
                 ->from($type . '_comment')
                 ->where('reference_id = ?')
                 ->join('user', 'phpmvc_user.id = phpmvc_' . $type . '_comment' . '.user_id')
                 ->orderBy($this->db->getTablePrefix() . $type . '_comment.created ASC');

        $this->db->execute([$id]);
        return $this->db->fetchAll();
    }

    public function saveComment($values = [], $type)
    {
        $this->db->insert(
            $type . '_comment',
            ['user_id', 'reference_id', 'content', 'created']
        );

        $this->db->execute([
            $values['user_id'],
            $values['reference_id'],
            $values['content'],
            $values['created'],
        ]);
    }

    public function updateComment($values = [], $type)
    {
        $this->setProperties($values);
        $values = $this->getProperties();

        if (isset($values['id'])) {
            $this->updateTable($values, $type . '_comment');
        } else {
            return $this->create($values);
        }
    }

    /**
     * Find and return.
     *
     * @return array
     */
    public function findBySlug($slug)
    {
        $this->db->select()
                 ->from($this->getSource())
                 ->where('slug = ?');

        $this->db->execute([$slug]);
        return $this->db->fetchInto($this);
    }
}
