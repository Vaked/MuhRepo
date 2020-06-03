<?php

namespace Core\Request;

use Core\Registry;
use Core\Request\Request;
use Exception;

class OutgoingRequest extends Request
{
    public function setContentType($contentType)
    {
        if(!in_array($contentType,Registry::Config('contentTypes'))){
            Throw new Exception("Mime type is not a part of the allowed types in the configuration.");
        }
        $this->contentType = $contentType;
        return $this;
    }

    public function setRequestMethod($method)
    {
        if($method == null || empty($method)){
            throw new Exception("Method parameter cannot be null or empty.");
        }
        $this->requestMethod = $method;
        return $this;
    }

    public function setHeader($header = [])
    {
        if ($header = null || empty($header)) {
            throw new Exception("Header value cannot be null.");
        }
        $this->header = $header;
        return $this;
    }

    public function setUri($uri)
    {
        if($uri == null || empty($uri)){
            throw new Exception("Uri value cannot be null or empty.");
        }
        $this->requestUri = $uri;
        return $this;
    }

    public function setFiles($files = null)
    {
        $this->files = $files;
        return $this;
    }

    public function execute()
    {
        $curl = curl_init();

        if ($this->getRequestMethod()) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->getRequestMethod());
            if ($this->requestMethod == 'POST' && $this->getBody()) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getBody());
            }
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $this->getUri());
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeader());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        $response = curl_exec($curl);
        $this->error = curl_error($curl);
        //TO DO cUrl response

        curl_close($curl);
    }
}
