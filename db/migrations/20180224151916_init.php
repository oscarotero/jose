<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

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
        $this->table('feed', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('title', 'string', ['null' => true])
            ->addColumn('url', 'string', ['null' => true])
            ->addColumn('feed', 'string')
            ->addColumn('lastCheckAt', 'timestamp', ['null' => true])
            ->addColumn('contentSelector', 'string', ['null' => true])
            ->addColumn('ignoredSelector', 'string', ['null' => true])
            ->addColumn('isEnabled', 'boolean', ['default' => 1])
            ->addIndex(['feed'], ['unique' => true])
            ->create();

        $this->table('image', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('url', 'string', ['null' => true])
            ->addColumn('data', 'text', ['null' => true])
            ->create();

        $this->table('entry', ['collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('title', 'string')
            ->addColumn('guid', 'string')
            ->addColumn('url', 'string')
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('body', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_MEDIUM])
            ->addColumn('publishedAt', 'timestamp', ['null' => true])
            ->addColumn('isSaved', 'boolean', ['default' => 0])
            ->addColumn('feed_id', 'integer', ['null' => false])
            ->addColumn('image_id', 'integer', ['null' => true])
            ->addIndex(['url'], ['unique' => true])
            ->addIndex(['guid'], ['unique' => true])
            ->addForeignKey('feed_id', 'feed', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('image_id', 'image', 'id', ['delete' => 'SET_NULL'])
            ->create();
    }
}
