<?php


use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('feed')
            ->addColumn('title', 'string', ['null' => true])
            ->addColumn('url', 'string', ['null' => true])
            ->addColumn('feed', 'string')
            ->addColumn('lastCheckAt', 'timestamp', ['null' => true])
            ->addIndex(['feed'], ['unique' => true])
            ->create();

        $this->table('entry')
            ->addColumn('title', 'string')
            ->addColumn('url', 'string')
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('body', 'text', ['null' => true])
            ->addColumn('publishedAt', 'timestamp', ['null' => true])
            ->addColumn('feed_id', 'integer', ['null' => false])
            ->addIndex(['url'], ['unique' => true])
            ->addForeignKey('feed_id', 'feed', 'id')
            ->create();
    }
}
