<?php

namespace App\Http\Controllers;


use App\Traits\ApiResponder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    use ApiResponder, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
