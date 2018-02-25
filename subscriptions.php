<?php

return [
    [
        'feed' => 'http://feeds.feedburner.com/CssTricks',
        'contentSelector' => '.article-content',
        'ignoredSelector' => '.jp-relatedposts'
    ],
    [
        'feed' => 'https://www.smashingmagazine.com/feed/',
        'contentSelector' => '#article__content',
        'ignoredSelector' => '.product-panel,.signature'
    ],
    [
        'feed' => 'https://inclusive-components.design/rss/',
        'contentSelector' => '#main',
    ],
    [
        'feed' => 'http://marcaporhombro.com/feed/',
        'contentSelector' => '.entry-content',
        'ignoredSelector' => '.jp-relatedposts,.sharedaddy'
    ],
    [
        'feed' => 'http://www.brandemia.org/feed/',
        'contentSelector' => '.node-article',
        'ignoredSelector' => '.view-profile'
    ],
    [
        'feed' => 'https://escss.blogspot.com/feeds/posts/default?alt=rss',
        'contentSelector' => '.post-body',
        'ignoredSelector' => '.autor-invitado,#promote,.post-datos,.autor-post'
    ],
];
