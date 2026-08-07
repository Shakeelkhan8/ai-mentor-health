<?php

namespace App\Http\Controllers\Web\Billing;

use App\Domains\Billing\Actions\HandleStripeWebhook;
use App\Domains\Billing\Contracts\BillingGateway;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends WebController
{
    public function __invoke(
        Request $request,
        BillingGateway $billingGateway,
        HandleStripeWebhook $handleStripeWebhook,
    ): JsonResponse {
        try {
            $event = $billingGateway->parseWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );
            $handleStripeWebhook->execute($event);
        } catch (DomainException $e) {
            Log::warning('Stripe webhook rejected', [
                'code' => $e->errorCode,
                'message' => $e->getMessage(),
            ]);

            $status = in_array($e->errorCode, ['stripe_signature_missing', 'stripe_signature_invalid'], true)
                ? 400
                : 422;

            return response()->json(['error' => $e->getMessage()], $status);
        }

        return response()->json(['received' => true]);
    }
}
