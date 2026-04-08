<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicPageController extends Controller
{
    /**
     * Display the Privacy Policy page.
     */
    public function privacy()
    {
        return view('legal.privacy');
    }

    /**
     * Display the Terms of Service page.
     */
    public function terms()
    {
        return view('legal.terms');
    }

    /**
     * Display the Clinic Schedule page.
     */
    public function schedule()
    {
        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeek; // 0 (Sun) to 6 (Sat)
        $hour = $now->hour;
        $isOpen = false;

        // Monday (1) to Thursday (4): 8 AM to 6 PM
        if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
            if ($hour >= 8 && $hour < 18) {
                $isOpen = true;
            }
        } 
        // Friday (5): 8 AM to 5 PM
        elseif ($dayOfWeek == 5) {
            if ($hour >= 8 && $hour < 17) {
                $isOpen = true;
            }
        }

        return view('information.schedule', compact('isOpen'));
    }
}
