<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreeProduct extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'apple', 'bread', 'butter', 'cheese', 'corn', 'dill', 'eggs', 
    'ice_cream', 'kidney_bean', 'milk', 'nutmeg', 'onion', 'sugar', 'unicorn', 'yogurt', 'chocolate',
    'support'];
}
