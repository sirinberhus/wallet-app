<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|string|unique:promotions,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from', //make sure valid_to dont start before valid_from
            'is_active' => 'boolean',
            'rewards' => 'required|array|min:1',
            // * means check that every <item> in each object of the rewards array.
            'rewards.*.type' => 'required|string|in:CASH,BONUS_SPIN,FREE_BET,OTHER',
            'rewards.*.amount' => 'required|numeric|min:0',
            'rewards.*.currency' => 'required|string|size:3'
        ];
    }
}
