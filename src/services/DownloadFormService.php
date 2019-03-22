<?php
/**
 * Download Form plugin for Craft CMS 3.x
 *
 * Show a download form to download a custom file.
 *
 * @link      https://www.pixelcode.nl
 * @copyright Copyright (c) 2019 Pixel&Code
 */

namespace pixelcode\downloadform\services;

use craft\db\Query;
use craft\helpers\UrlHelper;
use pixelcode\downloadform\DownloadForm;

use Craft;
use craft\base\Component;
use pixelcode\downloadform\models\Request;
use pixelcode\downloadform\records\DownloadFormRecord as FormRecord;
use pixelcode\downloadform\records\DownloadFormRecord;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DownloadFormService extends Component
{
    const EVENT_AFTER_SAVE     = 'afterSave';

    /**
     * @return \craft\base\VolumeInterface
     */
    public function getDownloadAssetSource()
    {
        $settings = DownloadForm::getInstance()->getSettings();
        if($settings['downloadAssetSource']) {
            $assetSource = Craft::$app->volumes->getVolumeById($settings['downloadAssetSource']);
        } else {
            $assetSource = null;
        }

        return $assetSource;
    }

    /**
     * @param string $downloadFileUrl
     * @return string
     * @throws \craft\errors\SiteNotFoundException
     */
    public function createDownloadUrl($downloadFileUrl)
    {
        $secretString = base64_encode(sprintf('%s|%s', session_id(), $downloadFileUrl));

        return UrlHelper::baseSiteUrl() . '/download-form/download?key=' . $secretString;
    }

    /**
     * @return bool
     */
    public function isMailChimpAvailable()
    {
        $mailChimpPlugin = Craft::$app->plugins->getPlugin('mailchimp-subscribe');
        $isEnabled = Craft::$app->plugins->isPluginEnabled('mailchimp-subscribe');

        return $mailChimpPlugin !== null && $isEnabled;
    }

    /**
     * @param Request $downloadRequest
     * @return bool
     * @throws \Exception
     */
    public function saveDownloadRequest(Request $downloadRequest)
    {
        $record = new FormRecord();
        $record->setAttribute('name', $downloadRequest->name);
        $record->setAttribute('email', $downloadRequest->email);
        $record->setAttribute('pageUrl', $downloadRequest->pageUrl);
        $record->setAttribute('entry', $downloadRequest->entry);
        $record->setAttribute('file', $downloadRequest->file);
        $record->setAttribute('mailChimpSubscribe', $downloadRequest->mailChimpSubscribe);
        $record->setAttribute('mailChimpList', $downloadRequest->mailChimpList);
        $record->setAttribute('sessionId', $downloadRequest->sessionId);
        $record->setAttribute('ip', $downloadRequest->ip);
        $record->setAttribute('date', $downloadRequest->date);
        $record->validate();

        try {
            $record->save(false);

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param int $page
     * @param int $limit
     * @return Request[]
     */
    public function getDownloadRequests($page, $limit)
    {
        $offset = $limit * ($page - 1);

        $results = (new Query())
            ->select('*')
            ->from([DownloadFormRecord::$tableName])
            ->orderBy('dateCreated DESC')
            ->offset($offset)
            ->limit($limit)
            ->all();

        return $results;
    }

    /**
     * @param int $limit
     * @return int
     */
    public function getTotalDownloadRequestPages($limit = 30)
    {
        $totalResults = (new Query())
            ->select('*')
            ->from([DownloadFormRecord::$tableName])
            ->count();

        return ceil($totalResults / $limit);
    }
}
