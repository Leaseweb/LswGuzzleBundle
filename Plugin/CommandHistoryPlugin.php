<?php

namespace Lsw\GuzzleBundle\Plugin;

use Guzzle\Service\Command\CommandInterface;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Maintains a list of commands sent using a client
 */
class CommandHistoryPlugin implements EventSubscriberInterface, \IteratorAggregate, \Countable
{
	/** @var array Commands that have passed through the plugin */
	protected $transactions = array();

	public static function getSubscribedEvents()
	{
		return array('command.before_send' => array('onCommandBeforeSent', 9999));
	}

	/**
	 * Add a request to the history
	 *
	 * @param CommandInterface $command Command to add
	 *
	 * @return CommandHistoryPlugin
	 */
	public function add(CommandInterface $command)
	{
		$this->transactions[] = $command;
		
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
		$this->add($event['command']);
	}
	
}
