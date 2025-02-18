<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function trial(Request $request)
    {
        $plans = Plan::orderBy('access_level')->get();

        return Inertia::render('Setting/Billing/Trial', [
            'plans' => $plans
        ]);
    }

    public function index(Request $request)
    {
        return Inertia::render('Setting/Billing/Index');
    }

    public function checkout(Request $request, $id)
    {
        $workspace = $request->user()->currentWorkspace;

        // create a stripe customer
        $workspace->createOrGetStripeCustomer();

        // get plan
        $plan = Plan::where('id', $id)->firstOrFail();

        return $workspace
            ->newSubscription('default', $plan->stripe_id)
            ->trialDays(8)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('setting.billing.checkout-success'),
                'cancel_url' => route('setting.billing.index'),
            ]);
    }

    public function billingPortal(Request $request)
    {
        return Inertia::location($request->user()->currentWorkspace->redirectToBillingPortal(route('setting.billing.index')));
    }

    public function checkoutSuccess(Request $request)
    {
        return Inertia::render('Setting/Billing/Success');
    }
}
