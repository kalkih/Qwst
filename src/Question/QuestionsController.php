<?php

namespace Anax\Question;

/**
* QuestionsController class for the Question class
*/
class QuestionsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     */
    public function initialize()
    {
        $this->questions = new \Anax\Question\Question();
        $this->questions->setDI($this->di);

        $this->tags = new \Anax\Tag\TagsController();
        $this->tags->setDI($this->di);

        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);

        $this->usersController = new \Anax\Users\UsersController();
        $this->usersController->setDI($this->di);
    }

    /**
     * List all questions.
     *
     */
    public function indexAction($all = null)
    {
        $this->initialize();

        if (!isset($null)) {
            $all = $this->questions->findAll();
        }

        foreach ($all as $question => $value) {
            $value->content = $this->textFilter->doFilter(htmlentities(strip_tags($value->content)), 'shortcode, markdown');
            $count = $this->questions->findAnswerCount($value->q_id);
            $value->count = $count->count;
        }

        $this->theme->setTitle("Questions");
        $this->views->add('questions/list', [
            'title' => "Questions",
            'questions' => $all,
        ], 'main');

        $this->dispatcher->forward([
            'controller' => 'tags',
            'action'     => 'popular',
            'params' => ["sidebar", 8]
        ]);
    }

    public function questionAction()
    {
        $this->initialize();

        $all = $this->questions->findAll();

        foreach ($all as $question => $value) {
            $value->content = $this->textFilter->doFilter(htmlentities(strip_tags($value->content)), 'shortcode, markdown');
        }

        return $all;
    }

    /**
     * Sidebar.
     *
     */
    public function sidebarAction()
    {
        $this->initialize();

        $all = $this->questions->findLatest(3);

        foreach ($all as $question => $value) {
            $value->content = $this->textFilter->doFilter(htmlentities(strip_tags($value->content)), 'shortcode, markdown');
        }

        $this->views->add('questions/sidebar', [
            'title' => "New Questions",
            'questions' => $all,
        ], 'sidebar');
    }

    /**
     * Footer.
     *
     */
    public function footerAction($area = 'main')
    {
        $this->initialize();

        $all = $this->questions->findLatest(3);

        foreach ($all as $question => $value) {
            $value->content = $this->textFilter->doFilter(htmlentities(strip_tags($value->content)), 'shortcode, markdown');
        }

        $this->views->add('questions/footer', [
            'title' => "Latest Questions",
            'questions' => $all,
        ], $area);
    }

    /**
     * Footer.
     *
     */
    public function footer2Action($area = 'main')
    {
        $this->initialize();

        $all = $this->questions->findLatestAnswers(3);

        foreach ($all as $question => $value) {
            $value->content = $this->textFilter->doFilter(htmlentities(strip_tags($value->content)), 'shortcode, markdown');
        }

        $this->views->add('questions/footer2', [
            'title' => "Latest Answers",
            'questions' => $all,
        ], $area);
    }

    /**
     * Show question.
     *
     */
    public function titleAction($id = null, $slug = null, $sort = 'score')
    {
        $this->initialize();

        if ($sort != 'score' && $sort != 'created') {
            $sort = 'score';
        }

        if ($id == null && $slug == null) {
            $question = null;
        } else {
            $question = $this->questions->findQuestion($id);
            $answers = $this->questions->findAnswers($id, $sort);
        }

        if (!is_object($question)) {
            $this->views->add('default/error', [
                'content' => 'Question does not exist',
                'details' => 'Question is deleted or does not exist.',
                'title' => '404 - Question not found!',
            ], 'full');
        } else {

            $question->content = $this->textFilter->doFilter(htmlentities(strip_tags($question->content)), 'shortcode, markdown');
            $this->theme->setTitle($question->title);
            $this->views->add('questions/question', [
                'question' => $question,
                'answers' => $answers,
                'sort' => $sort,
                'title' => $question->title,
            ], 'main');

            $this->dispatcher->forward([
                'controller' => 'tags',
                'action'     => 'popular',
                'params' => ["sidebar", 8]
            ]);
        }
    }

    /**
     * Get comments.
     *
     */
    public function commentsAction($type = null, $id = null)
    {
        $this->initialize();

        if ($type == null || $id == null || !is_string($type)) {
            exit();
        } else {
            if ($type == 'answer') {
                $comments = $this->questions->findComments($type, $id);
            } elseif ($type == 'question') {
                $comments = $this->questions->findComments($type,$id);
            } else {
                exit();
            }
        }

        if (empty($comments)) {
            $this->views->add('default/error', [
                'content' => 'Comment does not exist',
                'details' => 'Comment is deleted or does not exist.',
                'title' => '404 - Comment not found!',
            ], 'full');
        } else {

            foreach ($comments as $comment => $value) {
                $value->content = $this->textFilter->doFilter(htmlentities(strip_tags($value->content)), 'shortcode, markdown');
            }
            return $comments;
        }
    }

    /**
     * create new comment.
     *
     * @return void
     */
    public function commentAction($type = null, $id = null, $q_id = null)
    {
        $this->initialize();

        if ($type == null || $id == null || $q_id == null || !is_string($type)) {
            exit();
        } else {
            if ($type != 'answer' && $type != 'question') {
                exit();
            }
        }

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'Please sign in!');
            $url = $this->url->create('users/login');
            $this->response->redirect($url);
            exit();
        }

        $this->theme->setTitle("Comment");

        $form = $this->form;

        $form = $form->create([], [
            'comment' => [
                'type' => 'textarea',
                'placeholder' => 'Comment...',
                'required' => true,
                'validation' => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
            'reset' => [
                'type' => 'reset',
                'callback' => function($form) {
                    $form->saveInSession = false;
                    return false;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {
            // What to do if the form was submitted?
            $content = $_SESSION['form-save']['comment']['value'];

            $this->session->noSet('form-save');

            $now = date("Y-m-d H:i:s");

            $this->questions->saveComment([
                'user_id' => $this->session->get('user')->id,
                'reference_id' => $id,
                'content' => htmlentities(strip_tags($content)),
                'created' => $now,
            ], $type);

            $this->usersController->post($this->auth->password());

            $this->flashy->add('success', "Your comment was posted!");

            $slug = $this->questions->find($q_id)->slug;

            $url = $this->url->create('questions/title/' . $q_id . '/' . $slug);
            $this->response->redirect($url);
        }

        $this->views->add('default/page', [
            'title' => 'New comment',
            'content' => $form->getHTML(),
        ], 'main');
    }

    /**
     * Show latest question.
     *
     */
    public function latestAction($user = null)
    {
        $this->initialize();

        if ($user == null) {
            $questions = $this->questions->findLatest(5);
        } else {
            $questions = $this->questions->findByUser($user, 5);
        }

        return $questions;
    }

    /**
     * Show latest question.
     *
     */
    public function latestAnswersAction($user = null)
    {
        $this->initialize();

        if ($user == null) {
            $answers = $this->questions->findLatestAnswers(5);
        } else {
            $answers = $this->questions->findAnswersByUser($user, 5);
        }

        return $answers;
    }

    /**
     * Show latest comments.
     *
     */
    public function latestCommentsAction($user = null)
    {
        $this->initialize();

        if ($user == null) {
            $comments = array_merge($this->questions->findLatestComments(5, 'question'), $this->questions->findLatestComments(5, 'answer'));
        } else {
            $comments = array_merge($this->questions->findCommentsByUser($user, 'question', 3), $this->questions->findCommentsByUser($user, 'answer', 3));
        }

        return $comments;
    }

    /**
     * Create slug from string
     *
     * @param string $str string to covert to slug.
     *
     * @return string
     *
     */
    private function createSlug($str)
    {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

        return $clean;
    }

    /**
     * create new question.
     *
     * @return void
     */
    public function newAction()
    {
        $this->initialize();

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'Please sign in!');
            $url = $this->url->create('users/login');
            $this->response->redirect($url);
            exit();
        }

        $this->theme->setTitle("Ask a question");

        if (!isset($acronym)) {
            $form = $this->form;

            $form = $form->create([], [
                'title' => [
                    'type' => 'text',
                    'placeholder' => 'Title',
                    'required' => true,
                    'validation' => ['not_empty'],
                ],
                'question' => [
                    'type'        => 'textarea',
                    'placeholder' => 'Question...',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'tags' => [
                    'type'        => 'text',
                    'placeholder' => 'yolo, swag, cool',
                    'required'    => false,
                ],
                'submit' => [
                    'type'      => 'submit',
                    'callback'  => function($form) {
                        $form->saveInSession = true;
                        return true;
                    }
                ],
                'reset' => [
                    'type' => 'reset',
                    'callback' => function($form) {
                        $form->saveInSession = false;
                        return false;
                    }
                ],
            ]);

            // Check the status of the form
            $status = $form->check();

            if ($status === true) {
                // What to do if the form was submitted?
                $title = $_SESSION['form-save']['title']['value'];
                $content = $_SESSION['form-save']['question']['value'];
                $tags = $_SESSION['form-save']['tags']['value'];

                if (isset($tags)) {
                    $tags = htmlentities(strip_tags($tags));
                    $tags = strtolower($tags);
                    $tags = str_replace(' ', '', $tags);
                    $tags = str_replace('#', '', $tags);
                    $tags = explode(',', $tags);
                    if (sizeof($tags) > 8) {
                        $_SESSION['form-save']['tags']['value'] = null;
                        $this->flashy->add('error', "You can not use more than <b>8</b> tags!");
                        $this->response->redirect($_SERVER['PHP_SELF']);
                        exit();
                    }
                    foreach ($tags as $tag => $value) {
                        if (strlen($value) > 16) {
                            $this->flashy->add('error', "Tags can not be longer than <b>16</b> characters!");
                            $this->response->redirect($_SERVER['PHP_SELF']);
                            exit();
                        }
                        if (empty($value)) {
                            unset($tags[$tag]);
                        }
                    }

                    $this->tags->check($tags);

                }

                $this->session->noSet('form-save');

                $now = date("Y-m-d H:i:s");
                $slug = $this->createSlug($title);

                $this->questions->save([
                    'user_id' => $this->session->get('user')->id,
                    'title' => htmlentities(strip_tags($title)),
                    'content' => htmlentities(strip_tags($content)),
                    'tags' => serialize($tags),
                    'slug'      => $slug,
                    'created' => $now,
                ]);

                $this->usersController->post($this->auth->password());

                $this->flashy->add('success', "Your question was posted!");

                $url = $this->url->create('questions/title/' . $this->questions->findBySlug($slug)->id . '/' . $slug);
                $this->response->redirect($url);

            }

            $this->views->add('default/page', [
                'title' => 'New question',
                'content' => $form->getHTML(),
            ], 'main');
        }
    }

    /**
     * vote question.
     *
     * @return void
     */
    public function voteAction($id = null, $value = null)
    {
        $this->initialize();

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'Please sign in to vote!');
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        if (!isset($id) || !isset($value)) {
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        $question = $this->questions->find($id);

        $user = $this->users->find($this->auth->id());
        $votes = unserialize($user->q_votes);

        if (is_bool($votes) || !in_array($id, $votes)) {
            if ($value == 'up') {
                $question->score++;
            } elseif($value == 'down'){
                $question->score--;
            }
            $votes[] = $id;
            $user->save([
                'id' => $user->id,
                'q_votes' => serialize($votes),
            ]);
            $question->saveReal();
        } else {
            $this->flashy->add('warning', 'You have already voted on this question!');
        }

        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * vote answer question.
     *
     * @return void
     */
    public function voteAnswerAction($id = null, $value = null)
    {
        $this->initialize();

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'Please sign in to vote!');
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        if (!isset($id) || !isset($value)) {
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        $answer = $this->questions->findAnswer($id);

        $user = $this->users->find($this->auth->id());
        $votes = unserialize($user->a_votes);

        if (is_bool($votes) || !in_array($id, $votes)) {
            if ($value == 'up') {
                $answer->score++;
            } elseif($value == 'down'){
                $answer->score--;
            }
            $votes[] = $id;
            $user->save([
                'id' => $user->id,
                'a_votes' => serialize($votes),
            ]);
            $answer->updateAnswer([
                'id' => $answer->id,
                'score' => $answer->score,
            ]);
        } else {
            $this->flashy->add('warning', 'You have already voted on this answer!');
        }

        $this->response->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Set correct answer.
     *
     * @return void
     */
    public function correctAction($id = null, $answer_id = null)
    {
        $this->initialize();

        if (!isset($id) || !isset($answer_id)) {
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        $question = $this->questions->find($id);

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'You do not have permission to this page!');
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        if ($question->user_id != $this->auth->id()) {
            $this->flashy->add('error', 'You do not have permission to this page!');
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        if ($question->correct_answer == $answer_id) {
            $question->correct_answer = null;
            $this->flashy->add('success', 'You have unmarked a correct answer!');
        } else {
            $question->correct_answer = $answer_id;
            $this->flashy->add('success', 'You have marked a correct answer!');
        }

        $question->saveReal();


        $this->response->redirect($_SERVER['HTTP_REFERER']);

    }

    /**
     * create new answer.
     *
     * @return void
     */
    public function answerAction($id = null, $slug = null)
    {
        $this->initialize();

        if ($id == null || $slug == null) {
            $this->views->add('default/error', [
                'content' => 'Question does not exist',
                'details' => 'Question is deleted or does not exist.',
                'title' => '404 - Question not found!',
            ], 'full');
            exit();
        }

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'Please sign in!');
            $url = $this->url->create('users/login');
            $this->response->redirect($url);
            exit();
        }

        $this->theme->setTitle("Answer");

        $form = $this->form;

        $form = $form->create([], [
            'answer' => [
                'type' => 'textarea',
                'placeholder' => 'Answer...',
                'required' => true,
                'validation' => ['not_empty'],
            ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
            'reset' => [
                'type' => 'reset',
                'callback' => function($form) {
                    $form->saveInSession = false;
                    return false;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {
            // What to do if the form was submitted?
            $content = $_SESSION['form-save']['answer']['value'];

            $this->session->noSet('form-save');

            $now = date("Y-m-d H:i:s");

            $this->questions->saveAnswer([
                'user_id' => $this->session->get('user')->id,
                'question_id' => $id,
                'content' => htmlentities(strip_tags($content)),
                'created' => $now,
            ]);

            $this->usersController->post($this->auth->password());

            $this->flashy->add('success', "Your answer was posted!");

            $url = $this->url->create('questions/title/' . $id . '/' . $slug);
            $this->response->redirect($url);
        }

        $this->views->add('default/page', [
            'title' => 'New answer',
            'content' => $form->getHTML(),
        ], 'main');
    }

    /**
     * Setup question db.
     *
     */
    public function setupAction() {

        $this->initialize();

        $this->theme->setTitle('Setup');

        $this->db->dropTableIfExists('question')->execute();

        $this->db->createTable(
            'question',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'user_id' => ['integer', 'not null'],
                'title' => ['varchar(100)', 'not null'],
                'content' => ['text', 'not null'],
                'tags' => ['text'],
                'slug'    => ['varchar(100)', 'not null'],
                'correct_answer' => ['integer'],
                'score' => ['integer', 'default "0"'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
            ]
        )->execute();

        // Answers
        $this->db->dropTableIfExists('answer')->execute();

        $this->db->createTable(
            'answer',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'user_id' => ['integer', 'not null'],
                'question_id' => ['integer', 'not null'],
                'content' => ['text', 'not null'],
                'score' => ['integer', 'default "0"'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
            ]
        )->execute();

        // Question Comments
        $this->db->dropTableIfExists('question_comment')->execute();

        $this->db->createTable(
            'question_comment',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'user_id' => ['integer', 'not null'],
                'reference_id' => ['integer', 'not null'],
                'content' => ['text', 'not null'],
                'score' => ['integer', 'default "0"'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
            ]
        )->execute();

        // Answer Comments
        $this->db->dropTableIfExists('answer_comment')->execute();

        $this->db->createTable(
            'answer_comment',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'user_id' => ['integer', 'not null'],
                'reference_id' => ['integer', 'not null'],
                'content' => ['text', 'not null'],
                'score' => ['integer', 'default "0"'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
            ]
        )->execute();

        $this->views->addString('<h1>Question & Answer database was successfully setup!</h1>', 'main');
    }
}
