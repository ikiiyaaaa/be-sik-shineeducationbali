<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        // Ambil ID user dari parameter route
        $userId = $this->route('id');
        
        // Jika tidak ada userId, return rules tanpa validasi unique
        if (!$userId) {
            return [
                'name' => 'sometimes|required|string|max:50|min:3',
                'email' => 'sometimes|required|string|email|max:50',
                'password' => 'sometimes|required|string|min:8|confirmed',
                'password_confirmation' => 'sometimes|required_with:password|string|min:8|same:password',
                'role' => 'sometimes|required|string|exists:roles,name',
                'status' => 'sometimes|required|in:Aktif,Tidak Aktif',
            ];
        }

        // Gunakan caching untuk mengambil user dengan role-nya
        $user = Cache::remember("user_{$userId}_with_roles", 3600, function () use ($userId) {
            return User::with('roles')->findOrFail($userId);
        });

        return [
            'name' => 'sometimes|required|string|max:50|min:3',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:50',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => 'sometimes|required|string|min:8|confirmed',
            'password_confirmation' => 'sometimes|required_with:password|string|min:8|same:password',
            'role' => 'sometimes|required|string|exists:roles,name',
            'status' => [
                'sometimes',
                'required',
                'in:Aktif,Tidak Aktif',
                function ($attribute, $value, $fail) use ($user) {
                    // Validasi jika user memiliki role "Owner", status tidak bisa diubah menjadi Tidak Aktif
                    if ($user->hasRole('Owner') && $value === 'Tidak Aktif') {
                        $fail('User dengan role Owner tidak dapat di-nonaktifkan.');
                    }
                },
            ],
        ];
    }


    /**
     * Get the validated data from the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Remove fields that shouldn't be saved to database
        unset($validated['password_confirmation']);
        // Don't remove role here, let service handle it
        
        return $validated;
    }
}
