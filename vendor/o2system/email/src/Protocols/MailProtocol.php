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
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class MailProtocol
 *
 * @package O2System\Email\Protocols
 */
class MailProtocol extends Abstracts\AbstractProtocol
{
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
        $phpMailer->isMail();

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
            $this->addErrors([
                $phpMailer->ErrorInfo,
            ]);

            return false;
        }

        return true;
    }
}