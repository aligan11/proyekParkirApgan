<?php

namespace App\Rules;

use App\Models\Admin;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class loginCheck implements ValidationRule
{

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        $username = $this->request->input('username');
        $passwordInput = $this->request->input('password');
        $rolesDb = Admin::where('username', $username)->value('role');


        $loginStatus = false;

        $emailAdminCheck = Admin::where('username', $username)->count();
        if ($emailAdminCheck > 0) {
            $passwordDb  = Admin::where('username', $username)->value('password');
            $rolesDb = Admin::where('username', $username)->value('role');
            if (Hash::check($passwordInput, $passwordDb)) {
                $loginStatus = true;
            } else {
                $fail('Password tidak sesuai');
            }
        } else {
            $fail('Username atau email tidak ditemukan');
        }

        if ($loginStatus) {
            $adminData = Admin::where('username', $username)->first();
            session()->put('loginStatus', true);
            session()->put('adminData', $adminData);
            session()->put('role', $adminData->role); // simpan role di session
        }
    }
}
