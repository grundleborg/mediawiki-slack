mediawiki-slack
===============

Mediawiki Integration for Slack.
* http://www.mediawiki.org
* http://www.slack.com

## Features

* Sends notifications to a Slack channel whenever a page is added or edited on your MediaWiki
  installation.

## Requirements

* MediaWiki >= 1.21

## Set Up

1. Log in to your Slack Team and click "Integrations".
2. Select "Incoming WebHooks" as the type.
3. Choose the default channel you want to use and click "Add Integration".
4. Copy the Webhook URL shown on the left of the page. We'll need this in step 6.
5. If you use composer to manage your MediaWiki extensions, just add ```"mediawiki/slack": ">1.0.1"``` to the require section. If you don't use composer, copy the contents of the repository into a folder called ```Slack``` in your wiki's extensions folder.
6. Add the following lines to LocalSettings.php
   
   ```php
   # Enable the Slack extension
   require_once "$IP/extensions/Slack/Slack.php";

   # Slack extension configuration options
   $wgSlackWebhookURL = "THE INTEGRATION URL FROM STEP 4";
   $wgSlackUserName = "THE USERNAME YOU WANT YOUR BOT TO HAVE IN SLACK";
   $wgSlackChannel = "#theChannelForBotMessagesToAppearIn";
   ```

7. Edit a wiki page, and see the message pop up in Slack.

## Optional Features

The optional features that you can enable and configure are listed below.

### User Name Linking

This feature links the wiki user name to the equivalent Slack user in Slack messages.
Enable it by adding the following line to LocalSettings.php:

```php
$wgSlackLinkUsers = true;
```

Note that wiki usernames are converted to Lower Case before being passed to Slack. However, Slack's
user name linking feature is case sensitive, so if your Slack user name contains capitals, it won't
find you.

### Minor edits

If you don't want to receive notifications about minor edits, add the following line to
LocalSettings.php:

```php
$wgSlackIgnoreMinor = true;
```

## Improvements

Pull requests for new features are welcome. There's lots more I'd like to make this integration do,
but I don't have the time, and it already covers my main requirement.
