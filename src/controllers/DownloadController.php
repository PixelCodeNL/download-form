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

/**
 * @author    Pixel&Code
 * @package   DownloadForm
 * @since     2.0.0
 */
class DownloadController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $allowAnonymous = ['index'];

    /**
     * Download the file
     * @param string $key
     * @throws \Exception
     */
    public function actionIndex($key = '')
    {
        $decodedKey = base64_decode($key);
        $keyParts = explode('|', $decodedKey);

        if (count($keyParts) == 2) {
            $sessionId = $keyParts[0];
            $fileUrl = $keyParts[1];

            if ($sessionId == session_id()) {
                $extension = pathinfo($fileUrl, PATHINFO_EXTENSION);
                $randomName = bin2hex(random_bytes(8));

                $fileName = sprintf('%s.%s', $randomName, $extension);
                $fileName = substr($fileName, 0, strpos($fileName, "?"));

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $fileName);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');

                readfile($fileUrl);

                exit;
            }
        }

        http_response_code(404);
    }
}
