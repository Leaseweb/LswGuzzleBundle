<?php

namespace Lsw\GuzzleBundle\Plugin;

use Guzzle\Service\Command\CommandInterface;

use Guzzle\Common\Event;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Maintains a list of requests and responses sent using a request or client
 */
class HistoryPlugin implements EventSubscriberInterface, \IteratorAggregate, \Countable
{
	/** @var int The maximum number of requests to maintain in the history */
	protected $limit = 10;

	/** @var array Requests and responses that have passed through the plugin */
	protected $transactions = array();

	public static function getSubscribedEvents()
	{
		return array('command.after_send' => array('onCommandAfterSent', 9999));
	}

	/**
	 * Convert to a string that contains all request and response headers
	 *
	 * @return string
	 */
	public function __toString()
	{
		$lines = array();
		foreach ($this->transactions as $command) {
			$request = $command->getRequest(); 
			$response = $request->getResponse(); 
			$lines[] = '> ' . trim($request) . "\n\n< " . trim($response) . "\n";
		}

		return implode("\n", $lines);
	}

	/**
	 * Add a request to the history
	 *
	 * @param RequestInterface $request  Request to add
	 * @param Response         $response Response of the request
	 *
	 * @return HistoryPlugin
	 */
	public function add(CommandInterface $command)
	{
		$this->transactions[] = $command;
		
		if (count($this->transactions) > $this->getlimit()) {
			array_shift($this->transactions);
		}

		return $this;
	}

	/**
	 * Set the max number of requests to store
	 *
	 * @param int $limit Limit
	 *
	 * @return HistoryPlugin
	 */
	public function setLimit($limit)
	{
		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * Get the request limit
	 *
	 * @return int
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * Get all of the raw transactions in the form of an array of associative arrays containing
	 * 'request' and 'response' keys.
	 *
	 * @return array
	 */
	public function getAll()
	{
		return $this->transactions;
	}

	/**
	 * Get the requests in the history
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		// Return an iterator just like the old iteration of the HistoryPlugin for BC compatibility (use getAll())
		return new \ArrayIterator($this->transactions);
	}

	/**
	 * Get the number of requests in the history
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->transactions);
	}

	/**
	 * Get the last request sent
	 *
	 * @return RequestInterface
	 */
	public function getLastRequest()
	{
		$last = end($this->transactions);

		return $last['request'];
	}

	/**
	 * Get the last response in the history
	 *
	 * @return Response|null
	 */
	public function getLastResponse()
	{
		$last = end($this->transactions);

		return isset($last['response']) ? $last['response'] : null;
	}

	/**
	 * Clears the history
	 *
	 * @return HistoryPlugin
	 */
	public function clear()
	{
		$this->transactions = array();

		return $this;
	}

	public function onCommandAfterSent(Event $event)
	{
		$this->add($event['command']);
	}
}
