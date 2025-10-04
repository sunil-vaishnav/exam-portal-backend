<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Form;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = isset($request->limit) && !empty($request->limit) ? $request->limit : 10;  
        $page = isset($request->page) && !empty($request->page) ? $request->page : 0;
        $user_id = auth()->user()->id;

        $submissions = Submission::with('form')
                ->where('user_id',$user_id)
                ->skip($page)
                ->take($limit)
                ->get();

        $total = Submission::where('user_id',$user_id)->count();

        $res = array(
            'status' => true,
            'message' => 'Submissions fetch successfully',
            'total_submissions' => $total,
            'submissions' => $submissions
        );

        return response()->json($res, 200);
    }

    /**
     * Show the submission for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //prd($request->all());

        $validator = Validator::make($request->all(), [
            'form_id' => 'required',
            'data' => 'required|array'
        ]);

        if($validator->fails()){
            return response()->json(['status' => false,'message' => $validator->errors()], 200);
        }

        $form = Form::findOrFail($request->form_id);

        // check if form is active
        $now = now();
        if (!$form->active || ($form->start_at && $now->lt($form->start_at)) || ($form->end_at && $now->gt($form->end_at))) {
            return response()->json(['status' => false, 'message' => 'Form is not active'], 200);
        }

        $submission = new Submission();
        $submission->form_id = $request->form_id;
        $submission->user_id = auth()->user()->id;
        $submission->data = json_encode($request->data);
        $submission->save();

        $res = array(
            'status' => true,
            'message' => 'Submission submit successfully',
            'submission' => $submission
        );

        return response()->json($res, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $submission = Submission::with('form')->where('user_id',auth()->user()->id)->find($id);
        $res = array(
            'status' => true,
            'message' => 'Submission fetch successfully',
            'submission' => $submission
        );

        return response()->json($res, 200);
    }

    /**
     * Display the specified resource.
     *
     */
    public function adminIndex(Request $request)
    {
        $limit = isset($request->limit) && !empty($request->limit) ? $request->limit : 10;  
        $page = isset($request->page) && !empty($request->page) ? $request->page : 0;

        $submissions = Submission::with('form')->skip($page)->take($limit)->get();
        $total = Submission::count();
        $res = array(
            'status' => true,
            'message' => 'Submissions fetch successfully',
            'total_submissions' => $total,
            'submissions' => $submissions,
        );

        return response()->json($res, 200);
    }
}
