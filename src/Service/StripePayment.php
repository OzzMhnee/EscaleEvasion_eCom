<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePayment
{
    public function __construct(private string $stripeSecretKey)
    {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createCheckoutSession($reservation, $successUrl, $cancelUrl): Session
    {
        $price = floatval($reservation->getProduct()->getPrice());
        $nights = ($reservation->getEndDate()->getTimestamp() - $reservation->getStartDate()->getTimestamp()) / 86400;
        $totalPrice = $price * $nights;
        $acompte = round($totalPrice * 0.10, 2);

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Acompte réservation : ' . $reservation->getProduct()->getName(),
                    ],
                    'unit_amount' => intval($acompte * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'billing_address_collection' => 'required',
            'phone_number_collection' => [
                'enabled' => true,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'reservationId' => $reservation->getId(),
                    'clientName' => $reservation->getUser() ? $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName() : '',
                    'clientEmail' => $reservation->getUser() ? $reservation->getUser()->getEmail() : '',
                    'sejourDates' => $reservation->getStartDate()->format('d/m/Y') . ' - ' . $reservation->getEndDate()->format('d/m/Y'),
                    'logement' => $reservation->getProduct()->getName(),
                ],
            ],
        ]);
    }
}
