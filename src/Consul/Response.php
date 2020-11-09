<?php

namespace Huangdijia\Jet\Consul;

use ArrayAccess;
use LogicException;
use RuntimeException;
use Huangdijia\Jet\Exception\Exception;

class Response implements ArrayAccess
{
    /**
     * The result coming from curl_getinfo().
     *
     * @var array
     */
    protected $info = [];

    /**
     * Response Content (Body).
     *
     * @var string
     */
    protected $content;

    /**
     * Response Headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Response Cookies.
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * The decoded JSON response.
     *
     * @var array
     */
    protected $decoded;

    public function __construct($ch)
    {
        // 执行curl
        $response = curl_exec($ch);

        if (!curl_errno($ch)) {
            $this->info    = curl_getinfo($ch);
            $this->headers = $this->parseHeaders($response, $this->info['header_size']);
            $this->content = $this->parseBody($response, $this->info['header_size']);
            $this->cookies = $this->parseCookies($this->header('Set-Cookie'));
        } else {
            throw new RuntimeException(curl_error($ch));
        }

        curl_close($ch);
    }

    /**
     * Make an CurlResponse
     * @param mixed $ch
     * @return Response
     */
    public static function make($ch)
    {
        return new self($ch);
    }

    /**
     * Get the information about this response,
     * including header, status code and content.
     *
     * @return array
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * Get the status code for this response instance.
     *
     * @return int
     */
    public function status()
    {
        return $this->info['http_code'];
    }

    /**
     * Determine if the request was successful.
     *
     * @return bool
     */
    public function successful()
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Determine if the response code was "OK".
     *
     * @return bool
     */
    public function ok()
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response was a redirect.
     *
     * @return bool
     */
    public function redirect()
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     *
     * @return bool
     */
    public function failed()
    {
        return $this->serverError() || $this->clientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     *
     * @return bool
     */
    public function clientError()
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     *
     * @return bool
     */
    public function serverError()
    {
        return $this->status() >= 500;
    }

    /**
     * Execute the given callback if there was a server or client error.
     *
     * @param  \Closure|callable $callback
     * @return $this
     */
    public function onError(callable $callback)
    {
        if ($this->failed()) {
            $callback($this);
        }

        return $this;
    }

    /**
     * Get the content type of this response instance.
     *
     * @return string
     */
    public function contentType()
    {
        return $this->info['content_type'];
    }

    /**
     * Get the body of the response.
     * @return string
     */
    public function body()
    {
        return $this->content;
    }

    /**
     * Parse the headers of this response instance.
     *
     * @param string $response
     * @param int $headerSize
     *
     * @return array
     */
    protected function parseHeaders(string $response, int $headerSize)
    {
        $headers       = substr($response, 0, $headerSize);
        $parsedHeaders = [];

        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, ':')) {
                $nestedHeader                              = explode(':', $header);
                $parsedHeaders[array_shift($nestedHeader)] = trim(implode(':', $nestedHeader));
            }
        }

        return $parsedHeaders;
    }

    /**
     * Parse the cookiess of this response instance.
     * @param string $cookieJar
     * @return array
     */
    protected function parseCookies(string $cookieJar = '')
    {
        $cookies = [];

        preg_match_all('/([^;]*)/mi', $cookieJar, $matches);

        foreach ((array) $matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        return $cookies;
    }

    /**
     * Get the headers of this response instance.
     *
     * @return array
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Get a specific header from the headers of this response instance.
     *
     * @param string $name
     *
     * @return string
     */
    public function header($name)
    {
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : null;
    }

    /**
     * Parses the body (content) out of the response.
     *
     * @param string $response
     * @param int $headerSize
     *
     * @return string
     */
    public function parseBody(string $response, int $headerSize)
    {
        return substr($response, $headerSize);
    }

    /**
     *
     * @param string|int|null $key
     * @param mixed $default
     * @return mixed
     * @throws Exception
     */
    public function json($key = null, $default = null)
    {
        if ($this->header('Content-Type') !== 'application/json') {
            throw new Exception('The Content-Type of response is not equal application/json');
        }

        $data = json_decode((string) $this->body(), true);

        if (!$key) {
            return $data;
        }

        return array_get($data, $key, $default);
    }

    /**
     * Get the JSON decoded body of the response as an object.
     *
     * @return object
     */
    public function object()
    {
        return json_decode($this->body(), false);
    }

    /**
     * Content encoded as XML.
     *
     * @return SimpleXMLElement
     */
    public function xml()
    {
        return new \SimpleXMLElement($this->body());
    }

    /**
     * Primary IP.
     *
     * @return string
     */
    public function ip()
    {
        return $this->info['primary_ip'];
    }

    /**
     * Throw an exception if a server or client error occurred.
     * @return $this
     * @throws Exception
     */
    public function throw()
    {
        if ($this->failed()) {
            throw new Exception($this->body());
        }

        return $this;
    }

    /**
     * Get the cookies of the response.
     * @return array
     */
    public function cookies()
    {
        return $this->cookies;
    }

    /**
     * Get the cookies of the response.
     * @param mixed|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function cookie($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->cookies;
        }

        return array_get($this->cookies, $key, $default);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $array = $this->json();

        return isset($array[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $array = $this->json();

        return isset($array[$offset]) ? $array[$offset] : null;
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetSet($offset, $value)
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     *
     * @throws \LogicException
     */
    public function offsetUnset($offset)
    {
        throw new LogicException('Response data may not be mutated using array access.');
    }

    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->body();
    }
}
