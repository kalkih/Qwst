<h1><?=$title?></h1>


<? if (isset($user) && is_object($user)): ?>

        <?php $properties = $user->getProperties(); ?>
        <p> <?=$content?> </p>
        

<? else : ?>

    <p> <?=$content?> </p>

<? endif; ?>
 
<p><a href='<?=$this->url->create('users/update')?>'><i class="fa fa-arrow-left"></i> Back</a></p>