LswGuzzleBundle
===============


```
lsw_guzzle:
    clients:
        test:
            config:
                json_objects: true
                curl.options:
                    CURLOPT_CONNECTTIMEOUT: 10
                    CURLOPT_SSL_VERIFYPEER: false
                    CURLOPT_USERAGENT: "LeaseWeb API Caller"
                    CURLOPT_FOLLOWLOCATION: true
                    CURLOPT_SSLVERSION: 3
            description:
                baseUrl: "http://sf2testproject.dev"
                operations: 
                    test: 
                        httpMethod: "GET"
                        uri: "/app_dev.php/demo/?a=b"
                    json: 
                        httpMethod: "GET"
                        uri: "/app_dev.php/demo/json"
                    json_post: 
                        httpMethod: "POST"
                        uri: "/app_dev.php/demo/{action}"
                        parameters:
                            testvalue:
                                location: xml
                            action:
                                location: uri
```

```
    	$response = $this->get('guzzle.test')->getCommand('test')->execute();
    	$response = $this->get('guzzle.test')->getCommand('json')->execute();
    	$response = $this->get('guzzle.test')->getCommand('json_post',array('action'=>'json','testvalue'=>666))->execute();
```
