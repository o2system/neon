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

namespace O2System\Email;

// ------------------------------------------------------------------------

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Class Message
 *
 * @package O2System\Email
 */
class Message
{
    /**
     * Message::PRIORITY_HIGHEST
     *
     * Highest message priority.
     *
     * @var int
     */
    const PRIORITY_HIGHEST = 1;
    /**
     * Message::PRIORITY_HIGH
     *
     * High message priority.
     *
     * @var int
     */
    const PRIORITY_HIGH = 2;
    /**
     * Message::PRIORITY_NORMAL
     *
     * Normal message priority.
     *
     * @var int
     */
    const PRIORITY_NORMAL = 3;
    /**
     * Message::PRIORITY_HIGHEST
     *
     * Highest message priority.
     *
     * @var int
     */
    const PRIORITY_LOW = 4;
    /**
     * Message::PRIORITY_LOWEST
     *
     * Lowest message priority.
     *
     * @var int
     */
    const PRIORITY_LOWEST = 5;
    /**
     * Message::$from
     *
     * Email from.
     *
     * @var Address
     */
    protected $from;
    /**
     * Message::$replyTo
     *
     * Email reply-to.
     *
     * @var Address
     */
    protected $replyTo;
    /**
     * Message::$returnTo
     *
     * Email return path.
     *
     * @var string
     */
    protected $returnPath;
    /**
     * Message::$to
     *
     * Email to Receiver, or receivers of the mail.
     *
     * @var array
     */
    protected $to = [];
    /**
     * Message::$cc
     *
     * Email copy carbon of Receiver, or receivers of the mail.
     *
     * @var array
     */
    protected $cc = [];
    /**
     * Message::$bcc
     *
     * Email blank copy carbon of Receiver, or receivers of the mail.
     *
     * @var array
     */
    protected $bcc = [];
    /**
     * Message::$subscribers
     *
     * Email subscribers.
     *
     * @var array
     */
    protected $subscribers = [];
    /**
     * Message::$subject
     *
     * Subject of the email to be sent.
     *
     * @var string
     */
    protected $subject;
    /**
     * Message::$body
     *
     * Email message body to be sent.
     *
     * @var string
     */
    protected $body;
    /**
     * Message::$altBody
     *
     * Email message alternative body to be sent.
     *
     * @var string
     */
    protected $altBody;
    /**
     * Message::$headers
     *
     * Headers of the message to be sent.
     *
     * @var array
     */
    protected $headers = [];
    /**
     * Message::$attachments
     *
     * Email attachments.
     *
     * @var array
     */
    protected $attachments = [];
    /**
     * Message::$encoding
     *
     * Message encoding.
     *
     * @var string
     */
    protected $encoding = '8bit';
    /**
     * Message::$charset
     *
     * Character set (default: utf-8)
     *
     * @var string
     */
    protected $charset = 'utf-8';
    /**
     * Message::$mimeVersion
     *
     * Message mime version (default: 1.0).
     *
     * @var string
     */
    protected $mimeVersion = '1.0';
    /**
     * Message::$contentType
     *
     * Email body content type (default: text).
     *
     * @var string
     */
    protected $contentType = 'text';
    /**
     * Message::$priority
     *
     * Email priority
     *
     * @var string
     */
    protected $priority;
    /**
     * Message::$batchLimit
     *
     * Email sending batch limit
     *
     * @var int
     */
    protected $batchLimit = 100;


    // ------------------------------------------------------------------------

