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

use DateTime;
use pixelcode\downloadform\DownloadForm;

use Craft;
use craft\web\Controller;
use pixelcode\downloadform\models\Request;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class FormController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $allowAnonymous = ['submit'];


    /**
     * Submit a download form
     * @throws \Exception
     */
    public function actionSubmit()
    {
        $success = false;
        $url = null;

        $data = Craft::$app->request->post('downloadForm');

        if ($data) {
            $name = $data['name'];
            $email = $data['email'];
            $mailChimpSubscribe = array_key_exists('mailChimpSubscribe',
                $data) ? $data['mailChimpSubscribe'] : false;
            $mailChimpList = array_key_exists('mailChimpList',
                $data) ? $data['mailChimpList'] : '';
            $entryData = $data['entry'];

            if (!empty($name) && !empty($email) && !empty($entryData)) {
                $entry = Craft::$app->entries->getEntryById($entryData);

                if ($entry) {
                    $downloadFormSettings = $entry->downloadForm;
                    if ($downloadFormSettings && $downloadFormSettings['file']) {

                        $file = Craft::$app->assets->getAssetById($downloadFormSettings['file'][0])->getUrl();
                        $downloadFileUrl = DownloadForm::getInstance()->downloadFormService->createDownloadUrl($file);

                        $date = new DateTime();

                        $downloadRequest = new Request();
                        $downloadRequest->name = $name;
                        $downloadRequest->email = $email;
                        $downloadRequest->entry = $entry->getId();
                        $downloadRequest->pageUrl = $entry->getUrl();
                        $downloadRequest->mailChimpSubscribe = $mailChimpSubscribe;
                        $downloadRequest->mailChimpList = $mailChimpList;
                        $downloadRequest->file = $file;
                        $downloadRequest->sessionId = session_id();
                        $downloadRequest->ip = Craft::$app->request->getUserIP();
                        $downloadRequest->date = $date->format('Y-m-d H:i:s');

                        if (DownloadForm::getInstance()->downloadFormService->saveDownloadRequest($downloadRequest)) {
                            $success = true;
                            $url = $downloadFileUrl;

                            DownloadForm::getInstance()->downloadFormNotificationService->sendDownloadFormRequestNotification($downloadRequest);
                            DownloadForm::getInstance()->downloadFormMailChimpSubscribeService->subscribeToMailChimp($downloadRequest);
                        }
                    }
                }
            }
        }

        return $this->asJson([
            'success' => $success,
            'url' => $url
        ]);
    }
}
