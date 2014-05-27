<h2><a href="<?=$this->url->create('users')?>"><i class="fa fa-chevron-left hoverDark"></i></a> Users</h2>

<? if (isset($user) && is_object($user)): ?>
    <?php $gravatar = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '.jpg?s=40'; ?>
    <h1>
        <img class="gravatar" style='vertical-align: top; margin-right: 10px;' src='<?= $gravatar ?>' alt="Avatar"/>
        <?=$title?>

        <?php
            if ($this->auth->isAuthenticated()) {
                if ($this->auth->username() === $user->acronym) {
                    echo "<a class='a-alt' href='" . $this->url->create('users/edit') . '/' . $user->acronym .  "'><i class='fa fa-pencil'></i></a>";
                }
            }
        ?>

    </h1>

    <h2>Latest Questions</h2>
    <?php if(!empty($questions)) : ?>
        <?php foreach($questions as $question): ?>
            <h4 style="margin: .4em 0; ">
                <a class="hoverDark" href="<?=$this->url->create('questions/title/' . $question->id . '/' . $question->slug)?>">
                     <span><?=$question->title?></span>
                </a>
                - <span class="about"><?=$this->time->getRelativeTime($question->created)?></span>
            </h4>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Nothing yet...</p>
    <?php endif; ?>
    <h2>Latest Answers</h2>
    <?php if(!empty($answers)) : ?>
        <?php foreach($answers as $answer): ?>
            <h4 style="margin: .4em 0; ">
                <a class="hoverDark" href="<?=$this->url->create('questions/title/' . $answer->q_id . '/' . $answer->q_slug . '/#section' . $answer->id)?>">
                     <span><?=(strlen($answer->content) > 53) ? substr($answer->content,0,50).'...' : $answer->content?></span>
                </a>
                - <span class="about"><?=$this->time->getRelativeTime($answer->created)?></span>
            </h4>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Nothing yet...</p>
    <?php endif; ?>
    <h2>Latest Comments</h2>
    <?php if(!empty($comments)) : ?>
        <?php foreach($comments as $comment): ?>
            <h4 style="margin: .4em 0; ">
                <a class="hoverDark" href="<?=$this->url->create('questions/title/' . $comment->q_id . '/' . $comment->q_slug)?>">
                     <span><?=(strlen($comment->content) > 53) ? substr($comment->content,0,50).'...' : $comment->content?></span>
                </a>
                - <span class="about"><?=$this->time->getRelativeTime($comment->created)?></span>
            </h4>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Nothing yet...</p>
    <?php endif; ?>

<? else : ?>
    <p>User not found!</p>
<? endif; ?>
