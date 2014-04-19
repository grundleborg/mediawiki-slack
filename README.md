mediawiki-slack
===============

Mediawiki Integration for Slack.
* http://www.mediawiki.org
* http://www.slack.com

## Features

* Notifies a Slack channel whenever any page on the wiki is edited.

## Requirements

* MediaWiki >= 1.21
* PHP Curl extension.

## Set Up

1. Log in to your Slack Team and click "Integrations".
2. Select "Incoming WebHooks" as the type.
3. Choose the default channel you want to use and click "Add Integration".
4. Copy the Token shown on the left of the page. We'll need this in step 6.
5. Copy the contents of the Slack folder in this repository to your wiki's extensions folder.
6. Add the following lines to LocalSettings.php
   
   ```php
   # Enable the Slack extension
   require_once "$IP/extensions/Slack/Slack.php";

   # Slack extension configuration options
   $wgSlackTeamName = "Your Slack Team Name";
   $wgSlackIntegrationToken = "THE INTEGRATION TOKEN FROM STEP 3";
   $wgSlackUserName = "THE USERNAME YOU WANT YOUR BOT TO HAVE IN SLACK";
   $wgSlackChannel = "#theChannelForBotMessagesToAppearIn";
   ```

7. Edit a wiki page, and see the message pop up in Slack.

## Improvements

Pull requests for new features are welcome. There's lots more I'd like to make this integration do,
but I don't have the time, and it already covers my main requirement.
