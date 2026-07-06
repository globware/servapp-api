<?php

namespace App\Services;

use App\Exceptions\AppException;

use App\Models\Ticket;
use App\Models\TicketMessage;

class TicketService
{
    public bool | null $inProgress = null;
    public bool | null $resolved = null;
    public int | null $userId = null;

    public function getTickets($with = [])
    {
        return Ticket::with($with)
            ->when($this->userId != null, fn($q1) => $q1->where("user_id", $this->userId))
            ->when($this->inProgress != null, fn($q2) => $q2->where("in_progress", $this->inProgress))
            ->when($this->resolved != null, fn($q3) => $q3->where("resolved", $this->resolved))
            ->orderBy("created_at", "DESC")->get();
    }

    public function getTicket(int $id, array $with = [])
    {
        return Ticket::with($with)->where("id", $id)->first();
    }

    public function save(array $data)
    {
        $ticket = new Ticket;
        $ticket->user_id = $data['userId'];
        $ticket->title = $data['title'];
        $ticket->content = $data['content'];
        $ticket->in_progress = true;
        $ticket->save();

        return $ticket;
    }

    public function resolve(int $id, int $adminId)
    {
        $ticket = $this->getTicket($id);
        if (!$ticket) throw new AppException(402, "Ticket not found");

        $ticket->resolved = true;
        $ticket->in_progress = false;
        $ticket->resolved_by = $adminId;
        $ticket->save();

        return $ticket;
    }

    public function sendMessage(array $data, int $ticketId, int $userId)
    {
        $ticket = $this->getTicket($ticketId);
        if (!$ticket) throw new AppException(402, "Ticket not found");

        if ($ticket->user_id != $userId) throw new AppException(402, "You are not authorized to perform this action");

        $data["ticket_id"] = $ticketId;
        return TicketMessage::create($data);
    }
}
