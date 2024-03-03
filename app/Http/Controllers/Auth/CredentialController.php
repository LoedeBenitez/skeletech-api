<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\ResponseTrait;

use App\Models\Address;
use App\Models\Credential;
use App\Models\Beneficiary;
use App\Models\ContactNumber;
use App\Models\EducationalBackground;
use App\Models\EmergencyContact;
use App\Models\EmploymentInformation;
use App\Models\GovernmentInformation;
use App\Models\PersonalInformation;

class CredentialController extends Controller
{
    use ResponseTrait;
    public function onLogin(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required',
            'password' => 'required|min:6',
        ]);
        $logged_user = Credential::where('email', '=', $fields['email'])->first();

        if (!Auth::attempt($fields)) {
            return $this->dataResponse('error', 404, __('msg.employee_not_found'));
        }
        if ($logged_user->status == 0) {
            return $this->dataResponse('error', 404, 'Login failed: account has been suspended. Please contact IT Support for assistance.');
        }
        $token = auth()->user()->createToken('appToken')->plainTextToken;
        $personalInformation = $this->onGetPersonalInformation($fields['email']);
        $data = [
            'token' => $token,
            'user_details' => $personalInformation,
        ];
        return $this->dataResponse('success', 200, __('msg.login_success'), $data);
    }
    public function onGetPersonalInformation($email)
    {
        return PersonalInformation::where('email', '=', $email)->first();
    }
    public function onChangePassword(Request $request)
    {
        $fields = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed|different:old_password',
        ]);
        try {
            $user = auth()->user();
            if (!Hash::check($request->old_password, $user->password)) {
                return $this->dataResponse('error', 400, __('msg.old_password_incorrect'));
            }
            $user->update([
                'password' => bcrypt($fields['new_password']),
            ]);
            return $this->dataResponse('success', 201, __('msg.password_change_successful'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', 400, __('msg.password_change_unsuccessful'));
        }
    }
    public function onLogout()
    {
        try {
            auth()->user()->tokens()->delete();
            return $this->dataResponse('success', 200, __('msg.logout'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', 400, $exception->getMessage());
        }
    }

    public function onChangeStatus()
    {
        try {
            $userId = auth()->user()->id;
            $credential = Credential::find($userId);
            $credential->status = !$credential->status;
            $credential->save();
            return $this->dataResponse('success', 200, __('msg.update_success'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', 400, $exception->getMessage());
        }
    }
}
