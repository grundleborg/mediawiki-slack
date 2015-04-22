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
 * @file The hooks of the Mediawiki Slack integration extension.
 *       For more information on Slack, see http://www.slack.com.
 * @ingroup Extensions
 */

class SlackHooks {

  public static function encodeSlackChars($in) {
    // This function encodes chars that the Slack API expects to be encoded in the JSON values.
    // See https://api.slack.com/docs/formatting for details.
    $o = str_replace("&", "&amp;", $in);
    $o = str_replace("<", "&lt;", $o);
    $o = str_replace(">", "&gt;", $o);
    $o = str_replace('"', "&quot;", $o);

    return $o;
  }

  public static function onPageContentSaveComplete( $article, $user, $content, $summary, $isMinor, 
    $isWatch, $section, $flags, $revision, $status, $baseRevId ) {
      global $wgSlackWebhookURL, $wgSlackChannel, $wgSlackUserName, $wgSlackLinkUsers;

      wfDebug("Slack URL: ".$wgSlackWebhookURL."\n");

      // Build the message we're going to post to Slack.
      $message = '*<'.SlackHooks::encodeSlackChars($article->getTitle()->getFullURL())
                     .'|'.SlackHooks::encodeSlackChars($article->getTitle()).'>* '
                .'modified by *';
      if ($wgSlackLinkUsers) {
        $message .= '@';
      }
      $message .= SlackHooks::encodeSlackChars(strtolower($user->getName())).'*: '
                 .SlackHooks::encodeSlackChars($summary).'.';

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

}
