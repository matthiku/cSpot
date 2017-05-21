<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Item;

use Carbon\Carbon;
use Auth;


class Plan extends Model
{
    // mass assignment protection
	protected $fillable = [
		'date',
		'start',
		'end',
		'date_end',
		'leader_id',
		'teacher_id',
		'type_id',
		'info',
		'state',
        'changer',
		'private',
		'subtitle',
		'updated_at',
	];
	protected $hidden = [
		'created_at'
	];
	protected $dates = [
		'date', 'date_end', 'created_at', 'updated_at'
	];



	public function isFuture()
	{
		return $this->date > Carbon::yesterday();
	}



	// the leader_id points to the id on the users table
	public function leader()
	{
		return $this->belongsTo('App\Models\User', 'leader_id');
	}

	// the teacher_id points to the id on the users table
	public function teacher()
	{
		return $this->belongsTo('App\Models\User', 'teacher_id');
	}

	// the type_id points to the id on the types table (the type of service of this plan)
	public function type()
	{
		return $this->belongsTo('App\Models\Type');
	}


    /**
     * Many-to-many relationship with resources table
     *
     * (Allow individual comment for each resource assigned to a plan)
     */
    public function resources()
    {
        return $this->belongsToMany('App\Models\Resource')->withPivot('id', 'comment')->withTimestamps();
    }



    public function items()
    {
        return $this->hasMany('App\Models\Item');
    }


    public function teams()
    {
        return $this->hasMany('App\Models\Team');
    }


    public function planCaches()
    {
        return $this->hasMany('App\Models\PlanCache');
    }


    public function histories()
    {
        return $this->hasMany('App\Models\History');
    }


    public function notes()
    {
        return $this->hasMany('App\Models\Note');
    }


    /**
     * return true or false if current user (should be the leader) has already seen all notes for his plan
     * (each note has a field 'read_by_leader' indicating this)
     */
    public function notesRead()
    {
        return $notes = $this->notes->where('read_by_leader', false)->count();
    }


    /*  get all or just the first or just last item for this plan
    */
    public function allItems()
    {
        $items = Item::where('plan_id', $this->id);
        // Users without ownership of this plan won't see the FLEO items
        if ( ! Auth::User()->ownsPlan($this->id) )
            $items = $items
                ->where('forLeadersEyesOnly', false);

        return $items->orderBy('seq_no')->get();
    }
    public function firstItem()
    {
        // Users without ownership of this plan won't see the FLEO items
        if ( Auth::User()->ownsPlan($this->id) )
            $items = $this->items;
        else
            $items = $this->items
                ->where('forLeadersEyesOnly', false);

        return $items->sortBy('seq_no')->first();
    }
    public function lastItem()
    {
        // Users without ownership of this plan won't see the FLEO items
        if ( Auth::User()->ownsPlan($this->id) )
            $items = $this->items;
        else
            $items = $this->items
                ->where('forLeadersEyesOnly', false);

    	return $items->sortByDesc('seq_no')->first();
    }




    /**
     * return average song freshness
     */
    public function songsFreshness()
    {
    	$items = $this->items
            ->where('song_id', '>', 0)
            ->where('deleted_at', null);

    	$collect = collect();

    	foreach ($items as $item) {
    		if ($item->song_freshness)
    			$collect->push($item->song_freshness);
    	}

		return $collect->avg();
    }
}