    /**
     * Message::getEncoding
     *
     * Gets message encoding.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::setCharset
     *
     * Sets mail charset.
     *
     * @param string $charset
     *
     * @return static
     */
    public function charset($charset)
    {
        /**
         * Character sets valid for 7-bit encoding,
         * excluding language suffix.
         */
        $baseCharsets = [
            'us-ascii',
            'iso-2022-',
        ];

        foreach ($baseCharsets as $baseCharset) {
            if (strpos($baseCharset, $charset) === 0) {
                $this->encoding('7bit');
                break;
            }
        }

        $this->charset = $charset;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::encoding
     *
     * Sets message encoding.
     *
     * @param string $encoding
     *
     * @return static
     */
    public function encoding($encoding)
    {
        /**
         * Valid mail encodings
         */
        $bitDepths = [
            '7bit',
            '8bit',
        ];

        in_array($encoding, $bitDepths) OR $encoding = '8bit';

        $this->encoding = $encoding;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getCharset
     *
     * Gets mail charset.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::setMimeVersion
     *
     * Sets mail mime version.
     *
     * @param string $mimeVersion
     *
     * @return static
     */
    public function mimeVersion($mimeVersion)
    {
        $this->mimeVersion = $mimeVersion;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getMimeVersion
     *
     * Gets mail mime version.
     *
     * @return string
     */
    public function getMimeVersion()
    {
        return $this->mimeVersion;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::contentType
     *
     * Sets message content type.
     *
     * @param string $contentType
     *
     * @return static
     */
    public function contentType($contentType)
    {

        $this->contentType = $contentType;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getContentType
     *
     * Gets message content type.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::from
     *
     * Sets mail from.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function from($email, $name = null)
    {
        $this->setAddress($email, $name, 'from');

        if (empty($this->replyTo)) {
            $this->replyTo = $this->from;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::setFrom
     *
     * Sets mail from.
     *
     * @param string $email
     * @param string $name
     * @param string $object from | replyTo
     *
     * @return void
     */
    protected function setAddress($email, $name = null, $object)
    {
        if ($email instanceof Address) {
            $this->{$object} = $email;
        } else {
            $this->{$object} = new Address($email, $name);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getFrom
     *
     * Gets from mail address.
     *
     * @return \O2System\Email\Address|bool
     */
    public function getFrom()
    {
        if ($this->from instanceof Address) {
            return $this->from;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::replyTo
     *
     * Sets reply to mail address.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function replyTo($email, $name = null)
    {
        $this->setAddress($email, $name, 'replyTo');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getReplyTo
     *
     * Gets reply to mail address.
     *
     * @return \O2System\Email\Address|bool
     */
    public function getReplyTo()
    {
        if ($this->replyTo instanceof Address) {
            return $this->replyTo;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::returnPath
     *
     * Sets return to mail address.
     *
     * @param string $path
     *
     * @return static
     */
    public function returnPath($path)
    {
        if (strpos($path, '@') !== false) {
            $this->returnPath = strstr($path, '@');
        } else {
            $this->returnPath = '@' . $path;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getReturnTo
     *
     * Gets return to mail address.
     *
     * @return \O2System\Email\Address|bool
     */
    public function getReturnPath()
    {
        if (empty($this->returnPath)) {
            if ($this->from instanceof Address) {
                return $this->returnPath = strstr($this->from->getEmail(), '@');
            }

            return false;
        }

        return $this->returnPath;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::subject
     *
     * Sets mail subject.
     *
     * @param string $subject
     *
     * @return static
     */
    public function subject($subject)
    {
        $this->subject = trim($subject);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getSubject
     *
     * Gets mail subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::body
     *
     * Sets message body.
     *
     * @param string $body
     *
     * @return static
     */
    public function body($body)
    {
        $this->body = rtrim(str_replace("\r", '', $body));

        /* strip slashes only if magic quotes is ON
           if we do it with magic quotes OFF, it strips real, user-inputted chars.

           NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
             it will probably not exist in future versions at all.
        */
        if ( ! is_php('5.4') && get_magic_quotes_gpc()) {
            $this->body = stripslashes($this->body);
        }

        if (class_exists('O2System\Framework', false)) {
            $this->body = presenter()->assets->parseSourceCode($this->body);
        }

        $cssToInlineStyles = new CssToInlineStyles();
        $this->body = $cssToInlineStyles->convert($this->body);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getBody
     *
     * Gets mail body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::altBody
     *
     * Sets message alternative body.
     *
     * @param string $body
     *
     * @return static
     */
    public function altBody($altBody)
    {
        $this->altBody = trim($altBody);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getAltBody
     *
     * Gets mail body.
     *
     * @return string
     */
    public function getAltBody()
    {
        if (is_html($this->body)) {
            $body = preg_match('/\<body.*?\>(.*)\<\/body\>/si', $this->body, $match) ? $match[ 1 ] : $this->body;
            $body = str_replace("\t", '', preg_replace('#<!--(.*)--\>#', '', trim(strip_tags($body))));

            for ($i = 20; $i >= 3; $i--) {
                $body = str_replace(str_repeat("\n", $i), "\n\n", $body);
            }

            // Reduce multiple spaces
            $body = preg_replace('| +|', ' ', $body);

            return $this->body;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::priority
     *
     * Sets mail priority
     *
     * @param int $priority
     */
    public function priority($priority)
    {
        $priorities = [
            1 => '1 (Highest)',
            2 => '2 (High)',
            3 => '3 (Normal)',
            4 => '4 (Low)',
            5 => '5 (Lowest)',
        ];

        if (array_key_exists($priority, $priorities)) {
            $this->priority = $priorities[ $priority ];
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getPriority
     *
     * Gets mail priority.
     *
     * @return string|bool
     */
    public function getPriority()
    {
        if ( ! empty($this->priority)) {
            return $this->priority;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::addHeader
     *
     * Add additional mail header.
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function addHeader($name, $value)
    {
        $this->headers[ $name ] = $value;

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getHeaders
     *
     * Gets message additional headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::addTo
     *
     * Add to mail.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function to($email, $name = null)
    {
        $this->addAddress($email, $name, 'to');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::addAddress
     *
     * Add mail address to object.
     *
     * @param string      $email
     * @param string|null $name
     * @param string      $object
     *
     * @return void
     */
    protected function addAddress($email, $name = null, $object)
    {
        if ($email instanceof Address) {
            $this->{$object}[] = $email;
        } elseif (is_array($email)) {

            if (is_numeric(key($email))) {
                foreach ($email as $address) {
                    $this->{$object}[] = new Address($address);
                }
            } else {
                foreach ($email as $name => $address) {
                    $this->{$object}[] = new Address($address, $name);
                }
            }

        } elseif (strpos($email, ',') !== false) {
            $emails = preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($emails as $email) {
                $this->{$object}[] = new Address($email);
            }
        } else {
            $this->{$object}[] = new Address($email, $name);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getTo
     *
     * Gets mail to addresses.
     *
     * @return array|bool
     */
    public function getTo()
    {
        if (count($this->to)) {
            return $this->to;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::addCc
     *
     * Add to copy carbon mail.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function cc($email, $name = null)
    {
        $this->addAddress($email, $name, 'cc');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getCc
     *
     * Gets mail cc addresses.
     *
     * @return array|bool
     */
    public function getCc()
    {
        if (count($this->cc)) {
            return $this->cc;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::addBcc
     *
     * Add to blank copy carbon mail.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function bcc($email, $name = null, $limit = 100)
    {
        $this->batchLimit = (int)$limit;
        $this->addAddress($email, $name, 'bcc');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getBcc
     *
     * Gets mail bcc addresses.
     *
     * @return array|bool
     */
    public function getBcc()
    {
        if (count($this->bcc)) {
            return $this->bcc;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::subscriber
     *
     * Add mailing list subscribers.
     *
     * @param array $email
     * @param int   $limit
     *
     * @return static
     */
    public function subscribers($email, $limit = 100)
    {
        $this->batchLimit = (int)$limit;
        $this->addAddress($email, null, 'subscribers');

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getSubscribers
     *
     * Gets mail subscribers addresses.
     *
     * @return array|bool
     */
    public function getSubscribers()
    {
        if (count($this->subscribers)) {
            return $this->subscribers;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::addAttachment
     *
     * Add mail attachment.
     *
     * @param string      $attachment
     * @param string|null $filename
     *
     * @return bool
     */
    public function addAttachment($attachment, $filename = null)
    {
        if (is_file($attachment)) {
            $filename = isset($filename) ? $filename : pathinfo($attachment, PATHINFO_BASENAME);

            if ( ! in_array($attachment, $this->attachments)) {
                $this->attachments[ $filename ] = $attachment;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Message::getAttachments
     *
     * Gets mail attachments.
     *
     * @return array|bool
     */
    public function getAttachments()
    {
        if (count($this->attachments)) {
            return $this->attachments;
        }

        return false;
    }
}