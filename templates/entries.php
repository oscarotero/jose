<?php
$this->layout('layout');
$timeago = new Westsworld\TimeAgo();
?>

<nav class="menu">
    <a href="./" class="menu-logo"><strong>José</strong></a>

    <?php if (empty($saved)): ?>
    <a href="?saved=1">Show saved</a>
    <?php else: ?>
    <a href="./">Restore</a>
    <?php endif ?>

    <form method="post" class="refresh">
        <button type="submit" class="button">Refresh</button>
    </form>
</nav>

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
	<a href="?page=<?= $page + 1 ?>" class="button">Next page (<?= $page + 1 ?>)</a>
	<?php endif ?>
</nav>

<?php else: ?>
<p class="emptyState">
    No results
</p>
<?php endif ?>