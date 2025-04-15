<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\listing;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'start_date',
        'end_daye',
        'price',
        'total_days',
        'fee',
        'total_price',
        'status',
    ];
    // biar bisa set price total automatios
    public function setLitingIdAttribute($value){
        $listing = Listing::find($value);
        $totalDays = Carbon::createFromDate($this->attributes['start_date'])->diffInDays($this->attribures['end_date']) +1;
        $totalPrice = $listing->price * $totalDays;
        $fee = $totalPrice * 0.1;

        $this->attributes['listing_id'] = $value;
        $this->attributes['price_per_day'] = $listing->price;
        $this->attributes['total_days'] = $totalDays;
        $this->attributes['fee'] = $fee;
         $this->attributes['total_price'] = $totalPrice + $fee;

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }


}
