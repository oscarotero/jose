<?php


use Phinx\Seed\AbstractSeed;

class Feeds extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $this->table('feed')
            ->insert([
                ['feed' => 'http://feeds.feedburner.com/CssTricks'],
                //['feed' => 'https://www.smashingmagazine.com/feed/'],
                //['feed' => 'http://marcaporhombro.com/feed/'],
                //['feed' => 'http://www.brandemia.org/feed/'],
                //['feed' => 'https://inclusive-components.design/rss/'],
                //['feed' => 'https://escss.blogspot.com/feeds/posts/default?alt=rss'],
            ])
            ->save();
    }
}
