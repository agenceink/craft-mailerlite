<?php
/**
 * @copyright Copyright (c) Agence ink
 * Code by Mathieu V.
 */
namespace agenceink\craftmailerlite\services;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use agenceink\craftmailerlite\Plugin as MailerLite;
use yii\web\BadRequestHttpException;

class MailerliteService extends Component {
    
    /**
     * addSubscriber
     *
     * @param  mixed $subscriberEmail
     * @param  mixed $subscriberName
     * @return bool
     */
    public function addSubscriber(string $subscriberEmail, string $subscriberName = null): bool {
        // Variables
        $settings = Mailerlite::getInstance()->getSettings();
        $subscriberName = (!$subscriberName) ? $subscriberEmail : $subscriberName;

        // Check the email address
        if (!filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL)) {
            $error = "[MAILERLITE] — Invalid email format";
            Craft::error($error);
            throw new BadRequestHttpException($error);

        // Check MailerLite API key
        } elseif ($settings->getApiKey() === null || $settings->getApiKey() == "") {
            $error = "[MAILERLITE] — Your MailerLite API key is missing";
            Craft::error($error);
            throw new BadRequestHttpException($error);

        // Send the request to the API
        } else {
            try {
                $client = new Client();
                $client->request('POST', 'https://api.mailerlite.com/api/v2/subscribers', [
                    'json' => [
                        "email" => $subscriberEmail,
                        "resubscribe" => false,
                        "fields" => [
                            "name" => $subscriberName,
                        ],
                    ],
                    'headers' => [
                        'X-MailerLite-ApiDocs' => 'true',
                        'X-MailerLite-ApiKey' => $settings->getApiKey(),
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ],
                ]);

                return true;
                
            } catch (ClientException $e) {
                $error = '[MAILERLITE] API response => "' . $e->getResponse()->getReasonPhrase() . '"';
                Craft::error($error);
                throw new BadRequestHttpException($error);
            }
        }
    }
}
