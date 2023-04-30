<?php

namespace App\Imports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TransactionsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Transaction::create([
                'id' => $row[0] + 1,
                'apple' => $row[1],
                'bread' => $row[2],
                'butter' => $row[3],
                'cheese' => $row[4],
                'corn' => $row[5],
                'dill' => $row[6],
                'eggs' => $row[7],
                'ice_cream' => $row[8],
                'kidney_bean' => $row[9],
                'milk' => $row[10],
                'nutmeg' => $row[11],
                'onion' => $row[12],
                'sugar' => $row[13],
                'unicorn' => $row[14], 
                'yogurt' => $row[15],
                'chocolate' => $row[16]
            ]);
        }
    }
}
