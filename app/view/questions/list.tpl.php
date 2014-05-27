<?php $url = $this->url->create('questions/new'); ?>

<a href="<?=$this->url->create('questions')?>"><h1 class="left" style="display: inline; margin-bottom: .2em;"><?=$title?></h1></a>
<a href='<?=$url?>' style="margin-top: 25px;" class='ask-button right bold'><span><i class="fa fa-plus"></i> New</span></a>
<div class="clear"></div>

<?php if(!empty($questions)) : ?>

    <table class="question list">
        </tbody>
        <tr class="spacer"></tr>
        <?php foreach ($questions as $question) : ?>
            <?php $url = $this->url->create('questions/title/' . $question->q_id .'/' . $question->slug)?>
                <tr class='hover' onclick="document.location = '<?=$url?>'">
                <td style="width: 100%; display: block;">
                    <h2><?=$question->title?></h2>
                    <p class="question-content"><?=(strlen($question->content) > 178) ? substr($question->content,0,175).'...' : $question->content?></p>
                    <p class="question-about">
                        <img class="gravatar" style='vertical-align: text-top;' src='<?='http://www.gravatar.com/avatar/' . md5(strtolower(trim($question->email))) . '.jpg?s=15'?>' alt="Avatar"/>
                        <a class="hoverDark" href="<?=$this->url->create('users/profile/' . $question->acronym) ?>"><?=$question->acronym?></a>
                        <span> | <?=$this->time->getRelativeTime($question->created)?> | <?=$question->count?>
                            <?php if ($question->count == 1): ?>
                                answer
                            <?php else: ?>
                                answers
                            <?php endif ?> |
                        </span>
                        <?php if ($question->q_score > 0): ?>
                             <span class="grn"><?=$question->q_score?> points</span>
                        <?php elseif ($question->q_score < 0): ?>
                            <span class="red"><?=$question->q_score?> points</span>
                        <?php else: ?>
                            <span><?=$question->q_score?> points</span>
                        <?php endif ?>
                    </p>
                    <?php if (isset($question->tags)): ?>
                        <p class='question-tags'>
                            <?php $tags = unserialize($question->tags) ?>
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?=$this->url->create('tags/tag/' . $tag)?>">#<?=$tag?></a>
                            <?php endforeach ?>
                        </p>
                    <?php endif ?>
                </td>
            </tr>
            <tr class="spacer"></tr>
        <?php endforeach; ?>

        <tbody>
    </table>

<?php else : ?>

    <p>No questions found.</p>

<?php endif; ?>
