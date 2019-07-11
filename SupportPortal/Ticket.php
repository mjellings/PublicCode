<?php

namespace App;

use App\TicketStatus;
use App\TicketCategory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /* Relationships */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updates()
    {
        return $this->hasMany(TicketUpdate::class)->with('user')->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class, 'ticket_status_id');
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function impact()
    {
        return $this->belongsTo(TicketImpact::class, 'ticket_impact_id');
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::class, 'ticket_priority_id');
    }

    /* Scopes */
    public function scopeOpen($query)
    {
        return $query->where('ticket_status_id', '!=', 99);
    }

    public function scopeClosed($query)
    {
        return $query->where('ticket_status_id', '=', 99);
    }

    /* Attributes */
    public function getRefAttribute() {
        return str_pad($this->id,6,'0',STR_PAD_LEFT);
    }

    public function getTimeline() {
        $data = array();
        $c_date = '';
        foreach ($this->updates()->orderBy('created_at', 'DESC')->get() as $update) {
            $data[$update->created_at->format('jS F, Y')][] = $update;
        }
        dd($data);
        return $data;
    }

    public function getInitialNote() {
        return $this->updates()->where('type', 'NOTE')->orderBy('created_at', 'ASC')->first();
    }

    /* Check permissions */
    public function userCanView(User $user)
    {
        // user is admin
        if ($user->type == 'admin') {
            return true;
        } else {

            // user owns ticket
            if ($this->user_id == $user->id) {
                return true;
            } else {

                // Check if user has links to clients
                if (count($user->clients)) {
                    $return = false;

                    // Loop through each client
                    foreach ($user->clients as $client) {
                        

                        // Check if the client within the loop matches the one linked to the ticket
                        // and check if the user has view_all permissions.
                        if ($client->id == $this->client_id
                            && $client->pivot->tickets_view_all) {
                            // Set return variable to true
                            $return = true;
                        }

                        
                    }

                    return $return;
                } else {

                    // No clients listed, this user shouldn't be looking at his ticket.
                    return false;
                }
            }
        }
    }

    public function userCanUpdate(User $user)
    {
        // user is admin
        if ($user->type == 'admin') {
            return true;
        } else {

            // user owns ticket
            if ($this->user_id == $user->id) {
                return true;
            } else {

                // Check if user has links to clients
                if (count($user->clients)) {
                    $return = false;

                    // Loop through each client
                    foreach ($user->clients as $client) {
                        

                        // Check if the client within the loop matches the one linked to the ticket
                        // and check if the user has view_all permissions.
                        if ($client->id == $this->client_id
                            && $client->pivot->tickets_update_all) {
                            // Set return variable to true
                            $return = true;
                        }

                        
                    }

                    return $return;
                } else {

                    // No clients listed, this user shouldn't be looking at his ticket.
                    return false;
                }
            }
        }   
    }

    public function updateCategory($id = '') {
        if ($id) {
            if ($id == $this->category->id) {
                // No change in category needed
            }
            else {
                $category = TicketCategory::find($id);
                if ($category) {
                    $update = new TicketUpdate();
                    $update->ticket_id = $this->id;
                    $update->type = 'CATEGORY_CHANGED';
                    $update->user_id = \Auth::user()->id;
                    $update->details = 'Changed category from ' . $this->category->name .' to ' .$category->name;
                    $update->hidden = true;
                    $update->save();
                    $this->ticket_category_id = $id;
                    $this->save();
                }
            }
        }

        return $this;
    }

    public function updateStatus($id = '') {
        if ($id) {
            $status = TicketStatus::find($id);
            if ($id == $this->status->id) {
                // No change in status needed
            }
            else {
                if ($id == 99) {
                    $update = new TicketUpdate();
                    $update->ticket_id = $this->id;
                    $update->type = 'TICKET_CLOSED';
                    $update->user_id = \Auth::user()->id;
                    $update->details = '';
                    $update->save();
                    $this->ticket_status_id = 99;
                }
                elseif ($id == 11) {
                    $update = new TicketUpdate();
                    $update->ticket_id = $this->id;
                    $update->type = 'TICKET_REOPENED';
                    $update->user_id = \Auth::user()->id;
                    $update->details = '';
                    $update->save();
                    $this->ticket_status_id = 11;
                }
                else {
                    $update = new TicketUpdate();
                    $update->ticket_id = $this->id;
                    $update->type = 'STATUS_CHANGED';
                    $update->user_id = \Auth::user()->id;
                    $update->details = 'Changed status from ' . $this->status->title .' to ' . $status->title;
                    $update->save();
                    $this->ticket_status_id = $id;
                }
                $this->save();
            }
        }
        else {
            // No status given, ignore.
        }
        return $this;
    }
}
