<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doner extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstName',
        'lastName',
        'phoneNumber',
        'email',
        'currency',
        'amount',
        'address',
        'donatePurpose',
        'agreement',
    ];
}
