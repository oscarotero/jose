<article class="entry">
    <div class="entry-actions">
        <form action="?path=/save" method="post" class="entry-save">
            <input type="hidden" name="id" value="<?= $entry->id ?>">
            <button type="submit" class="entry-button<?= $entry->isSaved ? ' is-active' : '' ?>">
                <?= $entry->isSaved ? 'Unsave' : 'Save' ?>
            </button>
        </form>

        <form action="?path=/hide" method="post" class="entry-hide">
            <input type="hidden" name="id" value="<?= $entry->id ?>">
            <button type="submit" class="entry-button">
                <?= $entry->isHidden ? 'Show' : 'Hide' ?>
            </button>
        </form>
    </div>

    <?php if (!empty($entry->image)): ?>
    <img src="<?= $entry->image->data ?>" class="entry-image" width="100" height="100">
    <?php endif ?>

    <header class="entry-header">
        <h1>
            <a href="<?= $entry->url ?>" target="_blank">
                <?= $entry->title ?>
            </a>
        </h1>

        <p class="entry-info">
            <a href="?feed=<?= $entry->feed->id ?>">
                <?= $entry->feed->title ?>
            </a>

            <time><?= $timeago->inWords($entry->publishedAt) ?></time>

            <?php if (!empty($entry->feed->category)): ?>
            <a href="?category=<?= $entry->feed->category->id ?>">
                <?= $entry->feed->category->title ?>
            </a>
            <?php endif ?>
        </p>
    </header>

    <?php if ($entry->description): ?>
    <p class="entry-description">
        <?= $entry->description ?>
    </p>
    <?php endif ?>

    <?php if (!empty($entry->body)): ?>
    <details class="entry-body">
        <summary>See body</summary>

        <div>
            <?= $entry->body ?>
        </div>
    </details>
    <?php endif ?>
</article>