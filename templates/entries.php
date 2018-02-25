<?php $this->layout('layout') ?>

<form method="post" class="refresh">
    <button type="submit" class="button">Refresh</button>
</form>

<?php if (count($entries)): ?>
<ul class="entries">
    <?php foreach ($entries as $entry): ?>
    <li>
        <?php $this->insert('entry', ['entry' => $entry]) ?>
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