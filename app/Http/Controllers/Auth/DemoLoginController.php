<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * "Try the demo": signs the visitor into the shared demo account.
 * Exists only when DEMO_MODE is on; otherwise the route returns 404.
 */
class DemoLoginController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless(config('mentor.demo.enabled'), 404);

        $demo = User::query()->where('email', config('mentor.demo.email'))->first();
        abort_if($demo === null, 404);

        Auth::login($demo);
        $request->session()->regenerate();

        return redirect(RouteServiceProvider::HOME);
    }
}
