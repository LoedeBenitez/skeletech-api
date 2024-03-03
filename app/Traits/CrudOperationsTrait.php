<?php

namespace App\Traits;

use Exception;
use App\Traits\ResponseTrait;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

trait CrudOperationsTrait
{
    use ResponseTrait;
    public function createRecord($model, $request, $rules, $modelName)
    {
        $fields = $request->validate($rules);
        try {
            $record = new $model();
            $record->fill($fields);
            $record->save();
            return $this->dataResponse('success', Response::HTTP_OK, __('msg.create_success'));
        } catch (QueryException $exception) {
            if ($exception->getCode() == 23000) {
                if (str_contains($exception->getMessage(), '1062 Duplicate entry')) {
                    return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, __('msg.duplicate_entry', ['modelName' => $modelName]));
                }
            }
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }
    public function updateRecordById($model, $request, $rules, $modelName, $id)
    {
        $fields = $request->validate($rules);
        try {
            $record = new $model();
            $record = $model::find($id);
            if ($record) {
                $fields['updated_by_id'] = auth()->user()->id;
                $record->update($fields);
                return $this->dataResponse('success', Response::HTTP_OK, __('msg.update_success'), $record);
            }
            return $this->dataResponse('error', Response::HTTP_OK, __('msg.update_failed'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }
    public function readPaginatedRecord($model, $request, $searchableFields, $modelName)
    {
        try {
            $fields = $request->validate([
                'display' => 'nullable|integer',
                'page' => 'nullable|integer',
                'search' => 'nullable|string',
                'status' => 'nullable|integer',
            ]);
            $page = $fields['page'] ?? 1;
            $display = $fields['display'] ?? 10;
            $offset = ($page - 1) * $display;
            $search = $fields['search'] ?? '';
            $query = $model::orderBy('id')
                ->when($search, function ($query) use ($searchableFields, $search) {
                    $query->where(function ($innerQuery) use ($searchableFields, $search) {
                        foreach ($searchableFields as $field) {
                            $innerQuery->orWhere($field, 'like', '%' . $search . '%');
                        }
                    });
                });
            if (isset($request['status'])) {
                $query->where('status', $request['status']);
            }
            $dataList = $query->limit($display)->offset($offset)->get();
            $totalPage = max(ceil($query->count() / $display), 1);
            $reconstructedList = [];
            foreach ($dataList as $key => $value) {
                $data = $model::findOrFail($value->id);
                $response = $data->toArray();
                if (isset($data->created_by_id)) {
                    $response['created_by'] = $data->createdBy->first_name . ' ' . $data->createdBy->middle_name . ' ' . $data->createdBy->last_name;
                }
                if (isset($data->updated_by_id)) {
                    $response['updated_by'] = $data->updatedBy->first_name . ' ' . $data->updatedBy->middle_name . ' ' . $data->updatedBy->last_name;
                }
                $reconstructedList[] = $response;
            }
            $response = [
                'total_page' => $totalPage,
                'data' => $reconstructedList,
            ];
            if ($dataList->isNotEmpty()) {
                return $this->dataResponse('success', 200, __('msg.record_found'), $response);
            }
            return $this->dataResponse('error', 404, __('msg.record_not_found'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }
    public function readRecord($model, $modelName)
    {
        try {
            $dataList = $model::get();
            $reconstructedList = [];
            foreach ($dataList as $key => $value) {
                $data = $model::findOrFail($value->id);
                $response = $data->toArray();

                if (isset($data->created_by_id)) {
                    $response['created_by'] = $data->createdBy->first_name . ' ' . $data->createdBy->middle_name . ' ' . $data->createdBy->last_name;
                }
                if (isset($data->updated_by_id)) {
                    $response['updated_by'] = $data->updatedBy->first_name . ' ' . $data->updatedBy->middle_name . ' ' . $data->updatedBy->last_name;
                }
                $reconstructedList[] = $response;
            }
            if ($dataList->isNotEmpty()) {
                return $this->dataResponse('success', Response::HTTP_OK, __('msg.record_found'), $reconstructedList);
            }
            return $this->dataResponse('error', Response::HTTP_OK, __('msg.record_not_found'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }
    public function readRecordById($model, $id, $modelName)
    {
        try {
            $data = $model::find($id);
            if ($data) {
                $response = $data->toArray();
                if (isset($data->created_by_id)) {
                    $response['created_by'] = $data->createdBy->first_name . ' ' . $data->createdBy->middle_name . ' ' . $data->createdBy->last_name;
                }

                if (isset($data->updated_by_id)) {
                    $response['updated_by'] = $data->updatedBy->first_name . ' ' . $data->updatedBy->middle_name . ' ' . $data->updatedBy->last_name;
                }
                return $this->dataResponse('success', Response::HTTP_OK, __('msg.record_found'), $response);
            }
            return $this->dataResponse('error', Response::HTTP_OK, __('msg.record_not_found'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }
    public function changeStatusRecordById($model, $id, $modelName)
    {
        try {
            $data = $model::find($id);
            if ($data) {
                $response = $data->toArray();
                $response['status'] = !$response['status'];
                $data->update($response);
                return $this->dataResponse('success', Response::HTTP_OK, __('msg.update_success'), $response);
            }
            return $this->dataResponse('error', Response::HTTP_OK, __('msg.record_not_found'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }
    public function deleteRecordById($model, $id, $modelName)
    {
        try {
            $deletedRows = $model::destroy($id);
            if ($deletedRows) {
                return $this->dataResponse('success', Response::HTTP_OK, __('msg.delete_success'));
            }
            return $this->dataResponse('error', Response::HTTP_OK, __('msg.record_not_found'));
        } catch (QueryException $exception) {
            if ($exception->getCode() == 23000) {
                return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, __('msg.delete_failed_fk_constraint', ['modelName' => $modelName]));
            }
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }

    public function readDistinctRecord($model, $modelName, $dbData)
    {
        try {
            $dataList = $model::distinct()->get($dbData);
            $reconstructedList = [];
            foreach ($dataList as $key => $value) {
                $reconstructedList[] = $value;
            }
            if ($dataList->isNotEmpty()) {
                return $this->dataResponse('success', Response::HTTP_OK, __('msg.record_found'), $reconstructedList);
            }
            return $this->dataResponse('error', Response::HTTP_NOT_FOUND, __('msg.record_not_found'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }

    public function readRecordByCategory($model, $categoryName, $categoryId, $modelName)
    {
        try {
            $data = $model::where($categoryName, $categoryId)->orderBy('level', 'ASC')->get();
            if ($data) {
                $reconstructedList = [];
                foreach ($data as $response) {
                    if (isset($data->created_by_id)) {
                        $response['created_by'] = $data->createdBy->first_name . ' ' . $data->createdBy->middle_name . ' ' . $data->createdBy->last_name;
                    }
                    if (isset($response->updated_by_id)) {
                        $response['updated_by'] = $response->updatedBy->first_name . ' ' . $response->updatedBy->middle_name . ' ' . $response->updatedBy->last_name;
                    }
                    $reconstructedList[] = $response;
                }

                return $this->dataResponse('success', Response::HTTP_OK, __('msg.record_found'), $reconstructedList);
            }
            return $this->dataResponse('error', Response::HTTP_OK, __('msg.record_not_found'));
        } catch (Exception $exception) {
            return $this->dataResponse('error', Response::HTTP_BAD_REQUEST, $exception->getMessage());
        }
    }
}

/* 1044: Access denied for a user to a database.
1045: Access denied for a user with respect to a password.
1142: Permission denied for a specific table.
1146: Table does not exist.
1217: Cannot delete or update a parent row (foreign key constraint fails).
1451: Cannot delete or update a parent row: a foreign key constraint fails.
1452: Cannot add or update a child row: a foreign key constraint fails.
2002: Cannot connect to the MySQL server.
1049: Unknown database.
1062: Duplicate entry for a key.
1064: Syntax error in SQL statement.
1364: Field doesn't have a default value.
2003: Can’t connect to MySQL server.
23000: Integrity constraint violation (generic code, often seen in Laravel for various constraint violations). */
?>
