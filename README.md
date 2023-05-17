# MailerLite

An easy tool to add users to your MailerLite lists.

## Requirements

This plugin requires Craft CMS 4.4.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “MailerLite”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require agenceink/craft-mailerlite

# tell Craft to install the plugin
./craft plugin/install mailerlite
```

## Usage

This plugin allows you to collect a user email address and to add it to you subscribers list on MailerLite.
It is mostly used to make your "subscribe to the newsletter" form easier!

You will want to set up your MailerLite API key first in the plugin settings, as it will not work without a valid tokens. 

Next, you can create your form in your template :

```twig
<form method="post">
    {{ csrfInput() }}
    {{ actionInput('mailerlite/newsletter/subscribe') }}
    {{ redirectInput('contact/thank-you') }}

    <label for="full-name">Your name :</label>
    <input type="text" id="fullname" name="full-name">

    <label for="email">Your email :</label>
    <input type="email" id="email" name="email" required>

    <input type="submit" value="Send">
</form>
```
### Required fields

The only required field is `email`.

The `email` value must be a valid email address for the plugin to work.

### Optional fields and features

The only optional field is `full-name`. If this field is not detected by the plugin, the provided email address will replace it.

### Using flash messages

The plugin will set a `success` flash message on a successful form submission with the value `true` (or `1`).
You can use it to detect the form submission and show a sucess message to your users.

An BadRequestHttpException will be thrown by the plugin on errors. These can be a missing MailerLite API key, an invalid email address or any other error sent by the MailerLite API.
You'll also be able to find the plugin error messages in Craft's web logs.
