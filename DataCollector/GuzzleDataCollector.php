<?php
namespace Lsw\GuzzleBundle\DataCollector;

use Guzzle\Service\Command\AbstractCommand;

use Symfony\Component\Yaml\Yaml;
use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lsw\GuzzleBundle\Plugin\CommandHistoryPlugin;

/**
 * GuzzleDataCollector
 *
 * @author Maurits van der Schee <m.vanderschee@leaseweb.com>
 */
class GuzzleDataCollector extends DataCollector
{
    private $history;

    /**
     * Class constructor
     *
     * @param CommandHistoryPlugin $history history object
     */
    public function __construct(CommandHistoryPlugin $history)
    {
        $this->history = $history;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $req, Response $res, \Exception $exception = null)
    {
        
    	$data = array(
    			'calls'  => array(),
    			'errors' => 0,
    			'count'  => 0,
    			'time'   => 0,
    	);
    	
    	foreach ($this->history as $command) {
    		$request = $command->getRequest();
			
   			$response = $request->getResponse();
   		    $time = $response->getInfo('total_time');
		    $error = $response->isError();
    		
		    if (!$error) {
    			
				$result = $command->getResult();
				$responseType = 'text/plain';
				
	        	if (is_array($result) || is_a($result,'stdClass')) {
	        		$result = Yaml::dump(json_decode(json_encode($result),true));
	        	} else if (is_a($result,'Guzzle\Http\Message\Response')) {
	        		$result = $response->getBody(true);
	        		$responseType = $response->getContentType();
	        	} else {
	        		$result=(string)$command->getResult();
	        	}
        	
        	} else {
        		
        		$result = '';
        		$responseType = '';
        	
        	}
        	 
        	
        	$status = $response->getStatusCode().' '.$response->getReasonPhrase();
        	
        	$url = implode('',array(
        		$request->getMethod(),' ',
            	$request->getScheme(),'://',
            	$request->getHost(),
            	$request->getPath(),
        		(string)$request->getQuery()?'?...':''
        	));
        	
        	$parameters = $command->toArray();
        	$hidden = $parameters[AbstractCommand::HIDDEN_PARAMS];
        	$parameters = array_diff_key($parameters, array_combine($hidden,$hidden));
        	$parameterCount = count($parameters);
        	$requestParameters = Yaml::dump($parameters);
        	
        	$parameters = $command->getOperation()->getParams();
        	$requestDescription = Yaml::dump(array_map(function($p){ return $p->toArray(); }, $parameters));
        	
        	$operationName = $command->getName();
        	$clientName = $command->getClient()->getDescription()->getName();
        	 
    		$data['calls'][] = array(
				'url'			     => $url,
    			'status'		     => $status,
    			'clientName'         => $clientName,
    			'operationName'      => $operationName,
    			'parameterCount'     => $parameterCount,
    			'requestRaw'         => (string) $request,
    			'requestDescription' => $requestDescription,
    			'requestParameters'  => $requestParameters,
    			'responseRaw'        => (string) $response,
    			'responseBody'       => $response->getBody(true),
    			'responseType'       => $responseType,
    			'responseObject'     => $result,
    			'time'               => $time,
    			'error'              => $error
    		);
        	
        	// update totals
        	$data['count'] += 1;
        	$data['time'] += $time;
        	$data['errors'] += (int) $error;
       	 
    	}
    	
    	//var_dump($data); die();
    	
    	$this->data = $data;
    	
    }

    /**
     * Method counts amount of HTTP statuses, which is not equals to "200 OK"
     *
     * @return number
     */
    public function getReturnedErrorCount()
    {
        return $this->data['errors'];
    }

    /**
     * Method returns amount of logged API calls
     *
     * @return number
     */
    public function getCallCount()
    {
        return $this->data['count'];
    }

    /**
     * Method returns all logged API call objects
     *
     * @return mixed
     */
    public function getCalls()
    {
        return $this->data['calls'];
    }

    /**
     * Method calculates API calls execution time
     *
     * @return number
     */
    public function getTime()
    {
        return $this->data['time'];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'guzzle';
    }
}
