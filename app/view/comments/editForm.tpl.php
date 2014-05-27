
<h2>Edit comment</h2>
<div class='comment-form'>
    <form class="form" method=post>
        <legend>Edit comment</legend>
        <input type=hidden name="redirect" value="<?= $_SERVER['HTTP_REFERER'] ?>">
        <input type=hidden name="id" value="<?= $id ?>">
        <p><label>Name:<br/><input type='text' name='name' value='<?=$name?>'/></label></p>
        <p><label>Comment:<br/><textarea name='content'><?=$content?></textarea></label></p>
        <p><label>Email:<br/><input type='email' name='mail' value='<?=$mail?>'/></label></p>
        <p><label>Website:<br/><input type='text' name='web' value='<?=$web?>'/></label></p>
        <div>
            <input class='post-button-text' type='submit' name='doSave' value='Save' onClick="this.form.action = '<?=$this->url->create('comment/save')?>'"/>
            <input class='post-button-text' type='reset' value='Reset'/>
        </div>
    </form>
</div>
