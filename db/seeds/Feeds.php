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
        $data = include __DIR__.'/../../subscriptions.php';

        $this->table('feed')
            ->insert($data)
            ->save();
    }
}
