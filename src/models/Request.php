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
class Request extends Model
{
    /**
     * @var string
     */
    public $name = '';
    public $email = '';
    public $entry = '';
    public $pageUrl = '';
    public $mailChimpSubscribe = false;
    public $mailChimpList = '';
    public $sessionId = '';
    public $file = '';
    public $ip = '';
    public $date = '';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name'                  => ['string']],
            ['email'                 => ['string']],
            ['entry'                 => ['number']],
            ['pageUrl'               => ['string']],
            ['mailChimpSubscribe'    => ['boolean']],
            ['mailChimpList'         => ['string']],
            ['sessionId'             => ['string']],
            ['file'                  => ['string']],
            ['ip'                    => ['string']],
            ['date'                  => ['datetime']]
        ];
    }
}
