<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forms = Form::get();
        $res = array(
            'status' => true,
            'message' => 'Forms fetch successfully',
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
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false,'message' => $validator->errors()], 200);
        }

        $form = new Form();
        $form->title = $request->title;
        $form->description = isset($request->description) && !empty($request->description) ? $request->description : null;
        $form->fields = isset($request->fields) && !empty($request->fields) ? json_encode($request->fields) : null;
        $form->start_at = isset($request->start_at) && !empty($request->start_at) ? $request->start_at : null;
        $form->end_at = isset($request->end_at) && !empty($request->end_at) ? $request->end_at : null;
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
        $form = Form::find($id);
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
        //
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
