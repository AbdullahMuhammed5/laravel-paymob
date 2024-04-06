<?php

namespace ctf0\PayMob\Integrations;

use Illuminate\Support\Facades\Http;

class Fawry extends Accept
{
    public function getPaymentTypeName(): string
    {
        return 'fawry';
    }

    /**
     * finish checkout process.
     *
     * https://acceptdocs.paymobsolutions.com/docs/kiosk-payments
     *
     * @param float|int $total
     * @param array     $items
     */
    public function checkOut($total, $merchant_order_id, $items = [])
    {
        $this->checkForMinAmount($total);

        $url      = $this->getConfigKey($this->getPaymentTypeConfig('url'));
        $order_id = $this->orderRegistration($items, $total, $merchant_order_id);

        $response = Http::withToken($this->auth_token)
            ->post($url, [
                'source' => [
                    'identifier' => 'AGGREGATOR',
                    'subtype'    => 'AGGREGATOR',
                ],
                'payment_token' => $this->paymentKeyRequest($order_id, $total),
            ])
            ->throw();

        return $response['redirect_url'] ?: '';
    }
}
