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
 * Class Optimus
 * @package O2System\Image\Optimizers
 */
class Optimus
{
    /**
     * Optimus::$apiUrl
     *
     * @var string
     */
    private $apiUrl = 'https://api.optimus.io';

    /**
     * Optimus::$apiKey
     *
     * @var string
     */
    private $apiKey;

    // ------------------------------------------------------------------------

    /**
     * Optimus::__construct
     *
     * @param string|null $apiKey
     */
    public function __construct($apiKey = null)
    {
        if (isset($apiKey)) {
            $this->setApiKey($apiKey);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Optimus::setApiKey
     *
     * @param string $apiKey
     *
     * @return static
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = (string)$apiKey;

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

        $callApiUrl = $this->apiUrl . '/' . $this->apiKey . '?' . $option;

        $headers = [
            'User-Agent: Optimus-API',
            'Accept: image/*',
        ];

        $handle = curl_init();
        curl_setopt_array($handle, [
            CURLOPT_URL            => $callApiUrl,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => file_get_contents($image),
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
            throw new RuntimeException("Optimus-Error: {$curlError}, Output: {$body}");
        }

        return $body;
    }
}