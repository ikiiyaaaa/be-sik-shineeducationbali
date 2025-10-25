<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roleId = $this->route('id');
        
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'min:3',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ];
    }
}
