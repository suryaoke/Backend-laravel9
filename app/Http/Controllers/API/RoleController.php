<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateroleRequest;
use App\Http\Requests\UpdateroleRequest;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');

        $limit = $request->input('limit', 10);
        $with_responsibilities =  $request->input('with_responsibilities', false);


        $roleQuery = Role::query();

        //Get single data
        if ($id) {
            $role = $roleQuery->with('responsibilities')->find($id);

            if ($role) {
                return ResponseFormatter::success($role, 'role found');
            }
            return ResponseFormatter::error('role not found', 404);
        }

        // get multiple data

        $roles = $roleQuery->where('company_id', $request->company_id);
        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }
        if ($with_responsibilities) {
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'roles found',
        );
    }


    public function create(CreateroleRequest $request)
    {

        try {

            // create company
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new Exception('role not created');
            }

            return ResponseFormatter::success($role, 'Company created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateroleRequest $request, $id)
    {

        try {
            // Get role
            $role = Role::find($id);

            // Check if role exists
            if (!$role) {
                throw new Exception('role not found');
            }



            // Update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($role, 'role updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // get role
            $role = Role::find($id);

            // check if teaam exits
            if(!$role){
                throw new Exception('role not found');
            }
            // delete role
            $role->delete();
            return ResponseFormatter::success('role delete');

        } catch (Exception $e) {

            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
