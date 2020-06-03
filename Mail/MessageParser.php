<?php

namespace Salecto\Mailgun\Mail;


use Magento\Framework\Mail\EmailMessageInterface;

class MessageParser
{

    /**
     * @var \Magento\Framework\Mail\EmailMessageInterface|\Magento\Framework\Mail\Message
     */
    protected $message;

    /**
     * @param \Magento\Framework\Mail\EmailMessageInterface $message
     */
    public function __construct(EmailMessageInterface $message)
    {
        $this->message = \Zend\Mail\Message::fromString($message->getRawMessage());
    }

    /**
     * @return array
     */
    public function parse()
    {
        $headerContentType = $this->message->getHeaders()->get('content-type');

        $contentType = $headerContentType ? $headerContentType->getType() : 'text';

        if ($contentType === 'text/html') {
            $content = $this->message->getBody();
        } else {
            $content = $this->message->getBody();
        }

        $content = quoted_printable_decode($content);

        $attachments = [];

        if ($this->message->getBody() instanceof Mime\Message) {
            foreach ($this->message->getBody()->getParts() as /** @var \Laminas\Mime\Part $part */ $part) {
                if ($part->disposition == 'attachment') {
                    $attachments[] = $part;
                }
            }
        }

        return [
            'from' => $this->parseRecipients($this->message->getFrom()),
            'reply-to' => $this->parseRecipients($this->message->getReplyTo()),
            'to' => $this->parseRecipients($this->message->getTo()),
            'cc' => $this->parseRecipients($this->message->getCc()),
            'bcc' => $this->parseRecipients($this->message->getBcc()),
            'subject' => $this->message->getSubject(),
            'html' => $contentType !== 'text' ? $content : null,
            'text' => $contentType === 'text' ? $content : null,
            'attachments' => $attachments,
        ];
    }

    /**
     * @param \Zend\Mail\Header\AbstractAddressList $recipients
     *
     * @return array
     */
    protected function parseRecipients($recipients)
    {
        $result = [];

        foreach ($recipients as $recipient) {
            $result[] = $recipient->getEmail();
        }

        return $result;
    }
}
