<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{

    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);


        $teamQuery = Team::query();

        //Get single data
        if ($id) {
            $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success($team, 'Team found');
            }
            return ResponseFormatter::error('Team not found', 404);
        }

        // get multiple data

        $teams = $teamQuery->where('company_id', $request->company_id);
        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams found',
        );
    }


    public function create(CreateTeamRequest $request)
    {

        try {
            // Upload logo
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // create company
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new Exception('Team not created');
            }

            return ResponseFormatter::success($team, 'Company created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {

        try {
            // Get team
            $team = Team::find($id);

            // Check if team exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Update team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($team, 'Team updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // get team
            $team = Team::find($id);

            // check if teaam exits
            if(!$team){
                throw new Exception('Team not found');
            }
            // delete team
            $team->delete();
            return ResponseFormatter::success('Team delete');

        } catch (Exception $e) {

            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
