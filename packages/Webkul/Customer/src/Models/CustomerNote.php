<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Customer\Contracts\CustomerNote as CustomerNoteContract;

class CustomerNote extends Model implements CustomerNoteContract
{
    protected $fillable = ['note', 'customer_id', 'created_by'];

    public function customer() : BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}