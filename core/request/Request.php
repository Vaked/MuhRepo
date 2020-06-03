<?php

namespace Core\Request;

use Exception;
use DOMDocument;

abstract class Request implements RequestInterface
{
    protected $requestUri = "";
    protected $requestMethod = "";
    protected $files = null;
    protected $body = "";
    protected $header = [];
    protected $contentType = "";

    abstract public function setContentType($contentType);

    public function getContentType()
    {
        if (!isset($this->contentType)) {
            throw new Exception("Mime type has not been set.");
        }
        return $this->contentType;
    }

    abstract public function setRequestMethod($requestMethod);

    public function getRequestMethod()
    {
        if (!isset($this->requestMethod)) {
            throw new Exception("Request method has not been set.");
        }
        return $this->requestMethod;
    }

    abstract public function setUri($uri);

    public function getUri()
    {
        if (!isset($this->requestUri)) {
            throw new Exception("Request uri has been set.");
        }
        return $this->requestUri;
    }

    abstract public function setFiles($files = null);

    public function getFiles(): array
    {
        if ($_FILES) {
            if (!isset($this->files)) {
                throw new Exception("Files property has not been set.");
            }
        }
        return $this->files;
    }

    abstract public function setHeader($header = []);

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getHeaderParameter($parameter): string
    {
        if (!isset($this->header[$parameter])) {
            throw new Exception("Header parameters have not been set or wrong parameter name supplied.");
        }
        return $this->header[$parameter];
    }

    public function setBody($bodyContent)
    {
        try {
            if ($this->getContentType()) {
                if ($this->isJson()) {
                    $this->body = json_decode($bodyContent);
                } else if ($this->isXML()) {
                    $bodyContent = implode($bodyContent);
                    $this->body = simplexml_load_string($bodyContent);
                } else if ($this->isHTML()) {
                    $doc = new DOMDocument();
                    $doc->loadHTML($bodyContent);
                    $this->body = simplexml_import_dom($doc);
                } else {
                    $this->body = $bodyContent;
                }
                return $this;
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function getBody(): array
    {
        if ($this->requestMethod == 'POST' || $this->requestMethod == 'PUT') {
            if (!isset($this->body)) {
                throw new Exception("Body has not been set.");
            }
        }
        return $this->body;
    }

    private function isJson(): bool
    {
        if ($this->contentType == 'application/json' && json_decode($this->body)) {
            return true;
        }
        return false;
    }

    private function isXML(): bool
    {
        if ($this->contentType == $this->contentType = 'text/xml' || 'application/xml') {
            return true;
        }
        return false;
    }

    private function isHTML(): bool
    {
        if ($this->contentType == $this->contentType = 'text/html' || 'multipart/form-data') {
            return true;
        }
        return false;
    }

    public function urlDomain()
    {
        return $_SERVER['SERVER_NAME'];
    }

    public function urlScheme()
    {
        return (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://');
    }
}
