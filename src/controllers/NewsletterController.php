<?php
/**
 * @copyright Copyright (c) Agence ink
 * Code by Mathieu V.
 */
namespace agenceink\craftmailerlite\controllers;

use Craft;
use craft\web\Controller;
use agenceink\craftmailerlite\Plugin as MailerLite;


class NewsletterController extends Controller {

    protected array|bool|int $allowAnonymous = ['subscribe'];
    
    /**
     * actionSubscribe
     *
     * @return void
     */
    public function actionSubscribe() {

        $userMail = Craft::$app->getRequest()->getRequiredBodyParam('email');
        $userName = Craft::$app->getRequest()->getBodyParam('full-name');
        $redirect = Craft::$app->getRequest()->getValidatedBodyParam('redirect');

        MailerLite::$plugin->service->addSubscriber($userMail, $userName);
        Craft::$app->session->setFlash('success', true);

        if ($redirect) {
            return $this->redirect($redirect);
        }

        return;
    }
}
?>