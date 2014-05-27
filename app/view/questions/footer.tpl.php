<h2><a href="<?=$this->url->create('questions')?>"><?=$title?></a></h2>

<?php if(!empty($questions)) : ?>

    <table class='question-table'>
        <tbody>

        <?php foreach ($questions as $question) : ?>

            <tr>
                <?php $url = $this->url->create('questions/title/' . $question->q_id . '/' . $question->slug); ?>
                <td>
                    <a href="<?=$url?>"><?=(strlen($question->title) > 33) ? substr($question->title,0,30).'...' : $question->title?></a> <span class="right about"><?=$this->time->getRelativeTime($question->created)?></span><hr>
                    <?=(strlen($question->content) > 73) ? substr($question->content,0,70).'...' : $question->content?>
                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>
    </table>

<?php else : ?>

    <p>No questions found.</p>

<?php endif; ?>
