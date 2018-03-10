<?php
$this->layout('layout');
$timeago = new Westsworld\TimeAgo();
?>

<nav class="menu">
    <a href="./" class="menu-logo"><strong>José</strong></a>

    <ul class="menu-categories">
        <?php foreach ($categories as $cat): ?>
        <li>
            <a href="?category=<?= $cat->id ?>"
               class="<?= $category && $category === $cat->id ? 'is-selected' : '' ?>">
               <?= $cat->title ?>
            </a>
        </li>
        <?php endforeach ?>

        <li>
            <a href="?saved=1" 
               class="<?= empty($saved) ? '' : 'is-selected' ?>">
                Saved
            </a>
        </li>
    </ul>
</nav>

<form method="post" class="refresh" id="refresh-form">
    <button type="submit" class="button">Refresh</button>
</form>

<?php if (count($entries)): ?>
<ul class="entries">
    <?php foreach ($entries as $entry): ?>
    <li>
        <?php $this->insert('entry', ['entry' => $entry, 'timeago' => $timeago]) ?>
    </li>
    <?php endforeach ?>
</ul>

<nav class="pagination">
	<?php if (count($entries) === 50): ?>
	<a href="?<?php
        echo http_build_query([
            'page' => $page + 1,
            'category' => $category,
            'saved' => $saved,
            'feed' => $feed
        ]);
    ?>" class="button">Next page (<?= $page + 1 ?>)</a>
	<?php endif ?>
</nav>

<?php else: ?>
<p class="emptyState">
    No results
</p>
<?php endif ?>