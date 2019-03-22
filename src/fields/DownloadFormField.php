<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\fields;

use pixelcode\downloadform\DownloadForm;
use pixelcode\downloadform\assetbundles\downloadformfield\DownloadFormFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use pixelcode\downloadform\models\Form;
use yii\base\InvalidConfigException;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DownloadFormField extends Field
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('download-form', 'Download form');
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ($value instanceof Form) {
            return $value;
        }

        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        if ($value instanceof Form) {
            return $value;
        }

        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'download-form/_components/fields/DownloadFormField_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @param Form $value
     * @param ElementInterface|null $element
     *
     * @return string
     * @throws InvalidConfigException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        if (!$element) return '';

        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(DownloadFormFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        $services = DownloadForm::getInstance()->downloadFormService;
        $isMailChimpAvailable = $services->isMailChimpAvailable();
        $downloadAssetSource = $services->getDownloadAssetSource();
        $settings = DownloadForm::getInstance()->getSettings();
        $globalMailChimpListId = $settings['mailChimpList'];

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ];
        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').DownloadFormField(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'download-form/_components/fields/DownloadFormField_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'isMailChimpAvailable' => $isMailChimpAvailable,
                'downloadAssetSource' => $downloadAssetSource,
                'globalMailChimpListId' => $globalMailChimpListId
            ]
        );
    }
}
