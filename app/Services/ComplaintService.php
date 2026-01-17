<?php 

namespace App\Services;

use App\Exceptions\AppException;

use App\Models\Complaint;

class ComplaintService 
{
    public $targetId = null;
    public $targetType = null;
    public $referenceId = null;
    public $referenceType = null;
    public $userId = null;
    public $count = [];

    public function save($data)
    {
        try{
            $complaint = new Complaint; 
            $complaint->user_id = $data['userId'];
            $complaint->target_id = $data['targetId'];
            $complaint->target_type = $data['targetType'];
            if(isset($data['referenceId'])) $complaint->reference_id = $data['referenceId'];
            if(isset($data['referenceType'])) $complaint->reference_type = $data['referenceType'];
            $complaint->title = $data['title'];
            $complaint->content = $data['content'];
            $complaint->save();

            return $complaint;
        } catch(\Exception $e) {
            throw new AppException(500, "An Error occurred when saving a review", $e);
        }
    }

    public function complaints($with=[])
    {
        $query = Complaint::with($with)->withCount($this->count);
        if($this->targetId && $this->targetType) $query = $query->where("target_id", $this->targetId)->where("target_type", $this->targetType);
        if($this->referenceId && $this->referenceType) $query = $query->where("reference_id", $this->referenceId)->where("reference_type", $this->referenceType);
        if($this->userId) $query = $query->where("user_id", $this->userId);
        
        return $query->orderBy("created_at", "DESC")->get();
    }

    public function getComplaint($complaintId, $with=[])
    {
        return Complaint::with($with)->where("id", $complaintId)->first();
    }

    public function closeComplaint($complaintId, $adminId)
    {
        $complaint = $this->getComplaint($complaintId);
        if(!$complaint) throw new AppException(402, "Complaint not found");

        $complaint->closed = true;
        $complaint->closed_by = $adminId;
        $complaint->save();

        return $complaint;
    }

}