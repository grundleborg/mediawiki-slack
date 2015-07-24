<?php
/**
 * Mediawiki Slack integration extension.
 * @version 1.0.1
 *
 * Copyright (C) 2014-2015 George Goldberg <george@grundleborg.com>
 * @author George Goldberg <george@grundleborg.com>
 *
 * @license MIT
 *
 * @file The hooks of the Mediawiki Slack integration extension.
 *       For more information on Slack, see http://www.slack.com.
 * @ingroup Extensions
 */

class SlackHooks {

  public static function isCreate() {
    global $wgSlackIsCreate;

    if ($wgSlackIsCreate === true) {
      return true;
    }

    $wgSlackIsCreate = true;
    return false;
  }

  public static function encodeSlackChars($in) {
    // This function encodes chars that the Slack API expects to be encoded in the JSON values.
    // See https://api.slack.com/docs/formatting for details.
    $o = str_replace("&", "&amp;", $in);
    $o = str_replace("<", "&lt;", $o);
    $o = str_replace(">", "&gt;", $o);
    $o = str_replace('"', "&quot;", $o);

    return $o;
  }

  public static function sendToSlack($payload) {
    global $wgSlackWebhookURL;

    wfDebug("Slack URL: ".$wgSlackWebhookURL."\n");
    wfDebug("Slack Payload: ".$payload."\n");

    $post = "payload=".urlencode($payload);

    // POST it to Slack.
    $options = array(
      'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
         'method'  => 'POST',
         'content' => $post,
       ),
     );

    $context  = stream_context_create($options);
    $result = file_get_contents($wgSlackWebhookURL, false, $context);
    
    wfDebug("Slack Result: ".$result."\n");
  }

  public static function buildMessage($wikiPage, $user, $summary, $verb) {
    global $wgSlackLinkUsers;

    // Build the message we're going to post to Slack.
    $message = '*<'.SlackHooks::encodeSlackChars($wikiPage->getTitle()->getFullURL())
                   .'|'.SlackHooks::encodeSlackChars($wikiPage->getTitle()).'>* '
              .$verb.' by *';
    if ($wgSlackLinkUsers) {
      $message .= '@';
    }
    $message .= SlackHooks::encodeSlackChars(strtolower($user->getName())).'*';
    if (!empty($summary)) {
      $message .= ': '.SlackHooks::encodeSlackChars($summary);
    }
    $message .= '.';

    return $message;
  }

  public static function buildPayload($message) {
    global $wgSlackChannel, $wgSlackLinkUsers, $wgSlackUserName;

    // Build the WebHook Payload.
    // NB: The Slack parser chokes if there is a trailing , at the end of the list of items
    //     in the payload. Make sure any optional items are in the middle to avoid this.
    $payload = '{"channel": "'.$wgSlackChannel.'",';
    if ($wgSlackLinkUsers) {
      $payload .= '"link_names": "1",';
    }
    $payload .= '"username": "'.$wgSlackUserName.'",'
               .'"text": "'.$message.'"'
               .'}';

    return $payload;
  }

  public static function onPageContentSaveComplete($wikiPage, $user, $content, $summary, $isMinor, 
    $isWatch, $section, $flags, $revision, $status, $baseRevId) {
    global $wgSlackIgnoreMinor;

    // If this is a minor edit and we want to ignore minor edits, return now.
    if ($wgSlackIgnoreMinor && $isMinor) {
      return true;
    }

    // If this is a page creation, don't notify it as being modified too.
    if (true === SlackHooks::isCreate()) {
      return true;
    }

    // Build the Slack Message.
    $message = SlackHooks::buildMessage($wikiPage, $user, $summary, "modified");

    // Build the Slack Payload.
    $payload = SlackHooks::buildPayload($message);

    // Send the message to Slack.
    SlackHooks::sendToSlack($payload);

    return true;
  }

  public static function onPageContentInsertComplete($wikiPage, $user, $content, $summary,
    $isMinor, $isWatch, $section, $flags, $revision) {
    global $wgSlackIsCreate, $wgSlackIgnoreMinor;

    // If this is a minor edit and we want to ignore minor edits, return now.
    if ($wgSlackIgnoreMinor && $isMinor) {
      return true;
    }

    // Flag this as a page creation so we don't notify it's been modified as well.
    $wgSlackIsCreate = true;

    // Build the Slack Message.
    $message = SlackHooks::buildMessage($wikiPage, $user, $summary, "created");

    // Build the Slack Payload.
    $payload = SlackHooks::buildPayload($message);

    // Send the message to Slack.
    SlackHooks::sendToSlack($payload);

    return true;
  }

}
