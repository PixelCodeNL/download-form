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

use aelvan\mailchimpsubscribe\services\MailchimpSubscribeService;
use pixelcode\downloadform\DownloadForm;

use Craft;
use craft\base\Component;
use pixelcode\downloadform\models\Request;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DownloadFormMailChimpSubscribeService extends Component
{
    /**
     * Subscribe name and email in MailChimp
     * @param Request $request
     */
    public function subscribeToMailChimp(Request $request)
    {
        if (DownloadForm::getInstance()->downloadFormService->isMailChimpAvailable() && $request->mailChimpSubscribe) {
            /** @var MailchimpSubscribeService $mailChimpSubscribeService */
            $mailChimpSubscribeService = MailchimpSubscribeService::instance();
            $settings = DownloadForm::getInstance()->getSettings();
            $settingsAudienceId = $settings['mailChimpList'];
            $formAudienceId = $request->mailChimpList;
            $audienceId = !empty($formAudienceId) ? $formAudienceId : $settingsAudienceId;

            if ($audienceId) {
                if ($mailChimpSubscribeService) {
                    $name = $request->name;
                    $nameParts = explode(' ', $name);
                    $firstName = '';
                    $lastName = '';
                    if (count($nameParts) >= 1) {
                        $firstName = $nameParts[0];
                        if (count($nameParts) > 1) {
                            $lastName = implode(' ', array_slice($nameParts, 1, count($nameParts)));
                        }
                    }

                    $opts['merge_fields'] = [
                        'FNAME' => $firstName,
                        'LNAME' => $lastName
                    ];

                    $opts['tags'] = null;
                    
                    Craft::info(sprintf('Subscribe %s to list %s', $request->email, $audienceId), DownloadForm::LOGGER_CATEGORY);

                    $result = $mailChimpSubscribeService->subscribe($request->email, $audienceId, $opts);

                    if (array_key_exists('success', $result) && !$result['success']) {
                        Craft::error(sprintf('Error in MailChimp subscription: %s', print_r($result, true)), DownloadForm::LOGGER_CATEGORY);
                    }

                    Craft::info(sprintf('MailChimp result: %s', print_r($result, true)), DownloadForm::LOGGER_CATEGORY);
                } else {
                    Craft::error('Cannot find MailchimpSubscribeService', DownloadForm::LOGGER_CATEGORY);
                }
            } else {
                Craft::error('MailChimp list ID is not configured. Subscription skipped.', DownloadForm::LOGGER_CATEGORY);
            }
        }
    }
}
