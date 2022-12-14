<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Models\Responsibility;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponsibilityController extends Controller
{

    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);


        $ResponsibilityQuery = Responsibility::query();

        //Get single data
        if ($id) {
            $Responsibility = $ResponsibilityQuery->find($id);

            if ($Responsibility) {
                return ResponseFormatter::success($Responsibility, 'Responsibility found');
            }
            return ResponseFormatter::error('Responsibility not found', 404);
        }

        // get multiple data

        $Responsibilites = $ResponsibilityQuery->where('role_id', $request->role_id);
        if ($name) {
            $Responsibilites->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $Responsibilites->paginate($limit),
            'Responsibilites found',
        );
    }


    public function create(CreateResponsibilityRequest $request)
    {

        try {

            // create company
            $Responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            if (!$Responsibility) {
                throw new Exception('Responsibility not created');
            }

            return ResponseFormatter::success($Responsibility, 'Company created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }


    public function destroy($id)
    {
        try {
            // get Responsibility
            $Responsibility = Responsibility::find($id);

            // check if teaam exits
            if(!$Responsibility){
                throw new Exception('Responsibility not found');
            }
            // delete Responsibility
            $Responsibility->delete();
            return ResponseFormatter::success('Responsibility delete');

        } catch (Exception $e) {

            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
