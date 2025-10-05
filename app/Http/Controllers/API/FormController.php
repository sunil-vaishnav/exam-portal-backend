<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FormController extends Controller
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

        $forms = Form::with('submissions')->skip($page)->take($limit)->get();
        $total = Form::count();
        $res = array(
            'status' => true,
            'message' => 'Forms fetch successfully',
            'total_forms' => $total,
            'forms' => $forms
        );

        return response()->json($res, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //prd($request->all());

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'fields' => 'nullable|array',
            'fee' => 'required',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false,'message' => $validator->errors()], 200);
        }

        $form = new Form();
        $form->title = $request->title;
        $form->description = isset($request->description) && !empty($request->description) ? $request->description : null;
        $form->fee = $request->fee;
        $form->fields = isset($request->fields) && !empty($request->fields) ? json_encode($request->fields) : null;
        $form->start_at = isset($request->start_at) && !empty($request->start_at) ? Carbon::parse($request->start_at) : null;
        $form->end_at = isset($request->end_at) && !empty($request->end_at) ? Carbon::parse($request->end_at) : null;
        $form->save();

        $res = array(
            'status' => true,
            'message' => 'Form create successfully',
            'form' => $form
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
        $form = Form::with('submissions')->find($id);
        $res = array(
            'status' => true,
            'message' => 'Form fetch successfully',
            'form' => $form
        );

        return response()->json($res, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $form = Form::find($id);
        if(empty($form)){
            return response()->json(['status' => false,'message' => 'Please provide vaild form id'], 200);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'fields' => 'nullable|array',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false,'message' => $validator->errors()], 200);
        }

        $form->title = $request->title;
        $form->description = isset($request->description) && !empty($request->description) ? $request->description : $form->description;
        $form->fields = isset($request->fields) && !empty($request->fields) ? json_encode($request->fields) : $form->fields;
        $form->fee = isset($request->fee) && !empty($request->fee) ? $request->fee : $form->fee;
        $form->start_at = isset($request->start_at) && !empty($request->start_at) ? Carbon::parse($request->start_at) : $request->start_at;
        $form->end_at = isset($request->end_at) && !empty($request->end_at) ? Carbon::parse($request->end_at) : $request->end_at;
        $form->update();

        $res = array(
            'status' => true,
            'message' => 'Form update successfully',
            'form' => $form
        );

        return response()->json($res, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $form = Form::find($id);
        if(!empty($form)){
            $form->delete();
            $res = array(
                'status' => true,
                'message' => 'Form delete successfully'
            );

        }else{
            $res = array(
                'status' => false,
                'message' => 'Please provide correct form id'
            );
        }

        return response()->json($res, 200);
    }
}
