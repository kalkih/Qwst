<?php if ($this->auth->isAuthenticated()): ?>
    <?php $url = $this->url->create('questions/new'); ?>
    <a href='<?=$url?>' class='ask-button right'>
        <span>Ask a question?</span>
    </a>
<?php else: ?>
    <?php $url = $this->url->create('users/login'); ?>
    <a href='<?=$url?>' class='ask-button right'>
        <span>Login now</span>
    </a>
<?php endif; ?>
