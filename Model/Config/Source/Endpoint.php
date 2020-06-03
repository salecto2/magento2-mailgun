<?php

namespace Salecto\Mailgun\Model\Config\Source;

class Endpoint implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'api.mailgun.net', 'label' => __('Mailgun US API (live)')],
            ['value' => 'api.eu.mailgun.net', 'label' => __('Mailgun EU API (live)')],
            ['value' => 'bin.mailgun.net', 'label' => __('Mailgun Postbin (debug)')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'api.mailgun.net' => __('Mailgun US API (live)'),
            'api.eu.mailgun.net' => __('Mailgun EU API (live)'),
            'bin.mailgun.net' => __('Mailgun Postbin (debug)')
        ];
    }
}
