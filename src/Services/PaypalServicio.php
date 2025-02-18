<?php 


namespace Services;

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\Payer;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;

class PayPalServicio {

    private $apiContext;

    /**
     * Constructor para inicializar las variables de configuración de paypal
     */
    public function __construct() {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $_ENV['PAYPAL_CLIENT_ID'],     
                $_ENV['PAYPAL_SECRET']         
            )
        );

        $this->apiContext->setConfig([
                'mode' => $_ENV['PAYPAL_API_URL'],
        ]);
    }

 
    /**
     * Metodo que crea un pago usando la libreria de paypal
     * @var int con el precio del pedido
     * @var string con el tipo de moneda
     * @var string con la descripcion del pago
     * @var string con la url a devolver para llamar a paypal y pagar
     * @var string con la url de cancelacion del pago
     * @return void
     */
    public function createPayment($totalAmount, $currency, $description, $returnUrl, $cancelUrl) {
        // Crear un objeto Payer
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Crear el precio total a pagar
        $amount = new Amount();
        $amount->setCurrency($currency)
            ->setTotal($totalAmount); 

        // Crear la transacción
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription($description);

        // URLs de redirección
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl);

        // Crear el objeto Payment
        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        try {
            // Crear el pago
            $payment->create($this->apiContext);

            // Redirigir al usuario a la URL de PayPal
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    return $link->getHref(); 
                }
            }
        } catch (PayPalConnectionException $e) {
            error_log("Error al crear el pago: " . $e->getData());
            return null;
        }
    }

    /**
     * Metodo que ejecuta el pago
     * @var id con el id del pago a realizar
     * @var id con el id de la persona que paga
     * @return void
     */
    public function executePayment($paymentId, $payerId) {
        try {
            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            // Ejecutar el pago
            $result = $payment->execute($execution, $this->apiContext);

            return true;
        } catch (PayPalConnectionException $e) {
            error_log("Error al ejecutar el pago: " . $e->getData());
            return false;
        }
    }
}