<?php

namespace App\Http\Controllers\Web\Billing;

use App\Domains\Billing\Actions\StartSubscriptionCheckout;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BillingController extends WebController
{
    public function show(): View
    {
        $user = auth()->user();

        return view('backend_app.billing.index', [
            'productName' => config('mentor.name'),
            'subscription' => $user->activeSubscription(),
            'latestSubscription' => $user->subscriptions()->latest('id')->first(),
            'priceConfigured' => filled(config('services.stripe.price_id')),
            'disclaimer' => config('mentor.disclaimer'),
        ]);
    }

    public function checkout(StartSubscriptionCheckout $startSubscriptionCheckout): RedirectResponse
    {
        try {
            $session = $startSubscriptionCheckout->execute(auth()->user());
        } catch (DomainException $e) {
            return back()->withErrors(['billing' => $e->getMessage()]);
        }

        return redirect()->away($session['url']);
    }

    public function success(): View
    {
        return view('backend_app.billing.success', [
            'productName' => config('mentor.name'),
            'subscription' => auth()->user()->activeSubscription(),
        ]);
    }

    public function cancel(): View
    {
        return view('backend_app.billing.cancel', [
            'productName' => config('mentor.name'),
        ]);
    }
}
