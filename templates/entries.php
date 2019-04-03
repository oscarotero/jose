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

    <form class="menu-search">
        <input type="search" name="q" placeholder="Search in title">
        <button type="submit" class="button">Search</button>
    </form>
</nav>

<form method="post" class="refresh" id="refresh-form">
    <button type="submit" class="button">Refresh</button>
</form>

<?php if (count($entries)): ?>
<ul class="entries">
    <?php foreach ($entries as $entry): ?>
    <li>
        <?php $this->insert('entry', compact('entry', 'timeago')) ?>
    </li>
    <?php endforeach ?>
</ul>

<?php $page = $entries->page ?>
<nav class="pagination">
<?php if ($page['previousPage']): ?>
	<a href="?<?php
        echo http_build_query([
            'page' => $page['previousPage'],
            'category' => $category,
            'saved' => $saved,
            'feed' => $feed
        ]);
    ?>" class="button">Previous page (<?= $page['previousPage'] ?>)</a>
<?php endif ?>
<?php if ($page['nextPage']): ?>
	<a href="?<?php
        echo http_build_query([
            'page' => $page['nextPage'],
            'category' => $category,
            'saved' => $saved,
            'feed' => $feed
        ]);
    ?>" class="button">Next page (<?= $page['nextPage'] ?>)</a>
<?php endif ?>
</nav>

<a href="#top" class="float-button">
    <em>Top</em>
</a>

<?php else: ?>
<p class="emptyState">
    No results
</p>
<?php endif ?>