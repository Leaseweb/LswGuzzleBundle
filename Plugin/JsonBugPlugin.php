<?php

namespace Lsw\GuzzleBundle\Plugin;

use Guzzle\Service\Command\CommandInterface;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Fixes bug in Guzzle
 */
class JsonBugPlugin implements EventSubscriberInterface
{
	private $body;
	
	public static function getSubscribedEvents()
	{
		return array('command.after_send' => array('onCommandAfterSent', 9999));
	}

	public function onCommandAfterSent(Event $event)
	{
		$command = $event['command'];
		$response = $command->getRequest()->getResponse();
		if (!$response || !$response->isContentType('json') || $response->isError()) return;
		$this->body = $response->getBody(true);
		$command->setResult($this->json());
	}
	
	public function json()
	{
		$data = json_decode((string) $this->body);
		if (JSON_ERROR_NONE !== json_last_error()) {
			throw new RuntimeException('Unable to parse response body into JSON: ' . json_last_error());
		}
	
		return $data;
	}
	
}
