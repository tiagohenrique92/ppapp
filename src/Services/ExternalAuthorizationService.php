<?php

namespace PPApp\Services;

use GuzzleHttp\Psr7\Request;

class ExternalAuthorizationService 
{
    private $url;
    
    public function __construct(string $url)
    {
        $this->url = $url;
    }
    
    public function getAuthorization(): string
    {
        $request = new Request('GET', $this->url);
            $body = $request->getBody();
            die('<pre>' . __FILE__ . '[' . __LINE__ . ']' . PHP_EOL . print_r($body, true) . '</pre>');
            return "blablabla";
        }
    }