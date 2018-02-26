<?php

use Phinx\Seed\AbstractSeed;
use Symfony\Component\Yaml\Yaml;

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
        $data = Yaml::parseFile(__DIR__.'/../../subscriptions.yaml');

        $this->table('feed')
            ->insert($data)
            ->save();
    }
}
