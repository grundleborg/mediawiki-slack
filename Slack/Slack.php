<?php
/**
 * Mediawiki Slack integration extension.
 * @version 0.1.0
 *
 * Copyright (C) 2014 George Goldberg <george@grundleborg.com>
 * @author George Goldberg <george@grundleborg.com>
 *
 * @license MIT
 *
 * @file The main file of the Mediawiki Slack integration extension.
 *       For more information on Slack, see http://www.slack.com.
 * @ingroup Extensions
 */

// Credits
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Slack',
	'author' => array(
		'George Goldberg'
	),
	'version'  => '0.1.0',
	'url' => 'https://github.com/grundleborg/mediawiki-slack',
  'descriptionmsg' => 'Slack Integration for Media Wiki.',
);

// Init base-directories.
$dir = dirname( __FILE__ );
$dirbasename = basename( $dir );

// Register files
$wgAutoloadClasses['SlackHooks'] = $dir . '/Slack.hooks.php';

// Register hooks
$wgHooks['PageContentSaveComplete'][] = 'SlackHooks::onPageContentSaveComplete';

// Configuration Defaults
// These should be overridden in LocalSettings.php
// See README.md for an example.
/*
 * The name of your Slack Team.
 */
$wgSlackTeamName = "example";

/*
 * The Token for your Incoming WebHooks Integration.
 */
$wgSlackIntegrationToken = "yourtokenvalue";

/*
 * The channel in which the integration should report all changes.
 */
$wgSlackChannel = "#random";

/*
 * The username of the integration bot that will appear in Slack.
 */
$wgSlackUserName = "wikibot";

/*
 * Whether to prefix Wiki User Names wth an @ in Slack messages, linking them to the Slack
 * user with the same name.
 *
 * Note that the wiki user name is converted to lower case (as MediaWiki always makes
 * user names have a capital first letter, which is quite unusual in practice on other services).
 * This means that if the corresponding Slack user name contains upper case letters, the user
 * matching won't actually work, because Slack user matching is case sensitive.
 */
$wgSlackLinkUsers = false;

/*
 * Notify or not about edits, marked as "minor"
 */
$wgSlackIgnoreMinor = false;