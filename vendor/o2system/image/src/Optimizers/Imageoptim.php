<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Image\Optimizers;

// ------------------------------------------------------------------------

use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class Imageoptim
 * @package O2System\Image\Optimizers
 */
class Imageoptim
{
    /**
     * Imageoptim::$apiUrl
     *
     * @var string
     */
    private $apiUrl = 'https://img.gs';

    /**
     * Imageoptim::$apiKey
     *
     * @var string
     */
    private $username;

    // ------------------------------------------------------------------------

    /**
     * Imageoptim::__construct
     *
     * @param string|null $username
     */
    public function __construct($username = null)
    {
        if (isset($username)) {
            $this->setUsername($username);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Imageoptim::setApiKey
     *
     * @param string $username
     *
     * @return static
     */
    public function setUsername($username)
    {
        $this->username = (string)$username;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Imageoptim::optimize
     *
     * @param string $image
     * @param string|null
     *
     * @return string
     * @throws \Exception
     */
    public function optimize($image, $option = null)
    {
        // optimize: image optimization in the same format
        // webp: converts the image to the WebP image format
        if ($option === null) {
            $option = 'optimize';
        }

        $callApiUrl = $this->apiUrl . '/' . $this->username . '/' . $option;

        $blob = null;
        if (strpos($image, 'http')) {
            $blob = $this->fromUrl($image, $callApiUrl);
        } elseif (is_file($image)) {
            $blob = $this->fromFile($image, $callApiUrl);
        }

        return $blob;
    }

    protected function fromUrl($image, $callApiUrl)
    {
        $headers = [
            'User-Agent: Imageoptim-API',
            'Accept: image/*',
        ];

        $handle = curl_init();
        curl_setopt_array($handle, [
            CURLOPT_URL            => $callApiUrl . '/' . $image,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($handle);
        $curlError = curl_error($handle);
        $header_size = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        // error catching
        if ( ! empty($curlError) || empty($body)) {
            throw new RuntimeException("Imageoptim-Error: {$curlError}, Output: {$body}");
        }

        return $body;
    }

    protected function fromFile($image, $callApiUrl)
    {
        $fileData = @file_get_contents($image);
        $contentHash = md5($image);
        $boundary = "XXX$contentHash";
        $nameEscaped = addslashes(basename($image));

        $options[ 'content' ] = "--$boundary\r\n" .
            "Content-Disposition: form-data; name=\"file\"; filename=\"{$nameEscaped}\"\r\n" .
            "Content-Type: application/octet-stream\r\n" .
            "Content-Transfer-Encoding: binary\r\n" .
            "\r\n$fileData\r\n--$boundary--";

        $options[ 'header' ] =
            "Accept: image/*,application/im2+json\r\n" .
            "User-Agent: ImageOptim-php/1.1 PHP/" . phpversion() .
            "Content-Length: " . strlen($options[ 'content' ]) . "\r\n" .
            "Content-MD5: $contentHash\r\n" .
            "Content-Type: multipart/form-data, boundary=$boundary\r\n";

        $options[ 'timeout' ] = 30;
        $options[ 'ignore_errors' ] = true;
        $options[ 'method' ] = 'POST';

        $stream = @fopen($callApiUrl, 'r', false, stream_context_create(['http' => $options]));
        if ( ! $stream) {
            $error = error_get_last();
            throw new RuntimeException("Can't send HTTPS request to: $callApiUrl\n" . ($error ? $error[ 'message' ] : ''));
        }
        $response = @stream_get_contents($stream);
        if ( ! $response) {
            $error = error_get_last();
            fclose($stream);
            throw new RuntimeException("Error reading HTTPS response from: $callApiUrl\n" . ($error ? $error[ 'message' ] : ''));
        }
        $meta = @stream_get_meta_data($stream);
        if ( ! $meta) {
            $error = error_get_last();
            fclose($stream);
            throw new RuntimeException("Error reading HTTPS response from: $callApiUrl\n" . ($error ? $error[ 'message' ] : ''));
        }
        fclose($stream);
        if ( ! $meta || ! isset($meta[ 'wrapper_data' ], $meta[ 'wrapper_data' ][ 0 ])) {
            throw new RuntimeException("Unable to read headers from HTTP request to: $callApiUrl");
        }
        if ( ! empty($meta[ 'timed_out' ])) {
            throw new RuntimeException("Request timed out: $callApiUrl", 504);
        }
        if ( ! preg_match('/HTTP\/[\d.]+ (\d+) (.*)/', $meta[ 'wrapper_data' ][ 0 ], $status)) {
            throw new RuntimeException("Unexpected response: " . $meta[ 'wrapper_data' ][ 0 ]);
        }
        $status = intval($status[ 1 ]);
        $errorMessage = $status[ 2 ];
        if ($response && preg_grep('/content-type:\s*application\/im2\+json/i', $meta[ 'wrapper_data' ])) {
            $json = @json_decode($response);
            if ($json) {
                if (isset($json->status)) {
                    $status = $json->status;
                }
                if (isset($json->error)) {
                    $errorMessage = $json->error;
                }
                if (isset($json->code) && $json->code === 'IM2ACCOUNT') {
                    throw new RuntimeException($errorMessage, $status);
                }
            }
        }
        if ($status >= 500) {
            throw new RuntimeException($errorMessage, $status);
        }
        if ($status == 404) {
            throw new RuntimeException("Could not find the image: {$image}", $status);
        }
        if ($status == 403) {
            throw new RuntimeException("Origin server denied access to {$image}", $status);
        }
        if ($status >= 400) {
            throw new RuntimeException($errorMessage, $status);
        }

        return $response;
    }
}