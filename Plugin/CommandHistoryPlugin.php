<?php

namespace Lsw\GuzzleBundle\Plugin;

use Guzzle\Service\Command\CommandInterface;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Http\Message\RequestInterface;

/**
 * Maintains a list of commands sent using a client
 */
class CommandHistoryPlugin implements EventSubscriberInterface, \IteratorAggregate, \Countable
{
	/** @var array Commands that have passed through the plugin */
	protected $transactions = array();

	public static function getSubscribedEvents()
	{
		return array(
			'command.before_send' => array('onCommandBeforeSent', 9999),
			'request.sent' => array('onRequestSent', 9999),
		);
	}

	/**
	 * Add a request to the history
	 *
	 * @param RequestInterface $request Request to add
	 * @param CommandInterface $command Command to add
	 *
	 * @return CommandHistoryPlugin
	 */
	public function add(RequestInterface $request, CommandInterface $command = null)
	{
		$key = spl_object_hash($request);
		$this->transactions[$key] = array('request' => $request, 'command' => $command);
		
		return $this;
	}

	/**
	 * Get the requests in the history
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
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
	
	public function onCommandBeforeSent(Event $event)
	{
		$request = $event['command']->getRequest();
		$this->add($request, $event['command']);
	}
	
	public function onRequestSent(Event $event)
	{
		$this->add($event['request']);
	}
	
}
