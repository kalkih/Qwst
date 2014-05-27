<p class='sitefooter'>
    <span style='text-align: center;' class='about'>Copyright © Kalle Kihlström</span>
<?php if ($this->auth->isAuthenticated()): ?>
    <?php
        $logout = $this->url->create('users/logout');
        $profile = $this->url->create('users/profile') . '/' . $this->auth->username();
    ?>
    <span class="left"><a href="<?=$logout?>">Logout</a> | <a href="<?=$profile?>">Profile</a></span>
<?php else: ?>
    <?php $register = $this->url->create('users/register'); ?>
    <?php $login = $this->url->create('users/login'); ?>
    <span class="left"> <a href="<?=$login?>">Login</a> | <a href="<?=$register?>">Register</a></span>
<?php endif; ?>
    <?php $about = $this->url->create('about'); ?>
    <?php $source = $this->url->create('source'); ?>
    <span class="right"><a href="<?=$about?>">About</a>
    <?php if ($this->auth->isAuthenticated()): ?>
        | <a href='<?=$source?>'>Source</a></span>
    <?php endif; ?>
    <div class="clear"></div>
</p>
