<h1>About</h1>

<? if (isset($user) && is_object($user)): ?>

    <div class="about-sidebar">
        <h2 >Username</h2>
        <p><?=$user->acronym?></p>
        <h2>Name</h2>
        <p><?=$user->name?></p>
        <h2>Email</h2>
        <p><?=$user->email?></p>
        <h2>Reputation</h2>
        <p>
        <?php if ($user->rep < -99): ?>
            <span class="red bold"> Scam user: </span>
        <?php elseif ($user->rep < -9): ?>
            <span class="red bold"> Going down: </span>
        <?php elseif ($user->rep < 10): ?>
            <span class="bold"> Newbie: </span>
        <?php elseif ($user->rep < 25): ?>
            <span class="grn bold"> On the way up: </span>
        <?php elseif ($user->rep < 100): ?>
            <span class="grn bold">Helpful user: </span>
        <?php elseif ($user->rep < 1000): ?>
            <span class="grn bold">Über user: </span>
        <?php elseif ($user->rep < 10000): ?>
            <span class="grn bold">One of a kind: </span>
        <?php endif ?>
        <span> <?=$user->rep?> points / <?=$user->score?> posts</span>
        <h2>Registered</h2>
        <p><?=$this->time->getRelativeTime($user->created)?></p>
        <h2>User level</h2>
        <p><?=$user->permissionToString()?></p>
        </p>
    </div>

<? else : ?>
<? endif; ?>
