<?php

namespace Anax\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);
    }

    public function logoutAction()
    {
        $user = $this->session->get('user');
        $this->auth->logout();
        $this->flashy->add('info', "See you later {$user->username}!");
        $this->response->redirect($this->url->create(''));
    }

    public function loginAction()
    {
        if ($this->auth->isAuthenticated()) {
            $this->response->redirect($this->url->create(''));
            $this->Flashy->info('You are already logged in!');
            exit();
        }

        $this->theme->setTitle('Login');

        $form = $this->form;
        $form = $form->create([], [
            'username' => [
                'type' => 'text',
                'name' => 'username',
                'placeholder' => 'Username:',
                'required' => true,
                'validation' => ['not_empty'],
            ],
            'password' => [
                'type' => 'password',
                'name' => 'password',
                'placeholder' => 'Password:',
                'required' => true,
                'validation' => ['not_empty'],
            ],
            'submit' => [
                'type' => 'submit',
                'callback' => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        $status = $form->check();
        if ($status === true) {
            $acronym = $_SESSION['form-save']['username']['value'];
            $password = $_SESSION['form-save']['password']['value'];
            $this->session->noSet('form-save');
            if ($this->auth->authenticate($acronym, $password)) {
                $name = $this->session->get('user')->username;
                $this->flashy->add('success', 'Welcome!');
                $url = $this->url->create('');
            } else {
                $this->flashy->add('error', 'Incorrect username or password!');
                $url = $this->url->create('users/login');
            }

            $this->response->redirect($url);
            exit();
        }

        $this->views->add('default/page', [
            'title' => 'Login',
            'content' => $form->getHTML(),
        ], 'triptych-1');

        $url = $this->url->create('users/register');
        $this->views->addString("<h1>Sign up</h1><p>Not a member yet? Sign up <a class='hoverDark' href=" . $url . ">here</a>.</p>", 'triptych-2');
    }


    /**
     * List all users.
     *
     * @return void
     */
    public function listAction()
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $all = $this->users->findAll();

        $this->theme->setTitle("List all users");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "View all users",
        ], 'main');
    }

    /**
     * List all users.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->initialize();

        $all = $this->users->findAll();

        $this->theme->setTitle("Users");

        if ($this->auth->isAdmin()) {
            $this->views->add('users/all-admin', [
                'title' => "Users",
                'users' => $all,
            ], 'full');
        } else {
            $this->views->add('users/all', [
                'title' => "Users",
                'users' => $all,
            ], 'full');
        }
    }

    /**
     * List latest users.
     *
     * @return void
     */
    public function footerAction($area = 'main')
    {
        $this->initialize();

        $all = $this->users->findLatest(7);

        $this->views->add('users/footer', [
            'title' => "New Users",
            'users' => $all,
        ], $area);
    }

    /**
     * List latest users.
     *
     * @return void
     */
    public function topAction($area = 'main')
    {
        $this->initialize();

        $all = $this->users->findTop(7);

        $this->views->add('users/toplist', [
            'title' => "Most active users",
            'users' => $all,
        ], $area);
    }

    /**
     * post.
     *
     * @return void
     */
    public function post($password = null)
    {
        $this->initialize();

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'You do not have permission to this page!');
            $this->response->redirect($_SERVER['HTTP_REFERER']);
            exit();
        }

        if ($password == $this->auth->password()) {

            $user = $this->users->find($this->auth->id());
            $user->score++;
            $user->save([
                'id' => $user->id,
                'score' => $user->score
            ]);
        }
    }


    /**
     * Show user profile with acronym.
     *
     * @param string $acronym of user to display
     *
     * @return void
     */
    public function profileAction($acronym = null)
    {
        $this->initialize();

        $user = $this->users->findByName($acronym);
        $rep = array_merge($this->users->getQuestionScore($user->id), $this->users->getAnswerScore($user->id));
        $user->rep = 0;

        foreach ($rep as $val) {
            $user->rep += $val->score;
        }

        $questions = $this->dispatcher->forward([
            'controller' => 'questions',
            'action' => 'latest',
            'params' => [$user->id],
        ]);

        $answers = $this->dispatcher->forward([
            'controller' => 'questions',
            'action' => 'latestAnswers',
            'params' => [$user->id],
        ]);

        $comments = $this->dispatcher->forward([
            'controller' => 'questions',
            'action' => 'latestComments',
            'params' => [$user->id],
        ]);

        if (!is_object($user)) {
            $this->views->add('default/error', [
                'content' => 'User does not exist',
                'details' => 'User is deleted or does not exist.',
                'title' => '404 - User not found!',
            ], 'full');
        } else {

            if ($user->acronym == 'olund') {
                $this->theme->setTitle($user->acronym . 'is a scammer!!!');
                $this->views->add('users/scam', [
                    'user' => $user,
                    'title' => ucfirst($user->acronym) . 'is a sacmmer!!!',
                ]);
            }

            $this->theme->setTitle($user->acronym . '\'s Profile');
            $this->views->add('users/profile', [
                'user' => $user,
                'questions' => $questions,
                'answers' => $answers,
                'comments' => $comments,
                'title' => ucfirst($user->acronym) . '\'s profile',
            ], 'main');

            $this->views->add('users/profile-side', [
                'user' => $user,
                'title' => '',
            ], 'sidebar');
        }
    }


    /**
     * List user with id.
     *
     * @param int $id of user to display
     *
     * @return void
     */
    public function idAction($id = null)
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        if(is_numeric($id)) {
            $user = $this->users->find($id);
        } else {
            $user = $this->users->findByName($id);
        }

        // Form
        $form = $this->form;
        $form = $form->create([], [
            'id' => [
            'type'        => 'text',
            'label'       => 'Username or ID:',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
            'submit' => [
                'type'      => 'submit',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();
        if ($status === true) {
            // What to do if the form was submitted?
            $id = $_SESSION['form-save']['id']['value'];
            session_unset($_SESSION['form-save']['id']['value']);
            $url = $this->url->create('users/id/' . $id);
            $this->response->redirect($url);
        } else if ($status === false) {

        }

        $this->theme->setTitle("View user");
        $this->views->add('users/view', [
            'user' => $user,
            'title' => 'View user',
            'content' => $form->getHTML(),
        ]);
    }

    /**
     * List user with acronym.
     *
     * @param int $acronym of user to display
     *
     * @return void
     */
    public function acronymAction($acronym = null)
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $user = $this->users->findByName($acronym);

        $this->theme->setTitle("View user");
        $this->views->add('users/view', [
            'user' => $user,
            'title' => "View user",
        ]);
    }


    /**
     * Add new user.
     *
     * @param string $acronym of user to add.
     *
     * @return void
     */
    public function addAction($acronym = null)
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $this->theme->setTitle("Add user");

        if (!isset($acronym)) {
            $form = $this->form;

            $form = $form->create([], [
                'acronym' => [
                    'type'        => 'text',
                    'label'       => 'Username:',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'submit' => [
                    'type'      => 'submit',
                    'callback'  => function($form) {
                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            // Check the status of the form
            $status = $form->check();

            if ($status === true) {
                // What to do if the form was submitted?
                $acronym = $_SESSION['form-save']['acronym']['value'];
                session_unset($_SESSION['form-save']);
                $url = $this->url->create('users/add/' . $acronym);
                $this->response->redirect($url);
            }

            $this->views->add('me/page', [
                'content' => $form->getHTML(),
            ]);

        } else {

            $now = date("Y-m-d H:i:s");

            $this->users->save([
                'acronym' => $acronym,
                'email' => $acronym . '@mail.se',
                'name' => 'Mr/Mrs ' . $acronym,
                'password' => password_hash($acronym, PASSWORD_DEFAULT),
                'created' => $now,
                'active' => $now,
            ]);

            $url = $this->url->create('users/id/' . $this->users->id);
            $this->response->redirect($url);
        }
    }

    /**
     * register new user.
     *
     * @return void
     */
    public function registerAction()
    {
        $this->initialize();

        if ($this->auth->isAuthenticated()) {
            $this->flashy->add('warning', 'You are already signed in!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $this->theme->setTitle("Register");

        if (!isset($acronym)) {
            $form = $this->form;

            $form = $form->create([], [
                'name' => [
                    'type' => 'text',
                    'placeholder' => 'Name:',
                    'required' => true,
                    'validation' => ['not_empty'],
                ],
                'username' => [
                    'type'        => 'text',
                    'placeholder' => 'Username:',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'email' => [
                    'type' => 'email',
                    'placeholder' => 'Email:',
                    'required' => true,
                    'validation' => ['not_empty'],
                ],
                'password' => [
                    'type'        => 'password',
                    'placeholder' => 'Password:',
                    'required'    => true,
                    'validation'  => ['not_empty'],
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

                $name = $_SESSION['form-save']['name']['value'];
                $acronym = $_SESSION['form-save']['username']['value'];
                $email = $_SESSION['form-save']['email']['value'];
                $password = $_SESSION['form-save']['password']['value'];

                // Checks
                if (strlen($password) < 6) {
                    $_SESSION['form-save']['password']['value'] = null;
                    $this->flashy->add('error', "Your password must be atleast <b>6</b> characters long!");
                    $url = $this->url->create('users/register');
                    $this->response->redirect($url);
                    exit();
                }

                if (strlen($acronym) > 16) {
                    $_SESSION['form-save']['username']['value'] = null;
                    $this->flashy->add('error', "Your username can not be longer than <b>16</b> characters!");
                    $url = $this->url->create('users/register');
                    $this->response->redirect($url);
                    exit();
                }

                $this->session->noSet('form-save');

                $now = date("Y-m-d H:i:s");

                $this->users->save([
                    'acronym' => htmlentities(strip_tags($acronym)),
                    'email' => htmlentities(strip_tags($email)),
                    'name' => htmlentities(strip_tags($name)),
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'created' => $now,
                    'active' => $now,
                ]);

                $this->flashy->add('success', "Welcome {$name}!\nYour account was successfully registered!");

                $this->auth->authenticate($acronym, $password);

                $url = $this->url->create('');
                $this->response->redirect($url);
            }


            $this->views->add('default/page', [
                'title' => 'Register',
                'content' => $form->getHTML(),
            ], 'triptych-1');
        }
    }

    /**
     * Edit a user.
     *
     * @param string $username username of the user to edit
     *
     * @return void
     */
    public function editAction($username)
    {
        $this->initialize();

        if (!$this->auth->isAuthenticated()) {
            $this->flashy->add('error', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        if ($this->auth->username() != $username) {
            $this->flashy->add('error', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $this->theme->setTitle("Edit Profile");

        if (isset($username)) {
            $user = $this->users->findByName($username);
            $form = $this->form;
            $form = $form->create([], [
                'id' => [
                    'type' => 'hidden',
                    'value'    => $user->id,
                    'required' => true,
                ],
                'name' => [
                    'type' => 'text',
                    'placeholder' => 'Name:',
                    'value'         => $user->name,
                    'required' => true,
                    'validation' => ['not_empty'],
                ],
                'email' => [
                    'type' => 'email',
                    'placeholder' => 'Email:',
                    'value'         => $user->email,
                    'required' => true,
                    'validation' => ['not_empty'],
                ],
                'password' => [
                    'type'        => 'password',
                    'placeholder' => 'New password (optional):',
                ],
                'submit' => [
                    'type'      => 'submit',
                    'callback'  => function($form) {
                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            // Check the status of the form
            $status = $form->check();

            if ($status === true) {
                // What to do if the form was submitted?
                $id = $_SESSION['form-save']['id']['value'];
                $name = $_SESSION['form-save']['name']['value'];
                $email = $_SESSION['form-save']['email']['value'];
                $password = $_SESSION['form-save']['password']['value'];

                $this->session->noSet('form-save');

                $now = date("Y-m-d H:i:s");

                $user = $this->users->find($id);

                if (isset($password) && $password != '') {
                    $user->save([
                        'id' => $user->id,
                        'name' => $name,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'updated' => $now,
                    ]);
                } else {
                    $user->save([
                        'id' => $user->id,
                        'name' => $name,
                        'email' => $email,
                        'updated' => $now,
                    ]);
                }

                $this->flashy->add('success', "Your profile was successfully updated!");

                $url = $this->url->create('');
                $this->response->redirect($url);
            }


            $this->views->add('default/page', [
                'title' => 'Edit profile',
                'content' => $form->getHTML(),
            ], 'triptych-1');
        }
    }

    /**
     * Delete user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $this->theme->setTitle("Delete user");

        if (!isset($id)) {
            $form = $this->form;

            $form = $form->create([], [
                'id' => [
                    'type'        => 'text',
                    'label'       => 'Username or ID:',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],
                'submit' => [
                    'type'      => 'submit',
                    'callback'  => function($form) {
                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            // Check the status of the form
            $status = $form->check();

            if ($status === true) {
                // What to do if the form was submitted?
                $id = $_SESSION['form-save']['id']['value'];
                session_unset($_SESSION['form-save']);
                $url = $this->url->create('users/delete/' . $id);
                $this->response->redirect($url);
            }

            $this->views->add('me/page', [
                'content' => $form->getHTML(),
            ]);

        } else {

            if(!is_numeric($id)) {
                $user = $this->users->findByName($id);

                if(!is_numeric($id)) {
                    $id = $this->users->findByName($id)->id;
                }
            }

            $res = $this->users->delete($id);

            $this->flashy->add('success', 'User deleted!');

            $url = $this->url->create('users');
            $this->response->redirect($url);
        }
    }


    /**
     * List all active and not deleted users.
     *
     * @return void
     */
    public function activeAction()
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $all = $this->users->query()
            ->where('active IS NOT NULL')
            ->andWhere('deleted is NULL')
            ->execute();

        $this->theme->setTitle("Users that are active");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are active",
        ]);
    }


    /**
     * List all inactive and deleted users.
     *
     * @return void
     */
    public function inactiveAction()
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $all = $this->users->query()
            ->where('deleted IS NOT NULL')
            ->execute();

        $this->theme->setTitle("Users that are inactive");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are inactive",
        ]);
    }


    /**
     * Delete (soft) user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function softDeleteAction($id = null)
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        if (!isset($id)) {
            die("Missing id");
        }

        $now = date("Y-m-d H:i:s");

        $user = $this->users->find($id);

        $user->deleted = $now;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }

    /**
     * Undo (soft) user.
     *
     * @param integer $id of user to undo.
     *
     * @return void
     */
    public function softUndoAction($id = null)
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        if (!isset($id)) {
            die("Missing id");
        }

        $now = date("Y-m-d H:i:s");

        $user = $this->users->find($id);

        $user->deleted = NULL;
        $user->active = $now;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }

    /**
     * Update user.
     *
     * @param integer $id of user to update.
     *
     * @return void
     */
    public function updateAction($id = null)
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        if (isset($id)) {

            if(is_numeric($id)) {
                $user = $this->users->find($id);
            } else {
                $user = $this->users->findByName($id);
            }

            $form2 = $this->form2;
            $form2 = $form2->create([], [
                'id' => [
                    'type' => 'hidden',
                    'label' => 'id',
                    'required' => true,
                    'validation' => ['not_empty'],
                    'value' => $user->id,
                ],
                'acronym' => [
                    'type' => 'text',
                    'label' => 'Acronym',
                    'required' => true,
                    'validation' => ['not_empty'],
                    'value' => $user->acronym,
                ],
                'email' => [
                    'type' => 'text',
                    'label' => 'Email',
                    'required' => true,
                    'validation' => ['not_empty'],
                    'value' => $user->email,
                ],
                'name' => [
                    'type' => 'text',
                    'label' => 'Name',
                    'required' => true,
                    'validation' => ['not_empty'],
                    'value' => $user->name,
                ],

                'submit' => [
                    'type' => 'submit',
                    'callback' => function ($form2) {
                        $form2->saveInSession = true;
                        return true;
                    }
                ],

            ]);

            $status2 = $form2->check();
            if ($status2 === true) {

                $acronym = $_SESSION['form-save']['acronym']['value'];
                $email = $_SESSION['form-save']['email']['value'];
                $name = $_SESSION['form-save']['name']['value'];

                $now = date("Y-m-d H:i:s");
                $user->acronym = $acronym;
                $user->email = $email;
                $user->name = $name;
                $user->updated = $now;
                $user->save();

                unset($_SESSION['form-save']);
                $url = $this->url->create('users/id/' . $id);
                $this->response->redirect($url);
            }

            $this->theme->setTitle("Update user");
            $this->views->add('users/update', [
                'user'  => $user,
                'title' => "Edit user",
                'content' => $form2->getHTML(),
            ]);

        } else {

            // Form
            $form = $this->form;
            $form = $form->create([], [
                'id' => [
                'type'        => 'text',
                'label'       => 'Username or ID:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
                'submit' => [
                    'type'      => 'submit',
                    'callback'  => function($form) {
                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            // Check the status of the form
            $status = $form->check();
            if ($status === true) {
                $id = $_SESSION['form-save']['id']['value'];
                session_unset($_SESSION['form-save']['id']['value']);
                $url = $this->url->create('users/update/' . $id);
                $this->response->redirect($url);
            }

            $this->theme->setTitle("Update user");
            $this->views->add('users/update', [
                'title' => "Edit user",
                'content' => $form->getHTML(),
            ]);
        }
    }

    /**
     * Save user info.
     *
     * @return void
     */
    public function saveAction()
    {
        $this->initialize();

        if (!$this->auth->isAdmin()) {
            $this->flashy->add('warning', 'You do not have permission to this page!');
            $url = $this->url->create('');
            $this->response->redirect($url);
            exit();
        }

        $isPosted = $this->request->getPost('doSave');

        if (!$isPosted) {
            $this->response->redirect($this->request->getPost('redirect'));
        }

        $id = $this->request->getPost('id');
        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');

        $user = $this->users->find($id);

        $now = date("Y-m-d H:i:s");

        $user->email = $email;
        $user->name = $name;
        $user->updated = $now;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }


    /**
     * Setup user db.
     *
     */
    public function setupAction() {

        $this->initialize();

        $this->theme->setTitle('Setup');

        $this->db->dropTableIfExists('user')->execute();

        $this->db->createTable(
            'user',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'acronym' => ['varchar(20)', 'unique', 'not null'],
                'email' => ['varchar(80)'],
                'name' => ['varchar(80)'],
                'password' => ['varchar(255)'],
                'permission' => ['integer', 'default "1"'],
                'score' => ['integer', 'default "0"'],
                'q_votes' => ['text'],
                'a_votes' => ['text'],
                'created' => ['datetime'],
                'updated' => ['datetime'],
                'deleted' => ['datetime'],
                'active' => ['datetime'],
            ]
        )->execute();

        $now = date("Y-m-d H:i:s");

        $this->users->save([
            'acronym' => 'admin',
            'email' => 'admin@qwst.com',
            'name' => 'Administrator',
            'password' => password_hash("admin123", PASSWORD_DEFAULT),
            'permission' => '3',
            'created' => $now,
            'active' => $now,
        ]);

        $this->users->save([
            'acronym' => 'kalkih',
            'email' => 'kalle_kihlstrom@hotmail.com',
            'name' => 'Kalle Kihlström',
            'password' => password_hash("kalkih", PASSWORD_DEFAULT),
            'permission' => '3',
            'created' => $now,
            'active' => $now,
        ]);

        $this->users->save([
            'acronym' => 'user',
            'email' => 'user@qwst.com',
            'name' => 'User',
            'password' => password_hash("user123", PASSWORD_DEFAULT),
            'permission' => '1',
            'created' => $now,
            'active' => $now,
        ]);

        $this->users->save([
            'acronym' => 'moderator',
            'email' => 'moderator@qwst.com',
            'name' => 'Moderator',
            'password' => password_hash("moderator123", PASSWORD_DEFAULT),
            'permission' => '2',
            'created' => $now,
            'active' => $now,
        ]);

        $this->views->addString('<h1>User database was successfully setup!</h1>', 'main');
    }
}
