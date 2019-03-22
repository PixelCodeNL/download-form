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

use craft\helpers\FileHelper;
use pixelcode\downloadform\DownloadForm;

use Craft;
use craft\web\Controller;
use yii\web\Response;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class SettingsController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $allowAnonymous = ['index'];

    /**
     * @param string $subSection
     * @return Response
     */
    public function actionIndex()
    {
        $variables = [];
        $variables['settings'] = DownloadForm::$plugin->getSettings();

        return $this->renderTemplate('download-form/settings/index', $variables);
    }

    /**
     * Attempt cloning a demo template into the user's specified template directory
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAddDemoTemplate()
    {
        return $this->renderTemplate('download-form/settings/add-demo-template');
    }

    /**
     * Installs the demo templates
     *
     * @return string|Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionAddDemoTemplateFormSubmit()
    {
        $this->requirePostRequest();

        $pluginDemoFolder = 'demo-templates';
        $templateFolder = '_download-form-demo';

        $sourceFolder = DownloadForm::$plugin->getBasePath() . '/' . $pluginDemoFolder;
        $destinationFolder = Craft::$app->path->getSiteTemplatesPath() . '/' . $templateFolder;

        FileHelper::copyDirectory($sourceFolder, $destinationFolder);

        $variables['installed'] = true;

        return $this->renderTemplate('download-form/settings/add-demo-template', $variables);
    }
}
