<?php
/**
 * @copyright Copyright (c) Agence ink
 * Code by Mathieu V.
 */
namespace agenceink\craftmailerlite;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\elements\User;
use craft\services\Elements;
use yii\base\Event;
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
    public bool $hasCpSection = false;
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

        // == EVENT TRIGGERS == //
        // After a user registration
        Event::on(
            Elements::class, 
            Elements::EVENT_AFTER_SAVE_ELEMENT,
            function(Event $event) {
            if ($event->element instanceof User) {
                $settings = $this->getSettings();
                if ($settings->registration) {
                    $this::$plugin->service->addSubscriber($event->element->email, $event->element->fullName);
                }
            }
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
