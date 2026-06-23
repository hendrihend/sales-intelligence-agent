<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Tambahkan ini

class Product extends Model // Ubah dari ModelsPruned menjadi Model
{
     use HasFactory;

     protected $fillable = [
          'name',
          'category',
          'purchase_price',
          'price',
          'stock',
          'status',
     ];
}
