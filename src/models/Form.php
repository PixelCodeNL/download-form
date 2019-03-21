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
class Form extends Model
{
    /**
     * @var string
     */
    public $enabled = false;
    public $mailChimp = false;
    public $mailChimpList = '';
    public $title = '';
    public $intro = '';
    public $file = '';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['enabled', 'bool'],
            ['mailChimp', 'bool'],
            ['mailChimpList', 'string'],
            ['title', 'string'],
            ['intro', 'text'],
            ['file', 'string'],
        ];
    }
}
