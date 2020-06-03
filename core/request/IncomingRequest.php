<?php

namespace Core\Request;

use Core\Registry;
use Core\Request\Request;
use Exception;

class IncomingRequest extends Request
{
    public function __construct()
    {
        $this->setUri();
        $this->setRequestMethod();
        $this->setFiles();
        $this->evaluateHeader();
        $this->evaluateBody();
    }

    public function setContentType($contentType)
    {
        if (
            !isset($this->header['content-type']) &&
            !in_array($this->header['content-type'], Registry::Config('contentTypes'))
        ) {
            throw new Exception("Header has not been set, unable to get content type");
        }
        $this->contentType = strtolower($this->header['content-type']);
        return $this;
    }

    public function setRequestMethod($requestMethod = null)
    {
        if ($requestMethod == null) {
            $this->requestMethod = $_SERVER['REQUEST_METHOD'];
            return $this;
        }
        $this->requestMethod = $requestMethod;
        return $this;
    }

    public function setUri($uri = null)
    {
        if ($uri == null) {
            $this->requestUri = urldecode(strtolower(ltrim($_SERVER['REQUEST_URI'], '/')));
            return $this;
        }
        $this->uri = $uri;
        return $this;
    }

    public function setFiles($files = null)
    {
        if (isset($_FILES) && count($_FILES)) {
            $this->files = $_FILES;
        }
        return $this;
    }

    public function setHeader($header = [])
    {
        $this->header = apache_request_headers();
        $this->header = array_change_key_case($this->header, CASE_LOWER);
        return $this;
    }

    public function evaluateBody(): void
    {
        switch ($this->requestMethod) {
            case 'GET':
                $this->body = $_GET;
                break;
            case 'POST':
            case 'PUT':
                $requestContent = (file_get_contents('php://input'));
                parse_str($requestContent, $requestContentArray);
                $mergedRequest = array_merge($_POST, $requestContentArray);
                $this->setBody($mergedRequest);
                break;
            case 'DELETE':
                break;
            default:
                throw new Exception('Invalid request');
                break;
        }
    }

    public function evaluateHeader()
    {
        $this->setHeader();
        if (isset($this->header['cоntent-type']))
            $this->setContentType(strtolower($this->header['content-type']));
        if ($this->requestMethod == 'POST' || $this->requestMethod == 'PUT') {
            if (!in_array($this->contentType, Registry::Config('contentTypes'))) {
                throw new Exception('Mime Type not supported!');
            }
        }
    }
}