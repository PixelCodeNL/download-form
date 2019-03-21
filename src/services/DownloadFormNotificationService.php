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

use craft\mail\Message;
use craft\web\View;
use pixelcode\downloadform\DownloadForm;

use Craft;
use craft\base\Component;
use pixelcode\downloadform\models\Request;

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DownloadFormNotificationService extends Component
{
    /**
     * Send a notification when a download form is submitted
     * @param Request $request
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function sendDownloadFormRequestNotification(Request $request)
    {
        $settings = DownloadForm::getInstance()->getSettings();
        $toEmail = $settings['notificationEmail'];

        if ($toEmail) {
            $email = new Message();

            # Set the template context to CP
            Craft::$app->getView()->setTemplateMode(View::TEMPLATE_MODE_CP);

            $html = Craft::$app->getView()->renderTemplate('download-form/email/notification.twig', [
                'downloadRequest' => $request,
            ]);

            $email->setTo($toEmail);
            $email->setSubject(DownloadForm::t('New download request'));
            $email->setHtmlBody($html);

            Craft::$app->mailer->send($email);
        } else {
            Craft::error('No notification email set, notification is not send.', DownloadForm::LOGGER_CATEGORY);
        }

    }

}
