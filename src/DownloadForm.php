<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform;

use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use pixelcode\downloadform\controllers\FormController;
use pixelcode\downloadform\models\Settings;
use pixelcode\downloadform\services\DownloadFormMailChimpSubscribeService;
use pixelcode\downloadform\services\DownloadFormNotificationService;
use pixelcode\downloadform\services\DownloadFormService;
use pixelcode\downloadform\variables\DownloadFormVariable;
use pixelcode\downloadform\fields\DownloadFormField;

use Craft;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class DownloadForm
 *
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 *
 * @property  DownloadFormService $downloadFormService
 * @property  DownloadFormNotificationService $downloadFormNotificationService
 * @property  DownloadFormMailChimpSubscribeService $downloadFormMailChimpSubscribeService
 *
 */
class DownloadForm extends Plugin
{
    const TRANSLATION_CATEGORY = 'download-form';
    const LOGGER_CATEGORY = 'download-form';

    /**
     * @var DownloadForm
     */
    public static $plugin;

    /** @var bool */
    public $hasCpSection = true;

    /** @var bool */
    public $hasCpSettings = true;

    /** @var string */
    public $schemaVersion = '3.1.2';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->name = self::t('Download Form');

        $this->initControllerMap();
        $this->initServices();
        $this->initFieldTypes();
        $this->initUrls();
        $this->initUserPermissions();
        $this->initTwigVariable();
    }

    /**
     * @param string $message
     * @param array  $params
     * @param string $language
     *
     * @return string
     */
    public static function t($message, array $params = [], $language = null)
    {
        return Craft::t(self::TRANSLATION_CATEGORY, $message, $params, $language);
    }

    /**
     * @return array|null
     */
    public function getCpNavItem()
    {
        $subNavs = [];
        $currentUser = \Craft::$app->user;
        $navItem = parent::getCpNavItem();

        if ($currentUser->getIsAdmin() || $currentUser->checkPermission('downloadForm:dashboard')) {
            $subNavs['dashboard'] = [
                'label' => self::t('Requests'),
                'url' => 'download-form/dashboard',
            ];
        }

        if ($currentUser->getIsAdmin() || $currentUser->checkPermission('downloadForm:settings')) {
            $subNavs['settings'] = [
                'label' => self::t('Settings'),
                'url' => 'download-form/settings',
            ];
        }

        $navItem = array_merge($navItem, [
            'subnav' => $subNavs,
        ]);

        return $navItem;
    }

    /**
     * @return Settings
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }


    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate(
            'download-form/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    private function initControllerMap()
    {
        if (!\Craft::$app->request->isConsoleRequest) {
            $this->controllerMap = [
                'api'              => FormController::class,
            ];
        }
    }

    private function initServices()
    {
        $this->setComponents([
            'downloadFormService' => DownloadFormService::class,
            'downloadFormNotificationService' => DownloadFormNotificationService::class,
            'downloadFormMailChimpSubscribeService' => DownloadFormMailChimpSubscribeService::class,
        ]);
    }

    private function initFieldTypes()
    {
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = DownloadFormField::class;
            }
        );
    }
    
    private function initUrls()
    {
        // Register site urls
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge(
                    $event->rules,
                    [
                        'download-form/download' => 'download-form/download'
                    ]
                );
            }
        );

        // Register CP urls
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge(
                    $event->rules,
                    [
                        'download-form' => 'download-form/dashboard',
                        'download-form/dashboard' => 'download-form/dashboard',
                        'download-form/settings' => 'download-form/settings'
                    ]
                );
            }
        );
    }
    
    private function initUserPermissions()
    {
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function (RegisterUserPermissionsEvent $event) {
                Craft::debug(
                    'UserPermissions::EVENT_REGISTER_PERMISSIONS',
                    __METHOD__
                );
                $event->permissions[self::t('Download form')] = [
                    'downloadForm:dashboard' => [
                        'label' => self::t('Requests'),
                    ],
                    'downloadForm:settings' => [
                        'label' => self::t('Settings'),
                    ]
                ];
            }
        );
    }

    private function initTwigVariable()
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('downloadForm', DownloadFormVariable::class);
            }
        );
    }
}
