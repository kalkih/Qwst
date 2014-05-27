<a href="<?=$this->url->create('questions')?>"><h1 class="left" style="margin-bottom: .2em;"><?=$title?></h1></a>
<div class="clear"></div>

<?php if(!empty($tags)) : ?>

    <?php foreach($tags as $tag): ?>
        <p>
            <a  class="tag hoverDark" href="<?=$this->url->create('tags/tag/' . $tag->text)?>">
                #<?=$tag->text?>
            </a><span class='tag-uses'> x <?=$tag->uses?></span>
        </p>
    <?php endforeach; ?>

<?php else : ?>

    <p>Nothing yet...</p>

<?php endif; ?>
