<?php

namespace Salecto\Mailgun\Plugin\Mail\Template;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderByStore;

class TransportBuilderByStorePlugin
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * TransportBuilderByStorePlugin constructor.
     *
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(TransportBuilder $transportBuilder)
    {
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param TransportBuilderByStore $subject
     * @param TransportBuilderByStore $result
     * @param string $from
     *
     * @return TransportBuilderByStore
     */
    public function afterSetFromByStore(TransportBuilderByStore $subject, TransportBuilderByStore $result, string $from)
    {
        $this->transportBuilder->setFrom($from);

        return $result;
    }
}
