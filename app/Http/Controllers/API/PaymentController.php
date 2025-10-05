<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Submission;
use App\Models\Payment;
use App\Models\Form;

class PaymentController extends Controller
{
    public function initiate(Request $r){

        $validator = Validator::make($r->all(),[
            'submission_id'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false,'message' => $validator->errors()], 200);
        }

        $submission = Submission::findOrFail($r->submission_id);
        $form = Form::findOrFail($submission->form_id);
        $amountPaise = intval($form->fee * 100);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $order = $api->order->create([
            'receipt' => 'rcpt_'.$submission->id,
            'amount' => $amountPaise,
            'currency' => 'INR',
            'payment_capture' => 1
        ]);

        if(!isset($order['id'])){
            return response()->json(['status' => false,'message' => 'Something went worng when initiate payment'], 200);
        }

        $orderArr = $order->toArray();

        $payment = Payment::create([
            'submission_id' => $submission->id,
            'user_id' => $submission->user_id ?? auth()->user()->id,
            'amount' => $amountPaise,
            'currency' => 'INR',
            'razorpay_order_id' => $orderArr['id'],
            'status' => 'created',
            'meta' => json_encode($orderArr)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment initiate successfully',
            'order' => $orderArr,
            'payment_id' => $payment->id
        ]);
    }

    public function verify(Request $r){
        $r->validate([
            'razorpay_order_id'=>'required',
            'razorpay_payment_id'=>'required',
            'razorpay_signature'=>'required',
            'payment_id'=>'required'
        ]);

        $payment = Payment::findOrFail($r->payment_id);

        $generated_signature = hash_hmac('sha256', $r->razorpay_order_id . '|' . $r->razorpay_payment_id, env('RAZORPAY_SECRET'));
        //prd($generated_signature);

        if ($generated_signature !== $r->razorpay_signature) {
            $payment->update(['status' => 'failed']);
            return response()->json(['status' => false,'message'=>'Invalid signature'], 200);
        }

        // mark paid
        $payment->update([
            'razorpay_payment_id' => $r->razorpay_payment_id,
            'razorpay_signature' => $r->razorpay_signature,
            'status' => 'paid'
        ]);

        // generate PDF receipt
        $receiptData = [
            'payment' => $payment,
            'user' => $payment->user,
            'submission' => $payment->submission
        ];

        $html = view('pdf.receipt', $receiptData)->render();
        $pdf = \PDF::loadHTML($html);
        $path = "receipts/receipt_{$payment->id}.pdf";
        Storage::put('public/'.$path, $pdf->output());
        $payment->receipt_path = $path;
        $payment->save();

        return response()->json([
            'status' => true,
            'message'=>'Payment verified and receipt generated',
            'receipt_url' => Storage::url($path)
        ]);
    }
}
