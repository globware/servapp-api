<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\AppException;

use App\Http\Requests\CreateTicket;
use App\Http\Requests\SaveTicketMessage;

use App\Http\Resources\TicketResource;

use App\Services\TicketService;

use App\Utilities;

class TicketController extends Controller
{
    //
}
