<article class="entry">
    <form action="?path=/save" method="post" class="entry-save<?= $entry->isSaved ? ' is-saved' : '' ?>">
        <input type="hidden" name="id" value="<?= $entry->id ?>">
        <button type="submit" class="entry-save-button">Save</button>
    </form>

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
            <a href="<?= $entry->feed->url ?>" target="_blank">
                <?= $entry->feed->title ?>
            </a>
            <time><?= $timeago->inWords($entry->publishedAt->format('Y-m-d H:i:s')) ?></time>
        </p>
    </header>


    <p class="entry-description">
        <?= $entry->description ?>
    </p>

    <?php if (!empty($entry->body)): ?>
    <details class="entry-body">
        <summary>See body</summary>

        <template>
            <?= $entry->body ?>
        </template>
    </details>
    <?php endif ?>
</article>