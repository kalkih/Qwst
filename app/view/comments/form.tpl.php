<?php

    $form = $form->create([], [
        'id' => [
            'type'        => 'hidden',
            'value'       => isset($id) ? $id : null,
            'required'    => false,
        ],
        'url' => [
            'type'        => 'hidden',
            'value'       => $this->request->getCurrentUrl(),
            'required'    => true,
        ],
        'name' => [
            'type'        => 'text',
            'label'       => 'Name:',
            'required'    => true,
            'value'       => $name,
            'validation'  => ['not_empty'],
        ],
        'content' => [
            'type'        => 'textarea',
            'label'       => 'Comment:',
            'required'    => true,
            'value'       => $content,
            'validation'  => ['not_empty'],
        ],
        'email' => [
            'type'        => 'email',
            'label'       => 'Email:',
            'required'    => true,
            'value'       => $email,
            'validation'  => ['email_adress'],
        ],
        'web' => [
            'type'        => 'url',
            'label'       => 'Website:',
            'required'    => false,
            'value'       => $web,
        ],
        'submit' => [
            'type'      => 'submit',
            'callback'  => function($form) {
                $form->saveInSession = true;
                return true;
            }
        ],
        'reset' => [
            'type'      => 'reset',
            'callback'  => function($form) {
                $form->saveInSession = false;
                return true;
            }
        ],
    ]);

    // Check the status of the form
    $status = $form->check();

    if ($status === true) {

        // What to do if the form was submitted?
        $name = $_SESSION['form-save']['name']['value'];
        $content = $_SESSION['form-save']['content']['value'];
        $email = $_SESSION['form-save']['email']['value'];
        $web = $_SESSION['form-save']['web']['value'];
        $url = $_SESSION['form-save']['url']['value'];
        $id = $_SESSION['form-save']['id']['value'];

        session_unset($_SESSION['form-save']);

        if (isset($id)) {
            $this->dispatcher->forward([
                'controller' => 'comments',
                'action'     => $method,
                'params'     => [$name, $content, $email, $web, $id],
            ]);
        } else {
            $this->dispatcher->forward([
                'controller' => 'comments',
                'action'     => $method,
                'params'     => [$name, $content, $email, $web, $url],
            ]);
        }
    }

?>

<?=$form->getHTML()?>
