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
     * getGroups
     * Parses the provided group string in settings and returns it as an array.
     *
     * @return array
     */
    private function getGroups(): array {
        // Variables
        $settings = Mailerlite::getInstance()->getSettings();
        $groupsString = $settings->groups;

        if ($groupsString == "") {
            return [];
        } else {
            // Parse the groups string and check if they are valid integers
            $numbers = explode(',', $groupsString);
            $result = [];
        
            foreach ($numbers as $number) {
                $number = trim($number);
        
                if (is_numeric($number)) {
                    $result[] = (int) $number;
                } else {
                    $msg = "Error: Group ID should be an integer";
                    Craft::error("[MAILERLITE] => ". $msg, __METHOD__);
                    throw new BadRequestHttpException('Error: Group ID should be an integer');
                }
            }
            return $result; 
        }
    }
    
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
                        "groups" => $this->getGroups()
                    ],
                    'headers' => [
                        'X-MailerLite-ApiDocs' => 'true',
                        'X-MailerLite-ApiKey' => $settings->getApiKey(),
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ],
                ]);
                
                Craft::info("[MAILERLITE] — Successfully subscribed " . $subscriberName);
                return true;
                
            } catch (ClientException $e) {
                $error = '[MAILERLITE] API response => "' . $e->getResponse()->getReasonPhrase() . '"';
                Craft::error($error);
                throw new BadRequestHttpException($error);
            }
        }
    }
}
