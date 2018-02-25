<?php $this->layout('layout') ?>

<form method="post" class="refresh">
    <button type="submit">Refresh</button>
</form>

<?php if (count($entries)): ?>
<ul class="entries">
    <?php foreach ($entries as $entry): ?>
    <li>
        <?php $this->insert('partials/entry-list', ['entry' => $entry]) ?>
    </li>
    <?php endforeach ?>
</ul>
<?php else: ?>
<p class="emptyState">
    No results
</p>
<?php endif ?>