<?php

namespace App\Http\Controllers\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\PersonalInformation as PersonalInformationModel;
use Exception;
use App\Traits\CrudOperationsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PersonalInformationController extends Controller
{
    use CrudOperationsTrait;
    public PersonalInformationModel $personalInformationModel;
    public Credential $credential;

    public function onCreate(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email|unique:credentials,email',
            'password' => 'required|confirmed|min:6',
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:25',
            'middle_name' => 'required|string|max:25',
            'last_name' => 'required|string|max:25',
            'suffix' => 'nullable|string|max:10',
            'alias' => 'nullable|string|max:15',
            'gender' => 'required|string|max:10',
            'birth_date' => 'required|date|date_format:Y-m-d',
            'age' => 'required|integer',
            // password_confirmation
        ]);
        DB::beginTransaction();
        try {
            $this->personalInformationModel = new PersonalInformationModel();
            $this->credential = new Credential();
            $this->credential->email = $fields['email'];
            $this->credential->password = $fields['password'];
            $this->credential->save();

            $this->personalInformationModel->fill($request->only([
                'email',
                'first_name',
                'middle_name',
                'last_name',
                'prefix',
                'suffix',
                'alias',
                'gender',
                'birth_date',
                'age',
            ]));
            $this->personalInformationModel->save();
            DB::commit();
            $data = [
                'credentials' => $this->credential,
                'personal_information' => $this->personalInformationModel
            ];
            return $this->dataResponse('success', Response::HTTP_OK, __('msg.create_success'), $data);

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }


    public function onGetById($id)
    {
        return $this->readRecordById(PersonalInformationModel::class, $id, 'Personal Information');
    }

    public function onGetAll()
    {
        return $this->readRecord(PersonalInformationModel::class, 'Personal Information');
    }
    public function onUpdateById(Request $request, $id)
    {
        $rules = [
            'prefix' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:25',
            'middle_name' => 'required|string|max:25',
            'last_name' => 'required|string|max:25',
            'suffix' => 'nullable|string|max:10',
            'alias' => 'nullable|string|max:15',
            'gender' => 'required|string|max:10',
            'birth_date' => 'required|date|date_format:Y-m-d',
            'age' => 'required|integer',
        ];
        return $this->updateRecordById(PersonalInformationModel::class, $request, $rules, 'Personal Information', $id);
    }

    public function onGetPaginatedList(Request $request)
    {
        $searchableFields = [
            'email',
            'first_name',
            'middle_name',
            'last_name',
            'prefix',
            'suffix',
            'alias',
            'gender',
            'birth_date',
            'age',
        ];
        return $this->readPaginatedRecord(PersonalInformationModel::class, $request, $searchableFields, 'Personal Information');
    }

    public function onChangeStatus($id)
    {
        return $this->changeStatusRecordById(PersonalInformationModel::class, $id, 'Personal Information');
    }
    public function onDeleteById($id)
    {
        return $this->deleteRecordById(Credential::class, $id, 'Credential');
    }
}
