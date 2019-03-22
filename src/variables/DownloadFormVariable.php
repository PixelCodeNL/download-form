<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\variables;

use craft\records\Entry;
use craft\web\View;
use pixelcode\downloadform\DownloadForm;

use Craft;
use pixelcode\downloadform\models\Request;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DownloadFormVariable
{
    /**
     * Render the download form
     *
     * @param Entry $entry
     * @param array $options
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function render($entry, $options = [])
    {
        $oldPath = Craft::$app->getView()->getTemplatesPath();
        $isMailChimpAvailable = DownloadForm::getInstance()->downloadFormService->isMailChimpAvailable();
        $fieldHandle = array_key_exists('fieldHandle', $options) ? $options['fieldHandle'] : 'downloadForm';
        $template = array_key_exists('template', $options) ? $options['template'] : $oldPath . 'downloadform/downloadForm.twig';

        $variables = [
            'isMailChimpAvailable' => $isMailChimpAvailable,
            'entry' => $entry,
            'fieldHandle' => $fieldHandle,
            'settings' => DownloadForm::getInstance()->getSettings(),
        ];

        if (!Craft::$app->getView()->doesTemplateExist($template)) {
            $html = Craft::$app->getView()->render('downloadForm', $variables);

            // Reset templates path
            Craft::$app->getView()->setTemplatesPath($oldPath);
        } else {

            # Set the template context to the site mode.
            Craft::$app->getView()->setTemplateMode(View::TEMPLATE_MODE_SITE);

            # Now using the renderTemplate() method, it will work.
            $html = Craft::$app->getView()->renderTemplate($template, $variables);
        }

        return $html;
    }

    /**
     * @return array
     */
    public function getSettingsSubNavigation()
    {
        return
        [
            ''                     => ['title' => DownloadForm::t('General Settings')],
            'add-demo-template'    => ['title' => DownloadForm::t('Demo templates')]
        ];
    }

    /**
     * @return array
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    public function getAllAssetSourcesList()
    {
        $assetSources = [
            0 => Craft::$app->getView()->renderString('{{ "- Select an asset source -" | t("download-form") }}')
        ];

        foreach (Craft::$app->volumes->getAllVolumes() as $volume) {
            $assetSources[$volume->id] = $volume->name;
        }

        return $assetSources;
    }

    /**
     * @return mixed
     */
    public function getDownloadAssetSource()
    {
        return DownloadForm::getInstance()->downloadFormService->getDownloadAssetSource();
    }

    /**
     * @param int|string $page
     * @param int $limit
     * @return Request[]
     */
    public function downloadRequests($page = 1, $limit = 30)
    {
        return DownloadForm::getInstance()->downloadFormService->getDownloadRequests($page, $limit);
    }

    /**
     * @param int $limit
     * @return int
     */
    public function totalDownloadRequestPages($limit = 30)
    {
        return DownloadForm::getInstance()->downloadFormService->getTotalDownloadRequestPages($limit);
    }
}
