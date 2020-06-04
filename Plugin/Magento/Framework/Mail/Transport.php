<?php

namespace Salecto\Mailgun\Plugin\Magento\Framework\Mail;

use Http\Adapter\Guzzle6\Client as HttpClient;
use Magento\Framework\Mail\TransportInterface;
use Mailgun\Mailgun;
use Mailgun\Message\MessageBuilder;
use Mailgun\Message\Exceptions\TooManyParameters;

class Transport
{
    /**
     * @var Config
     */
    private $mailgunConfig;

    /**
     * Transport constructor.
     *
     * @param Salecto\Mailgun\Helper\Config $config
     *
     * @throws InvalidArgumentException
     */
    
    public function __construct(\Salecto\Mailgun\Helper\Config $config)
    {
        $this->mailgunConfig = $config;
    }

    /**
     * @param \Magento\Framework\Mail\TransportInterface $subject
     * @param callable $proceed
     * @return callable $proceed|void
     */
    public function aroundSendMessage(
        \Magento\Framework\Mail\TransportInterface $subject,
        callable $proceed
    ) {
        if ($this->mailgunConfig->enabled()) {           
            try {
                $messageBuilder = $this->createMailgunMessage($this->parseMessage($subject->getMessage()));

                $mailgun = Mailgun::create($this->mailgunConfig->privateKey(), $this->mailgunConfig->endpoint());
                $mailgun->setApiVersion($this->mailgunConfig->version());
                $mailgun->setSslEnabled($this->mailgunConfig->ssl());

                $mailgun->sendMessage($this->mailgunConfig->domain(), $messageBuilder->getMessage(), $messageBuilder->getFiles());
            } catch (\Exception $e) {
                die($e);
            }
        } else {
            return $proceed();
        }
    }

    /**
     * @param \Magento\Framework\Mail\EmailMessage message
     * @return array
     */
    protected function parseMessage(\Magento\Framework\Mail\EmailMessage $message)
    {
        $parser = new \Salecto\Mailgun\Mail\MessageParser($message);

        return $parser->parse();
    }

    
    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return new HttpClient();
    }

    /**
     * @param array $message
     *
     * @return MessageBuilder
     * @throws TooManyParameters
     */
    protected function createMailgunMessage(array $message)
    {
        $builder = new MessageBuilder();
        $builder->setFromAddress(reset($message['from']));
        $builder->setSubject($message['subject']);
        foreach ($message['to'] as $to) {
            $builder->addToRecipient($to);
        }

        foreach ($message['cc'] as $cc) {
            $builder->addCcRecipient($cc);
        }

        foreach ($message['bcc'] as $bcc) {
            $builder->addBccRecipient($bcc);
        }

        if ($message['html']) {
            $builder->setHtmlBody($message['html']);
        }

        if ($message['text']) {
            $builder->setTextBody($message['text']);
        }

        foreach ($message['attachments'] as $attachment) {
            $tempPath = tempnam(sys_get_temp_dir(), 'attachment');
            file_put_contents($tempPath, $attachment->getRawContent());
            $builder->addAttachment($tempPath, $attachment->filename);
        }

        return $builder->getMessage();
    }
}
