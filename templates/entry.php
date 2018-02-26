<article class="entry">
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
            <time><?= $entry->publishedAt->format('Y-m-d H:i:s') ?></time>
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