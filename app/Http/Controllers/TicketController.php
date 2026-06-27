<?php

namespace App\Http\Controllers;

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
    public function __construct(protected TicketService $ticketService)
    {
    }

    public function create(CreateTicket $request)
    {
        try{
            $data = $request->validated();
            $data['userId'] = Auth::user()->id;

            $ticket = $this->ticketService->save($data);

            return Utilities::ok(new TicketResource($ticket));

        }catch (AppException $e) {
            throw $e;
        } catch (\Exception $e) {
            return Utilities::error($e, "An error occurred while attempting to create Ticket");
        }
    }

    public function tickets(Request $request)
    {
        $resolved = $request->query('resolved', null);

        if($resolved == true) $this->ticketService->resolved = true;
        if($resolved == false) $this->ticketService->inProgress = true;

        $this->ticketService->userId = Auth::user()->id;
        $tickets = $this->ticketService->getTickets();

        return Utilities::ok(TicketResource::collection($tickets));
    }

    public function ticket(int $ticketId)
    {
        $ticket = $this->ticketService->getTicket($ticketId, ['messages']);
        if(!$ticket) return Utilities::error402("Ticket not found");

        return Utilities::ok(new TicketResource($ticket));
    }

    public function sendMessage(SaveTicketMessage $request, int $ticketId)
    {
        try{
            $data = $request->validated();
            $this->ticketService->sendMessage($data, $ticketId);
            
            return Utilities::okay("Message sent Successfully");
        }catch (AppException $e) {
            throw $e;
        } catch (\Exception $e) {
            return Utilities::error($e, "An error occurred while attempting to create Ticket");
        }
    }
}
