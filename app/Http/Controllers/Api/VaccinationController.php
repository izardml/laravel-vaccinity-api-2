<?php

namespace App\Http\Controllers\Api;

use App\Models\Society;
use App\Models\Vaccination;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VaccinationController extends Controller
{
    public function index(Request $request)
    {
        $society = Society::where('login_tokens', $request->token)->first();

        $first = Vaccination::where('society_id', $society->id)->with('spot.regional', 'vaccine')->first();
        $second = Vaccination::where('society_id', $society->id)->with('spot.regional', 'vaccine')->skip(1)->first();

        $first ? $first->spot->makeHidden('available_vaccines') : null;
        $second ? $second->spot->makeHidden('available_vaccines') : null;
        
        $vaccinations = compact('first', 'second');

        return response()->json(compact('vaccinations'));
    }

    public function store(Request $request)
    {
        $society = Society::where('login_tokens', $request->token)->first();
        $consultation = Consultation::where('society_id', $society->id)->first();

        if($consultation->status != 'accepted') {
            return response()->json([
                'message' => 'Your consultation must be accepted by doctor before'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'spot_id' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 401);
        }

        $vaccination = Vaccination::where('society_id', $society->id)->first();

        if($vaccination) {
            $now = Carbon::parse($request->date);
            $date = Carbon::parse($vaccination->date);
            $diff = $now->diffInDays($date);

            if($diff < 30) {
                return response()->json([
                    'message' => 'Wait at least +30 days from 1st Vaccination'
                ], 401);
            }
        }

        $vaccination_count = Vaccination::where('society_id', $society->id)->count();
        
        if($vaccination_count >= 2) {
            return response()->json([
                'message' => 'Society has been 2x vaccinated'
            ], 401);
        }

        $queue = Vaccination::where([
            'date' => $request->date,
            'spot_id' => $request->spot_id
        ])->count();

        $vaccination = Vaccination::create([
            'queue' => $queue + 1,
            'dose' => $vaccination ? 2 : 1,
            'date' => $request->date,
            'society_id' => $society->id,
            'spot_id' => $request->spot_id,
        ]);

        return response()->json([
            'message' => ($vaccination->dose == 2 ? 'Second' : 'First') . ' vaccination registered successful'
        ], 200);
    }
}
