<?php

namespace Omnipay\Vindicia\Message;

/**
 * Begin a PayPal purchase. A redirect URL will be returned to send the user to PayPal's
 * website.
 *
 * Uses all the same parameters as a regular purchase request. A card does not need to be
 * specified, but you may choose to in order to provide address or customer name information.
 * Providing a card number is meaningless and it will be ignored. See Message\PurchaseRequest
 * for parameter details.
 *
 * Requires two additional parameters:
 * - returnUrl: The URL to which the user should be redirected after they complete the
 * purchase on PayPal's site.
 * - cancelUrl: The URL to which the user should be redirected if they cancel their purchase
 * on PayPal's site.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_PayPal');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // create a customer (unlike many gateways, Vindicia requires a customer exist
 *   // before a transaction can occur)
 *   $customerResponse = $gateway->createCustomer(array(
 *       'name' => 'Test Customer',
 *       'email' => 'customer@example.com',
 *       'customerId' => '123456789'
 *   ))->send();
 *
 *   if ($customerResponse->isSuccessful()) {
 *       echo "Customer id: " . $customerResponse->getCustomerId() . PHP_EOL;
 *       echo "Customer reference: " . $customerResponse->getCustomerReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // now we start the purchase process
 *   $purchaseResponse = $gateway->purchase(array(
 *       'amount' => '9.99',
 *       'currency' => 'USD',
 *       'customerId' => $customerResponse->getCustomerId(), // could do this by customerReference also
 *       'card' => array(
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'pp-123456', // this ID will be assigned to the PayPal account
 *       'returnUrl' => 'http://www.example.com/order_summary,
 *       'cancelUrl' => 'http://www.example.com/cart
 *   ))->send();
 *
 *   if ($purchaseResponse->isSuccessful()) {
 *       echo "Transaction id: " . $purchaseResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $purchaseResponse->getTransactionReference() . PHP_EOL;
 *       echo "Redirecting to: " . $purchaseResponse->getRedirectUrl();
 *       $purchaseResponse->redirect();
 *   } else {
 *       // error handling
 *   }
 *
 *   // ... now the user is filling out info on PayPal's site. Then they get redirected back
 *   // to your site, where you will complete the purchase ...
 *
 *   // get the PayPal transaction reference from the URL
 *   $payPalTransactionReference = $_GET['vindicia_vid'];
 *
 *   // complete the purchase
 *   $completeResponse = $gateway->completePurchase(array(
 *       'success' => true,
 *       'payPalTransactionReference' => $payPalTransactionReference
 *   ))->send();
 *
 *   if ($completeResponse->isSuccessful()) {
 *       // these are the same transaction ids you saw after the purchase call
 *       echo "Transaction id: " . $completeResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $completeResponse->getTransactionReference() . PHP_EOL;
 *   }
 * </code>
 */
class PayPalPurchaseRequest extends PurchaseRequest
{
    /**
     * @psalm-suppress TooManyArguments because psalm can't see validate's func_get_args call
     */
    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $this->validate('returnUrl', 'cancelUrl');

        return parent::getData(self::PAYMENT_METHOD_PAYPAL);
    }

    /**
     * Use a special response object for PayPal purchase requests.
     *
     * @param object $response
     * @return PayPalPurchaseResponse
     */
    protected function buildResponse($response)
    {
        return new PayPalPurchaseResponse($this, $response);
    }
}
