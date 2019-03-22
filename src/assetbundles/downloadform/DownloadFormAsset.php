<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\assetbundles\DownloadForm;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DownloadFormAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@pixelcode/downloadform/assetbundles/downloadform/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/DownloadForm.js',
        ];

        $this->css = [
            'css/DownloadForm.css',
        ];

        parent::init();
    }
}
