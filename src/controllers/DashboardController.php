<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DashboardController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $allowAnonymous = ['index'];


    /**
     * @return Response
     */
    public function actionIndex()
    {
        return $this->renderTemplate('download-form/dashboard');
    }
}
