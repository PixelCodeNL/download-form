<?php

namespace pixelcode\downloadform\migrations;

use Craft;
use craft\db\Migration;

/**
 * m220726_082851_add_pageurl_field migration.
 */
class m220726_082851_add_pageurl_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp() : bool
    {
        $tableName = '{{%downloadform_download_request}}';

        if (!$this->db->columnExists($tableName, 'pageUrl')) {
            $this->addColumn(
                $tableName,
                'pageUrl',
                $this->string(255)->after('entry')->notNull()
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220726_082851_add_pageurl_field cannot be reverted.\n";
        return false;
    }
}
