<?php

namespace Core\Response;

use Exception;
use Core\Registry;

class Response implements ResponseInterface
{
    private $headers;
    private $body;
    private $status;
    private $contentType;

    public function __construct($body = '', $status = 200, $headers = [])
    {
        $this->setBody($body);
        $this->setStatus($status);
        $this->setHeaders($headers);
    }

    public function setContentType($contentType)
    {
        if (!in_array($contentType, Registry::Config('contentTypes'))) {
            throw new Exception('Mime type "{$contentType}" is not part of the configuration');
        }
        $this->contentType = $contentType;
        return $this;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function setStatus($status = 200)
    {
        if (!$this->isValidStatus()) {
            throw new Exception('HTTP status code "{$status}" is not valid.');
        }
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function sendHeader()
    {
        if (headers_sent()) {
            return $this;
        }
        foreach ($this->headers as $name => $values) {
            $replace = 0 === strcasecmp($name, 'Content-Type');
            foreach ($values as $value) {
                header($name . ': ' . $value, $replace, $this->status);
            }
        }
        return $this;
    }

    public function redirect($redirect)
    {
        if (!isset($redirect) || empty($redirect)) {
            throw new Exception("Redirect parameter has not been set or is invalid.");
        }
        $redirect = strtolower($redirect);

        header('Location: ' . Registry::IncomingRequest()->urlScheme() . Registry::IncomingRequest()->urlDomain() . '/' . $redirect);
        die;
    }

    public function setHeaders($headers)
    {
        if (!isset($headers)) {
            throw new Exception("Header value cannot be null");
        }
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
        return $this;
    }

    public function setHeader($name, $value)
    {
        if (!isset($name) || !isset($value)) {
            throw new Exception("Name or value parameters cannot be null");
        }
        $this->headers[] = strtolower([$name, $value]);
        return $this;
    }


    public function setBody($body = null)
    {
        try {
            if (isset($this->header['content-type'])) {
                $contentType = strtolower($this->header['content-type']);
                if ($this->isJson($contentType, $body)) {
                    $this->body = json_decode($body);
                } else if ($contentType == 'text/plain') {
                    $this->body[] = $body;
                } else {
                    $this->body = (array) $body;
                }
                return $this;
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function getBody()
    {
        return $this->body;
    }

    public function isJson($contentType, $body): bool
    {
        if ($contentType == 'application/json' && json_decode($body)) {
            return false;
        }
        return true;
    }

    public function isValidStatus()
    {
        if ($this->status == null) {
            return true;
        } else if (array_key_exists($this->status, Registry::Config('statusCodes'))) {
            return true;
        }
        return false;
    }
}