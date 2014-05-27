<?php

namespace Anax\Comments;

/**
 * Model for Users
 *
 */
class Comment extends \Anax\MVC\CDatabaseModel
{

    /**
     * Save current object/row.
     *
     * @param array $values key/values to save or empty to use object properties.
     *
     * @return boolean true or false if saving went okey.
     */
    public function save($values = [])
    {
        $this->setProperties($values);
        $values = $this->getProperties();
     
        if (isset($values['id'])) {
            return $this->update($values);
        } else {
            return $this->create($values);
        }
    }
}
