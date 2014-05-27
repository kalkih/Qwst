<?php $url = $this->url->create('questions/new'); ?>

<a href="<?=$this->url->create('questions')?>"><h1 class="left" style="display: inline;"><?=$title?></h1></a>
<a href='<?=$url?>' style="margin-top: 25px;" class='ask-button right bold'><span><i class="fa fa-plus"></i> New</span></a>
<div class="clear"></div>

<?php if(!empty($questions)) : ?>

    <table class="question">
        </tbody>
        <tr class="spacer"></tr>
        <?php foreach ($questions as $question) : ?>
            <?php $url = $this->url->create('questions/title/' . $question->q_id .'/' . $question->slug)?>
            <tr class='hover' onclick="document.location = '<?=$url?>'">
                <td style="width: 100%; display: block;">
                    <h2><?=(strlen($question->title) > 20) ? substr($question->title,0,17).'...' : $question->title?></h2>
                    <p class="question-content"><?=(strlen($question->content) > 178) ? substr($question->content,0,175).'...' : $question->content?></p>
                    <p class="question-about">
                        <a class="hoverDark" href="<?=$this->url->create('users/profile/' . $question->acronym) ?>"><?=$question->acronym?></a> |
                        <span><?=$this->time->getRelativeTime($question->created)?></span>
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
