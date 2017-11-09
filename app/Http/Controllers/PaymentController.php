<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Payments;
use App\Paymenttransactions;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class PaymentController extends Controller
{		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="";
	    $success=false;
	    $result="";			
	    $data =$request->all();		
            $rules = array(
                'gid' => 'required|numeric',
                'currency' => 'required|min:3',
                'adult' => 'required|numeric',
                'total_participants' => 'required|numeric',
                'booking_activity_date' => 'required',
             );
            $validator = Validator::make($data, $rules);
	    if ($validator->fails())
	    {
		$error_code="403";
		$message=$validator->errors();
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
		return response()->json($response,$error_code);
	    }
	    else
	    {
		try
                        {
				$newtodo = $data;
				$gateway_id=$newtodo['gid'];
	    			$Payments = Payments::where('gid',$gateway_id)->orderBy('gid','desc')->first();
			        if(!$Payments)
		                {
		                    $error_code="403";
                		    $message="Data Not Found for Gateway ID $gateway_id";
		                    $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                		    return response()->json($response,$error_code);
		                }	
				switch ($gateway_id)
				{
					case "1":
					$this->show_payfort_form($data);
					break;
					case "2":
					$this->show_paypal_form($data);
					break;
				}
				
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not fetch Payment gateway details .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
		                return response()->json($response,$error_code);	
                        }
	
	    }		
        }
	 public function show()
        {
                $Payments = Payments::get();
                $PaymentsArr=$Payments->toArray();
                $success=true;
                $error_code="200";
                $message="";
                #$response= ["error"=>false,"message" =>$ShopperArr,"status_code"=>200];
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$PaymentsArr,"success"=>$success];
                return response()->json($response,$error_code);
        }
}
?>
