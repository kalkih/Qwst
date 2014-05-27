<?php

namespace Anax\Comments;

/**
 * A controller for users and admin related events.
 *
 */ 
class CommentsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->comments = new \Anax\Comments\Comment();
        $this->comments->setDI($this->di);
    }

     /**
     * View all comments.
     *
     * @return void
     */
    public function viewAction()
    {
        $this->initialize();

        $all = $this->comments->findAll();

        $this->views->add('comments/comments', [
            'comments' => $all,
        ], 'sidebar');
    }

    /**
     * Add a comment.
     *
     * @param string $name of the person.
     * @param string $content of the comment.
     * @param string $email of the person.
     * @param string $web Website of the person.
     * @param string $url to redirect to.
     * 
     * @return void
     */
    public function addAction($name = null, $content = null, $email = null, $web = null, $url = null)
    {   
        $this->initialize();

        if (!isset($name)) {
            die("Missing name");
        }

        $now = date("Y-m-d h:i:s");

        $this->comments->save([
            'name' => $name,
            'content' => $content,
            'email' => $email,
            'web' => $web,
            'ip' => $this->request->getServer('REMOTE_ADDR'),
            'created' => $now,
            'active' => $now,
        ]);

        $this->flashy->add('success', 'Comment was posted!');

        $this->response->redirect($url);
    }

    /**
     * Save a comment.
     *
     * @param integer $id of comment to save.
     * 
     * @return void
     */
    public function saveAction($name = null, $content = null, $email = null, $web = null, $id = null) {
        
        $this->initialize();

        if (!isset($id)) {
            die("Missing id");
        }

        $comment = $this->comments->find($id);
     
        $now = date("Y-m-d h:i:s");
        
        $comment->name = $name;
        $comment->content = $content;
        $comment->email = $email;
        $comment->web = $web;

        $comment->updated = $now;
        $comment->save();

        $this->flashy->add('success', 'Comment was saved!');
        
        $url = $this->url->create('redovisning');
        $this->response->redirect($url);
    }

    
    /**
     * Update comment.
     *
     * @param integer $id of comment to update.
     *
     * @return void
     */
    public function updateAction($id)
    {
        $this->initialize();

        if (!isset($id)) {
            die("Missing id");
        }

        $comment = $this->comments->find($id);

        $data = [
            'method'    => 'save',
            'name'      => $comment->name,
            'content'   => $comment->content,
            'email'     => $comment->email,
            'web'       => $comment->web,
            'id'        => $comment->id,
        ];

        $this->session->set('data', $data);

        $url = $this->url->create($_SERVER['HTTP_REFERER'] . '#form-link');
        $this->response->redirect($url);
    }

    /**
     * Delete comment.
     *
     * @param integer $id of comment to delete.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        $this->initialize();

        if (!isset($id)) {
            die("Missing id");
        }
     
        $res = $this->comments->delete($id);

        $this->flashy->add('success', 'Comment was deleted!');
     
        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Remove all comments.
     *
     * @return void
     */
    public function deleteAllAction()
    {
        $this->initialize();

        $this->comments->deleteAll();

        $this->flashy->add('success', 'All comments deleted!');

        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }
}
