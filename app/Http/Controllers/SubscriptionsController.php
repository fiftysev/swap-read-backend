<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    public function get_followers() {
        return Subscription::with('follower')->get('follower');
    }
}
