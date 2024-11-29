<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //Auth::check();   // Authorized request if User authenticated
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // check request method
        $isUpdateMethod = $this->isMethod('put') || $this->isMethod('patch');

        // Retrieve the user ID from query
        // or from the route, in this case 'users/{user}' or Route::resource('users', ...
        $id = $this->id ?? ($this->route()?->parameters()['user'] ?? null);

        return [
          'name' => [
            'required',
            'string',
            'max:255',
          ],
          'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            $isUpdateMethod
              ? Rule::unique('users', 'email')->ignore($id)
              : Rule::unique('users', 'email'),

          ],
          'password' => [
            $isUpdateMethod
              ? 'sometimes'
              : 'required',
            'string',
            $isUpdateMethod
              ? ''
              : 'confirmed',
            Password::defaults()
          ],
        ];
    }
}
