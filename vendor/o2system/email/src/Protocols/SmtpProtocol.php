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

namespace O2System\Email\Protocols;

// ------------------------------------------------------------------------

use O2System\Email\Address;
use O2System\Email\Message;
use O2System\Email\Spool;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class SmtpProtocol
 *
 * @package O2System\Email\Protocols
 */
class SmtpProtocol extends Abstracts\AbstractProtocol
{
    /**
     * SmtpProtocol::$config
     *
     * @var \O2System\Email\DataStructures\Config
     */
    protected $config;

    /**
     * SmtpProtocol::__construct
     *
     * @param \O2System\Email\Spool $spool
     */
    public function __construct(Spool $spool)
    {
        parent::__construct($spool);

        $this->config = $this->spool->getConfig();

        if ( ! $this->config->offsetExists('debug')) {
            /**
             * Debug output level.
             * Options:
             * `0` No output
             * `1` Commands
             * `2` Data and commands
             * `3` As 2 plus connection status
             * `4` Low-level data output.
             */
            $this->config[ 'debug' ] = 0;
        }

        if ( ! $this->config->offsetExists('auth')) {
            $this->config[ 'auth' ] = false;
            if ( ! empty($this->config[ 'username' ])) {
                $this->config[ 'auth' ] = true;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * MailProtocol::sending
     *
     * Protocol message sending process.
     *
     * @param Message $message
     *
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function sending(Message $message)
    {
        $phpMailer = new PHPMailer();
        $phpMailer->isSMTP();
        $phpMailer->SMTPDebug = $this->config[ 'debug' ];

        $host = $this->config[ 'host' ];

        if (is_array($this->config[ 'host' ])) {
            $host = implode(';', $this->config[ 'host' ]);
        }

        $phpMailer->Host = $host;
        $phpMailer->SMTPAuth = $this->config[ 'auth' ];
        $phpMailer->Username = $this->config[ 'username' ];
        $phpMailer->Password = $this->config[ 'password' ];
        $phpMailer->SMTPSecure = $this->config[ 'encryption' ];
        $phpMailer->Port = $this->config[ 'port' ];

        // Set from
        if (false !== ($from = $message->getFrom())) {
            $phpMailer->setFrom($from->getEmail(), $from->getName());
        }

        // Set recipient
        if (false !== ($to = $message->getTo())) {
            foreach ($to as $address) {
                if ($address instanceof Address) {
                    $phpMailer->addAddress($address->getEmail(), $address->getName());
                }
            }
        }

        // Set reply-to
        if (false !== ($replyTo = $message->getReplyTo())) {
            $phpMailer->addReplyTo($replyTo->getEmail(), $replyTo->getName());
        }

        // Set content-type
        if ($message->getContentType() === 'html') {
            $phpMailer->isHTML(true);
        }

        // Set subject, body & alt-body
        $phpMailer->Subject = $message->getSubject();
        $phpMailer->Body = $message->getBody();
        $phpMailer->AltBody = $message->getAltBody();

        if (false !== ($attachments = $message->getAttachments())) {
            foreach ($attachments as $filename => $attachment) {
                $phpMailer->addAttachment($attachment, $filename);
            }
        }

        if ( ! $phpMailer->send()) {
            $this->addError(100, $phpMailer->ErrorInfo);

            return false;
        }

        return true;
    }
}