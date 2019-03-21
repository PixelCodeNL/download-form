<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\models;

use craft\base\Model;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class Settings extends Model
{
    /**
     * @var string
     */
    public $downloadAssetSource = '';
    public $notificationEmail = '';
    public $mailChimpList = '';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['downloadAssetSource', 'number'],
            ['notificationEmail', 'string'],
            ['mailChimpList', 'string']
        ];
    }
}