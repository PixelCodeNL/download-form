<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\records;

use craft\fields\Date;
use craft\db\ActiveRecord;

/**
 * Class DownloadFormRecord
 *
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $entry
 * @property boolean $mailChimpSubscribe
 * @property string|null $mailChimpList
 * @property string $sessionId
 * @property string $file
 * @property string $ip
 * @property Date $date
 */

class DownloadFormRecord extends ActiveRecord
{
    public static $tableName = '{{%downloadform_download_request}}';

    public $name = '';
    public $email = '';
    public $entry = '';
    public $pageUrl = '';
    public $mailChimpSubscribe = false;
    public $mailChimpList = '';
    public $sessionId = '';
    public $thanksUrl = '';
    public $file = '';
    public $ip = '';
    public $date = '';



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return self::$tableName;
    }
}
