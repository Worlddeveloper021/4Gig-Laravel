<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Paypal\PayPal;

class PaypalTest extends TestCase
{
    /** @test */
    public function ensure_it_works_fine()
    {
        $paypal = new PayPal;

        $paypal->getAccessToken();
        $response = $paypal->getPaymentDetails('PAYID-MIEEYEQ9AT89072W84764009');

        dd($response, $response['state']);
    }
}
