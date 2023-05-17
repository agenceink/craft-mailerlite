<?php
/**
 * @copyright Copyright (c) Agence ink
 * Code by Mathieu V.
 */
namespace agenceink\craftmailerlite;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use agenceink\craftmailerlite\models\Settings;
use agenceink\craftmailerlite\services\MailerliteService;

/**
 * MailerLite plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 * @author Agence ink <support@agenceink.com>
 * @copyright Agence ink
 * @license MIT
 */
class Plugin extends BasePlugin {
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;
    public static $plugin;

    public static function config(): array {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void {
        parent::init();
        self::$plugin = $this;

        // Register service
        $this->setComponents([
            'service' => MailerliteService::class,
        ]);

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }
    

    protected function createSettingsModel(): ?Model {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string {
        return Craft::$app->view->renderTemplate('mailerlite/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
    }
}
