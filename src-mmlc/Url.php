<?php

namespace Grandeljay\WordpressIntegration;

class Url
{
    private string $scheme;
    private string $hostname;
    private int $port;
    private string $username;
    private string $password;
    private string $path;
    private string $query;
    private string $fragment;

    private array $request_headers;
    private string $request_body;

    public function __construct(string $url)
    {
        $this->parseUrl($url);
    }

    private function parseUrl(string $url): void
    {
        $parts = \parse_url($url);

        if (isset($parts['scheme'])) {
            $this->scheme = $parts['scheme'];
        }

        if (isset($parts['host'])) {
            $this->hostname = $parts['host'];
        }

        if (isset($parts['port'])) {
            $this->port = $parts['port'];
        }

        if (isset($parts['user'])) {
            $this->username = $parts['user'];
        }

        if (isset($parts['pass'])) {
            $this->password = $parts['pass'];
        }

        if (isset($parts['path'])) {
            $this->path = $parts['path'];
        }

        $this->query = $parts['query'] ?? '';

        if (isset($parts['fragment'])) {
            $this->fragment = $parts['fragment'];
        }
    }

    public function addParameters(array $parameters_to_add): void
    {
        \parse_str($this->query, $paramaters);

        $paramaters = \array_merge($paramaters, $parameters_to_add);

        $this->query = \http_build_query($paramaters);
    }

    public function addDefaultParameters(): void
    {
        $parameters_to_add = [
            'language' => $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE,
        ];

        $this->addParameters($parameters_to_add);
    }

    public function makeRequest(): void
    {
        $request_url  = $this->toString();
        $curl_handler = \curl_init($request_url);

        \curl_setopt($curl_handler, \CURLOPT_HEADER, true);
        \curl_setopt($curl_handler, \CURLOPT_RETURNTRANSFER, true);

        $curl_response = \curl_exec($curl_handler);

        if (\curl_error($curl_handler)) {
        }

        $curl_response_header_size = \curl_getinfo($curl_handler, \CURLINFO_HEADER_SIZE);

        \curl_close($curl_handler);

        $this->request_headers = $this->_getRequestHeaders($curl_response, $curl_response_header_size);
        $this->request_body    = $this->_getRequestBody($curl_response, $curl_response_header_size);
    }

    private function _getRequestHeaders(string $curl_response, int $curl_response_header_size): array
    {
        $curl_response_headers_string = \substr($curl_response, 0, $curl_response_header_size);
        $curl_response_headers_array  = \explode(\PHP_EOL, $curl_response_headers_string);
        $curl_response_headers_array  = \array_filter($curl_response_headers_array);

        $curl_response_headers = [];

        foreach ($curl_response_headers_array as $key => $value) {
            $colon_position = \strpos($value, ':');

            if (false !== $colon_position) {
                $key   = \substr($value, 0, $colon_position);
                $value = \substr($value, $colon_position + 2);

                $curl_response_headers[$key] = $value;
            } else {
                $http_version_and_status = \explode(' ', \trim($value));
                $http_version            = $http_version_and_status[0];
                $http_status             = $http_version_and_status[1];

                $curl_response_headers['version'] = $http_version;
                $curl_response_headers['status']  = $http_status;
            }
        }

        return $curl_response_headers;
    }

    public function getRequestHeaders(): array
    {
        return $this->request_headers;
    }

    private function _getRequestBody(string $curl_response, int $curl_response_header_size): string
    {
        $curl_response_body = \substr($curl_response, $curl_response_header_size);

        return $curl_response_body;
    }

    public function getRequestBody(): array
    {
        $decoded_request_body = \json_decode($this->request_body, true);

        return $decoded_request_body;
    }

    public function isRequestSuccessful(): bool
    {
        $response_code = $this->request_headers['status'] ?? 0;
        $success       = $response_code >= 200 && $response_code < 400;

        return $success;
    }

    public function toString(): string
    {
        $url = '';

        if (isset($this->scheme)) {
            $url .= $this->scheme . '://';
        }

        if (isset($this->hostname)) {
            $url .= $this->hostname;
        }

        if (isset($this->port)) {
            $url .= ':' . $this->port;
        }

        if (isset($this->username)) {
        }

        if (isset($this->password)) {
        }

        if (isset($this->path)) {
            $url .= $this->path;
        }

        if (isset($this->query)) {
            $url .= '?' . $this->query;
        }

        if (isset($this->fragment)) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }
}
