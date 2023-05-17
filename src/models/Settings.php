<?php
/**
 * @copyright Copyright (c) Agence ink
 * Code by Mathieu V.
 */
namespace agenceink\craftmailerlite\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;

/**
 * mailerlite settings
 */
class Settings extends Model {
   // API Key
    public $apiKey;
    public function getApiKey(): string {
        return App::parseEnv($this->apiKey);
    }
}
