<?php 

namespace App\Services;

use APp\Exceptions\AppException;

use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketService 
{
    public bool | null $inProgress = null;
    public bool | null $resolved = null;
    public int | null $userId = null;

    public function getTickets($with=[])
    {
        return Ticket::with($with)
                ->when($this->userId != null, fn($q1) => $q1->where("user_id", $this->userId))
                ->when($this->inProgress != null, fn($q2) => $q2->where("in_progress", $this->inProgress))
                ->when($this->resolved != null, fn($q3) => $q3->where("resolved", $this->resolved))
                ->orderBy("created_at", "DESC")->get();
    }

    public function getTicket(int $id, array $with=[])
    {
        return Ticker::with($with)->where("id", $id)->first();
    }

    public function save(array $data)
    {
        $ticket = new Ticket;
        $ticket->user_Id = $data['userId'];
        $ticket->title = $data['title'];
        $ticket->content = $data['content'];
        $ticket->in_progress = true;
        $ticket->save();

        return $ticket;
    }

    public function resolve(int $id, int $adminId)
    {
        $ticket = $this->getTicket($id);
        if(!$ticket) throw new AppException(402, "Ticket not found");

        $ticket->resolved = true;
        $ticket->resolved_by = $adminId;
        $ticket->save();

        return $ticket;
    }

    public function sendMessage(array $data, int $ticketId)
    {
        $ticket = $this->getTicket($ticketId);
        if(!$ticket) throw new AppException(402, "Ticket not found");

        $data["ticket_id"] = $ticketId;
        return TicketMessage::create($data);
    }
}