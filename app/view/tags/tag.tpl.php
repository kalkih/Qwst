<h2><a href="<?=$this->url->create('tags')?>"><i class="fa fa-chevron-left hoverDark"></i></a> Tags</h2>
<h1><?=$title?></h1>
<?php if(!empty($questions)) : ?>

    <table class="question list">
        </tbody>
        <tr class="spacer"></tr>
        <?php foreach ($questions as $question) : ?>
            <?php $url = $this->url->create('questions/title/' . $question->q_id .'/' . $question->slug)?>
            <?php if ($question->score < -10000): ?>
                <tr class='red-blink hover' onclick="document.location = '<?=$url?>'">
            <?php else: ?>
                <tr class='hover' onclick="document.location = '<?=$url?>'">
            <?php endif; ?>
                <td style="width: 100%; display: block;">
                    <h2><?=$question->title?></h2>
                    <p class="question-content"><?=(strlen($question->content) > 178) ? substr($question->content,0,175).'...' : $question->content?></p>
                    <p class="question-about">
                        <img class="gravatar" style='vertical-align: text-top;' src='<?='http://www.gravatar.com/avatar/' . md5(strtolower(trim($question->email))) . '.jpg?s=15'?>' alt="Avatar"/>
                        <a class="hoverDark" href="<?=$this->url->create('users/profile/' . $question->acronym) ?>"><?=$question->acronym?></a> |
                        <span><?=$this->time->getRelativeTime($question->created)?></span> |
                        <?php if ($question->score > 0): ?>
                             <span class="grn"><?=$question->score?> points</span>
                        <?php elseif ($question->score < 0): ?>
                            <span class="red"><?=$question->score?> points</span>
                        <?php else: ?>
                            <span class=""><?=$question->score?> points</span>
                        <?php endif ?>
                    </p>
                    <p class='question-tags'>
                        <?php if (isset($question->tags)): ?>
                            <?php $tags = unserialize($question->tags) ?>
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?=$this->url->create('tags/tag/' . $tag)?>">#<?=$tag?></a>
                            <?php endforeach ?>
                        <?php endif ?>
                    </p>
                </td>
            </tr>
            <tr class="spacer"></tr>
        <?php endforeach; ?>

        <tbody>
    </table>

<?php else : ?>

    <p>No questions found.</p>

<?php endif; ?>
