<?php

namespace Webkul\Payment\Payment;

class Mpay extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'mpay';

    public function getRedirectUrl()
    {
        
    }
}