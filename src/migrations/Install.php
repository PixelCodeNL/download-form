<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\migrations;

use Craft;
use craft\db\Migration;
use pixelcode\downloadform\records\DownloadFormRecord;
use Solspace\Commons\Migrations\ForeignKey;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class Install extends Migration
{
    /**
     * @var string The database driver to use
     */
    public $driver;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(DownloadFormRecord::$tableName);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                DownloadFormRecord::$tableName,
                [
                    'id' => $this->primaryKey(),
                    'name' => $this->string(255)->notNull(),
                    'email' => $this->string(255)->notNull(),
                    'pageUrl' => $this->string(255)->notNull(),
                    'entry' => $this->integer(255)->null(),
                    'mailChimpSubscribe' => $this->boolean(),
                    'mailChimpList' => $this->string(255)->null(),
                    'thanksUrl' => $this->string(255)->null(),
                    'sessionId' => $this->string(255)->notNull(),
                    'file' => $this->string(255)->notNull(),
                    'ip' => $this->string(255)->notNull(),
                    'date' => $this->dateTime()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'siteId' => $this->integer()->null(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName(DownloadFormRecord::$tableName, 'siteId'),
            DownloadFormRecord::$tableName,
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(DownloadFormRecord::$tableName, 'entry'),
            DownloadFormRecord::$tableName,
            'entry',
            '{{%entries}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists(DownloadFormRecord::$tableName);
    }
}
