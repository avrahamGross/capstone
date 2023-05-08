<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Phpml\Helper\Trainable;
use Illuminate\Http\Request;
use Phpml\Association\Apriori;
use App\Imports\TransactionsImport;
use App\Models\ThreeProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    use Trainable;

    public function make_apriori()
    {
        $tables = ["App\Models\OneProduct", "App\Models\TwoProduct", "App\Models\ThreeProduct", "App\Models\FourProduct"];

        $data = Transaction::get()->toArray();

        $samples = [];
        foreach ($data as $transaction) {
            $array = [];
            foreach ($transaction as $key => $value) {
                if ($value === 1 && $key !== 'id') {
                    array_push($array, $key);
                }
                // else {
                //     array_push($array, $value);
                // }
            }
            array_push($samples, $array);
        }
        $labels  = [];
        $support = 0.4;
        $total_transactions = Transaction::count();
        while ($support > 0.04) {
            $confidence = 0.5;
            while ($confidence > 0.04) {
                $associator = new Apriori($support, $confidence);
                $associator->train($samples, $labels);
                $results = $associator->apriori();
                foreach ($results as $index => $count_items) {
                    if ($index === 1) {
                        continue;
                    }
                    foreach ($count_items as $key => $selection) {
                        $insert_list = array_fill_keys($selection, 1);
                        $this_support = Transaction::where($insert_list)->count();
                        $final_support = $this_support / $total_transactions;
                        // $insert_list['support'] = 
                        $already_there = $tables[$index - 2]::where($insert_list)->first();
                        if ($already_there && $already_there->support > $final_support) {
                            continue;
                        }
                        $tables[$index - 2]::updateOrCreate($insert_list, ['support' => $final_support]);
                    }
                }
                $confidence = $confidence - 0.05;
            }
            $support = $support - 0.05;
        }
    }

    public function import()
    {
        Excel::import(new TransactionsImport, 'C:\Users\Avramie\Documents\WGU\Capstone\basket_analysis.csv');
    }

    public function welcome(Request $request)
    {
        $tables = ["App\Models\OneProduct", "App\Models\TwoProduct", "App\Models\ThreeProduct"];
        $validated = $this->validation($request->all());
        if ($validated instanceof ValidationException) {
            return view('welcome', [
                'products' => [
                    'apple', 'bread', 'butter', 'cheese', 'corn', 'dill', 'eggs',
                    'ice_cream', 'kidney_bean', 'milk', 'nutmeg', 'onion', 'sugar', 'unicorn', 'yogurt', 'chocolate'
                ],
                'error' => $validated->getMessage()
            ]);
        }
        $results = array();
        $temp_results = [2 => [], 1 => [], 0 => []];
        $multi_array = [
            2 => [
                array_fill_keys($validated, 1)
            ],
            1 => [
                [$validated['product1'] => 1, $validated['product2'] => 1],
                [$validated['product1'] => 1, $validated['product3'] => 1],
                [$validated['product2'] => 1, $validated['product3'] => 1]
            ],
            0 => [
                [$validated['product1'] => 1],
                [$validated['product2'] => 1],
                [$validated['product3'] => 1]
            ]
        ];

        $results = array();
        $single_level = array();
        foreach ($multi_array as $index => $options) {

            foreach ($options as $option) {
                $result = $tables[$index]::where($option)
                    ->orderBy('support', 'desc')
                    ->limit(3)
                    ->get()
                    ->toArray();
                $single_level = array_merge($single_level, $result);
                if (count($single_level) >= 3) {
                    break;
                }
                // if (count($single_level) > 0) {
                //     array_push($temp_results[$index],  $single_level);
                // }
            }
            if (count($single_level) === 0) {
                continue;
            }
        }
        $single_level = new Collection($single_level);
        $single_level = $single_level->sortBy([
            ['support', 'desc']
        ]);
        $temp = $single_level->slice(0, 3)->toArray();

        foreach ($temp as $object) {
            array_push($results, ['item' => array_keys($object, 1), 's&c' => array_intersect_key($object, array_flip(array('support')))]);
        }

        $recommendations = array();
        unset($result);
        // $result[] = ['Products','Support and Confidence'];

        foreach ($results as $to_recomemnd) {
            // $ideas = array_diff($to_recomemnd['item'], $validated);
            // $selected = array_intersect($to_recomemnd['item'], $validated);
            // array_push($recommendations, [$ideas, $selected, $to_recomemnd['s$c']]);
            // array_push($recommendations, str_replace('_', ' ', implode('', array_diff($to_recomemnd['item'], $validated))));
            array_push($recommendations, str_replace('_', ' ', implode('', array_diff($to_recomemnd['item'], $validated))));
        }
        $recommendations = array_filter($recommendations);
        $incrementing = 0;
        foreach ($recommendations as $key => $value) {
            $result[$value] = $results[$key]['s&c'];
            array_push($result[$value], $this->lift_calculator($multi_array[2][0], $value));
            $result[$value]['confidence'] = $this->confidence_calculator($results[$key]['s&c']['support'], $multi_array[2][0]);
        }

        $last = array_pop($validated);
        $selected = implode(', ', $validated);
        if ($selected) {
            $selected .= ', or ';
        }
        $selected .= $last;
        $items = [];
        $lift = [];
        foreach($result as $item => $metric) {
            array_push($items, $item);
            array_push($lift, $metric[0]);
        }
        // $items = json_encode($items);
        // $lift = json_encode($lift);
        return view('welcome', [
            'products' => [
                'apple', 'bread', 'butter', 'cheese', 'corn', 'dill', 'eggs',
                'ice_cream', 'kidney_bean', 'milk', 'nutmeg', 'onion', 'sugar', 'unicorn', 'yogurt', 'chocolate'
            ],
            'selected' => $selected,
            'recommendation' => $recommendations,
            'result' => $result,
            'items' => json_encode($items),
            'lift' => json_encode($lift)
        ]);
    }

    private function confidence_calculator($support, $selected) {
        $total_transactions = Transaction::count();
        $selected_support = Transaction::where($selected)->count() / $total_transactions;
        $confidence = $support / $selected_support;
        return $confidence;
    }

    private function lift_calculator($selected, $recommened) {
        $total_transactions = Transaction::count();
        $support_selected = Transaction::where($selected)->count() / $total_transactions;
        $support_recommended = Transaction::where([$recommened => 1])->count() / $total_transactions;
        $over_value = $support_selected * $support_recommended;
        $selected[$recommened] = 1;
        $transactions_with_recommended = Transaction::where($selected)->count() / $total_transactions;
        
        $lift = $transactions_with_recommended / $over_value;
        return $lift;
    }

    private function validation($data)
    {
        $validated = Validator::make($data, [
            'product1' => function ($attribute, $value, $fail) use ($data) {
                if ($value == $data['product2'] || $value == $data['product3']) {
                    $fail('Each selection must be different!');
                }
            },
            'product2' => function ($attribute, $value, $fail) use ($data) {
                if ($value == $data['product1'] || $value == $data['product3']) {
                    $fail('Each selection must be different!');
                }
            },
            'product3' => function ($attribute, $value, $fail) use ($data) {
                if ($value == $data['product1'] || $value == $data['product2']) {
                    $fail('Each selection must be different!');
                }
            },
        ]);
        return $validated->validate();
    }
}
