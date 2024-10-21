<?php

namespace App\Controller;

use Stripe\Webhook;
use App\Security\EmailVerifier;
use App\Service\HourCalculator;
use App\Service\PaymentService;
use Symfony\Component\Mime\Address;
use App\Repository\ReservationRepository;
use App\Service\EmailNotificationService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class PaymentController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('/payment/{id}', name: 'app_payment', methods: ['GET'])]
    public function time(HourCalculator $hourCalculator, ReservationRepository $rr, PaymentService $ps, int $id): Response
    {
        $reservaton = $rr->find(['id' => $id]);
        $totalTime = $hourCalculator->calculateTotalHours($reservaton->getStartDate()->format('Y-m-d'), $reservaton->getEndDate()->format('Y-m-d'), $reservaton->getStartTime()->format('H:i:s'), $reservaton->getEndTime()->format('H:i:s'));

        $ps->askCheckout($id);
        $payment = $ps->addPayment($id);

        return $this->render('payment/pay.html.twig', [
            'totalTime' => $totalTime,
            'payment' => $payment

        ]);
    }

    // Route lorsque le paiement est réussi
    #[Route('/payment-success/{id}', name: 'app_payment_success', methods: ['GET'])]
    public function paymentSuccess(Request $request, PaymentService $ps, int $id): Response
    {
        if ($request->headers->get('referer') === 'https://checkout.stripe.com/') {
            $reservation = $ps->addPayment($id);

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $this->getUser(),
                (new TemplatedEmail())
                    ->from(new Address('hall4all@email.com', 'hall4all'))
                    ->to((string) $this->getUser()->getEmail())
                    ->subject('Thank you for your Payment')
                    ->htmlTemplate('payment/paymentConfirmationMail.html.twig')
            );

            return $this->render('payment/payment-success.html.twig', [
                'reservation' => $reservation,
            ]);
        } else {
            $this->addFlash('error', "You can't take a reservation without a payment");
            return $this->redirectToRoute('app_subscription');
        }

        return $this->render('subscription/payment-success.html.twig');
    }

    // Route lorsque le paiement a échoué
    #[Route('/payment-cancel', name: 'app_payment_cancel')]
    public function paymentCancel(Request $request): Response
    {
        if ($request->headers->get('referer') === 'https://checkout.stripe.com/') {
            return $this->render('payment/payment-cancel.html.twig');
        } else {
            $this->addFlash('error', "You can't take a subscription without a payment");
            return $this->redirectToRoute('app_reservation_index');
        }
    }


    /**
     * Redirection vers le paiement Stripe
     * Ici on utilise la classe RedirectResponse de HttpFoundation
     * Cela nous donne accès à la méthos redirect qui génère la requête
     * à partir de la session initié avec PaymentService->askCheckout()
     **/
    #[Route('/payment/checkout/{id}', name: 'app_payment_checkout', methods: ['GET'])]
    public function checkout(PaymentService $ps, int $id): RedirectResponse
    {
        return $this->redirect($ps->askCheckout($id)->url);
    }

    /**
     * Check du statut de paiement
     */
    #[Route('/payment-webhook', name: 'app_stripe_webhook', methods: ['GET', 'POST'])]
    public function stripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endpointSecret = $this->getParameter('stripe_webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
            dd($event);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }
    }
}
