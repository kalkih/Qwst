
<?php $time = new \kalkih\Time\CTime(); ?>

<div class='article1'>
    <h2>Comments <?php if(!empty($comments)) : ?> - <a href="<?=$this->url->create('comments/delete-all')?>" title="Remove all">Remove all</a> <?php endif ;?> </h2>
    <div class='comments'>
        <?php foreach ($comments as $comment) : ?>

            <?php $gravatar = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($comment->email))) . '.jpg?s=60'; ?>

            <table class="comment">
                <tr>
                    <td class="post-avatar">
                        <img class="gravatar" src='<?= $gravatar ?>' alt="Avatar"/>
                    </td>

                    <td class='post-body'>
                        <div class="post-header">
                            <a class="post-name"><?=$comment->name?></a> -
                            <span class="post-time"><?=$comment->created?></span>
                        </div>

                        <p class="post-content"><?=$comment->content?></p>

                        <div class="post-footer">
                            <form method=post>
                                <div class="post-menu">
                                    <span class="post-mail"><?=$comment->email?></span><br/>
                                    <?php if (!empty($comment->web)) : ?>
                                        <a class="post-web" href='<?=$comment->web?>'><?php echo substr($comment->web, 0, 20); ?>...</a>
                                    <?php endif; ?>
                                    <input type=hidden name="redirect" value="<?=$this->request->getCurrentUrl()?>">
                                    <input type=hidden name="id" value="<?=$comment->id?>">
                                    <div class="divider"></div>
                                    <span>
                                        <input type='submit' name ='doEditPost' value='edit' onclick="this.form.action = '<?= $this->url->create('comments/update/' . $comment->id) ?>'">
                                        <input type='submit' name='doRemovePost' value='delete' onclick="this.form.action = '<?= $this->url->create('comments/delete/' . $comment->id) ?>'">
                                    </span>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            </table>
            
        <?php endforeach; ?>
    </div>
</div>