<h2><a href="<?=$this->url->create('users')?>"><?=$title?></a></h2>

<?php if(!empty($users)) : ?>

    <table>
        <tbody>

        <?php foreach ($users as $user) : ?>

            <?php $properties = $user->getProperties(); ?>
            <tr>
                <?php $url = $this->url->create('users/profile/' . $properties['acronym']) ?>
                <td><a href="<?=$url?>"><?=$properties['acronym']?></a></td>
                <td style='text-align: right;'><span class="about"><?=$this->time->getRelativeTime($properties['created'])?></span></td>
            </tr>

        <?php endforeach; ?>

    </tbody>
    </table>

<?php else : ?>

    <p>No users found.</p>

<?php endif; ?>
