<?php if(!empty($contents)) : ?>

    <?php foreach($contents as $content): ?>
        <a href="<?=$this->url->create('question/title/' . $content->id . '/' . $content->slug)?>">
            <h4 class="hoverDark"><?=$content->title?></h4>
        </a>
    <?php endforeach; ?>

<?php else : ?>

    <p>Nothing yet...</p>

<?php endif; ?>
