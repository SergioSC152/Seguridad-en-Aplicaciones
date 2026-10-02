<?php
namespace App\Services;
use App\Models\Product;
use App\Models\User;
class ProductService
{
    public function save(User $user, array $data, ?Product $record=null): Product
    {
        if ($record) { $record->update($data); return $record->refresh(); }
        return Product::create(['user_id'=>$user->id]+$data);
    }
}

