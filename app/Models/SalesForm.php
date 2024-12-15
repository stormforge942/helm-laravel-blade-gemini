<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesForm extends Model
{
    use HasFactory;

    protected $fillable = ['first_name','last_name','email','phone','website_rented','price','sales_representative','originating_lead','status','select_date'];

    protected $table = "sales_form";
}
