<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends Controller
{
   public function __construct()
   {
       $this->middleware('auth');
   }
   
   public function index()
   {
      $YOUR_DOMAIN = 'http://localhost:8000';

      $items = \Cart::session(7)->getContent();
      $products = [];
      foreach ($items as $item) {

            $products[] = [
            'price_data' => [
              'currency' => 'eur',
              'unit_amount' => intval($item['price']*100),
              'product_data' => [
                 'name' => $item['name'],
                 'images' => [$YOUR_DOMAIN."/public/image/products/".$item['attributes']->image],
                      ],
            ],
            'quantity' => $item['quantity'],
         ];
      }

      Stripe::setApiKey('sk_test_51IAusCI6YwgpDkGgRmZowmNTpDQPHZRfJFOWrXJU4o3F7tTmeemAL21Ss81Z7PuPmuTchvw0Z8BVDemuYYLlNBOh00WFmn8iGO');

      $checkout_session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [$products],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.html',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
      ]);

      $response = new JsonResponse(['id' => $checkout_session->id]);
      return $response;
   }
}
