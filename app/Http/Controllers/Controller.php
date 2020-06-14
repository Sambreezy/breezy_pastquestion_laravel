<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\ApiResponderTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ApiResponderTrait;

    // Admin Level Token
    protected $USER_LEVEL_3 = 'SuPeRuPeraDmIn';
    protected $USER_LEVEL_2 = 'TeMpOrAlAdMins';
    protected $USER_LEVEL_1 = 'user';
}
