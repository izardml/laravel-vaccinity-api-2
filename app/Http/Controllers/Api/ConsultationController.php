<?php

namespace App\Http\Controllers\Api;

use App\Models\Society;
use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $society = Society::where('login_tokens', $request->token)->first();

        $consultation = Consultation::where('society_id', $society->id)->with('doctor')->first();

        return response()->json(compact('consultation'), 200);
    }

    public function store(Request $request)
    {
        $society = Society::where('login_tokens', $request->token)->first();

        $consultation = Consultation::create([
            'society_id' => $society->id,
            'disease_history' => $request->disease_history,
            'current_symptoms' => $request->current_symptoms,
        ]);

        return response()->json([
            'message' => 'Request consultation sent successful'
        ], 200);
    }
}
