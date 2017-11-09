<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Packages;
use App\Packages_schedule;
use App\Packages_price;
use App\Packages_booking;
use App\Packages_access;
use Validator;
use Illuminate\Http\Request;
use DB;
class PackagesController extends Controller
{		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Package Added Successfully.";
	    $success=false;
	    $result="";			
	    $data =$request->all();
            $rules = array(
		'activity_id' => 'required',
		'merchant_id' => 'required',
		'package_name' => 'required|min:20',
		'package_details' => 'required|min:20',
		'package_description' => 'required|min:20',
		'status' => 'required',
		'category1'=>'required',
		'highlights'=>'required|min:20',
		'price_details.list_price'=>'required|numeric',	
		'price_details.web_price'=>'required|numeric',	
		'price_details.cost_price'=>'required|numeric',	
		'schedule_details.activity_startdate'=>'required',	
		'schedule_details.activity_type'=>'required',	
		'schedule_details.duration'=>'required',	
		'schedule_details.start_day'=>'required',	
		'schedule_details.start_time'=>'required',	
		'schedule_details.endtime'=>'required',	
		'schedule_details.end_day'=>'required',	
		'booking_details.type'=>'required'	
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
                                $Packages=new Packages();
				$activity_id=$newtodo['activity_id'];	
                                $Packages->activity_id=$newtodo['activity_id'];
                                $Packages->merchant_id=$newtodo['merchant_id'];;
                                $Packages->package_name=$newtodo['package_name'];
                                $Packages->package_details=$newtodo['package_details'];
				$Packages->status=$newtodo['status'];
				$Packages->package_description=$newtodo['package_description'];
				$Packages->highlights=$newtodo['highlights'];
				$Packages->categoryL1=$newtodo['category1'];
				 $datetime=date("Y-m-d H:i:s");
				if($request->input('package_terms_conditions'))
                                {
                                        $Packages->package_terms_conditions=$request->input('package_terms_conditions');
                                }
				if($request->input('package_more_details'))
                                {
                                        $Packages->package_more_details=$request->input('package_more_details');
                                }
				if($request->input('tag_id'))
                                {
					$Packages->tag_id=$request->input('tag_id');
                                }
				if($request->input('category2'))
                                {
                                        $Packages->categoryL2=$request->input('category2');
                                }
				if($request->input('categorySVG'))
                                {
                                        $Packages->categorySVG=$request->input('categorySVG');
                                }
                                DB::beginTransaction();
                                $response=$Packages->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create Package.Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
					return response()->json($response,$error_code);	
                                }
				$package_id = $Packages->package_id; // last inserted id for merchants
				if($request->input('schedule_details'))
				{
					$schedule_details=$request->input('schedule_details');
					$activity_startdate=$schedule_details['activity_startdate'];
					$activity_enddate=$schedule_details['activity_enddate'];
					$activity_type=$schedule_details['activity_type'];
					$activity_on_M=$schedule_details['activity_on_M'];
					$activity_on_T=$schedule_details['activity_on_T'];
					$activity_on_W=$schedule_details['activity_on_W'];
					$activity_on_TH=$schedule_details['activity_on_TH'];
					$activity_on_F=$schedule_details['activity_on_F'];
					$activity_on_S=$schedule_details['activity_on_S'];
					$activity_on_SU=$schedule_details['activity_on_SU'];
					$start_day=$schedule_details['start_day'];
					$start_time=$schedule_details['start_time'];
					$end_day=$schedule_details['end_day'];
					$endtime=$schedule_details['endtime'];
					$duration=$schedule_details['duration'];
					$meals=$schedule_details['meals'];
					$dinner=$schedule_details['dinner'];
					$lunch=$schedule_details['lunch'];
					$pickup=$schedule_details['pickup'];
					$pickup_location=$schedule_details['pickup_location'];
					$dropoff=$schedule_details['dropoff'];
					$dropoff_location=$schedule_details['dropoff_location'];	
					$response=DB::table('package_schedule_details')->insert(['package_id' =>$package_id, 'activity_id' =>$activity_id,'activity_startdate' =>"$activity_startdate",'activity_enddate' =>"$activity_enddate",'activity_type' =>"$activity_type",'activity_on_M'=>"$activity_on_M",'activity_on_T'=>"$activity_on_T",'activity_on_W'=>"$activity_on_W",'activity_on_TH'=>"$activity_on_TH",'activity_on_F'=>"$activity_on_F",'activity_on_S'=>"$activity_on_S",'activity_on_SU'=>"$activity_on_SU",'start_day'=>"$start_day",'start_time'=>"$start_time",'end_day'=>"$end_day",'endtime'=>"$endtime",'duration'=>"$duration",'meals'=>"$meals",'dinner'=>"$dinner",'lunch'=>"$lunch",'pickup'=>"$pickup",'pickup_location'=>"$pickup_location",'dropoff'=>"$dropoff",'dropoff_location'=>"$dropoff_location",'created_at' => "$datetime",'updated_at' => "$datetime"]);		
					if(!$response)
					{
						DB::rollback();
                                 	        $error_code=503;
                                        	$message="Could not create Package.Please try again after some time";
                                        	$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                        	return response()->json($response,$error_code);			
					}	
				}
				if($request->input('price_details'))
                                {
                                        $price_details=$request->input('price_details');
					$list_price=$price_details['list_price'];
                                        $web_price=$price_details['web_price'];
                                        $cost_price=$price_details['cost_price'];
                                        $vat_required=$price_details['vat_required'];
                                        $bast_cashback=$price_details['bast_cashback'];
                                        $best_cashback_type=$price_details['best_cashback_type'];
                                        $free_allowed=$price_details['free_allowed'];
                                        $age_category=$price_details['age_category'];
                                        $age_value_type=$price_details['age_value_type'];
                                        $minimum_age=$price_details['minimum_age'];
                                        $maximum_age=$price_details['maximum_age'];
                                        $age=$price_details['age'];
                                        $response=DB::table('package_price_details')->insert(['package_id' =>$package_id, 'list_price' =>$list_price,'web_price' =>"$web_price",'cost_price' =>"$cost_price",'vat_required' =>"$vat_required",'bast_cashback'=>"$bast_cashback",'best_cashback_type'=>"$best_cashback_type",'free_allowed'=>"$free_allowed",'age_category'=>"$age_category",'age_value_type'=>"$age_value_type",'minimum_age'=>"$minimum_age",'maximum_age'=>"$maximum_age",'age'=>"$age",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                                        if(!$response)
                                        {
                                                DB::rollback();
                                                $error_code=503;
                                                $message="Could not create Package.Please try again after some time";
                                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                                return response()->json($response,$error_code);
                                        }
				}
				if($request->input('booking_details'))
                                {
                                        $booking_details=$request->input('booking_details');
                                        $tour=$booking_details['tour'];
                                        $type=$booking_details['type'];
                                        $best_discount=$booking_details['best_discount'];
                                        $exclusive=$booking_details['exclusive'];
                                        $instant_ticket=$booking_details['instant_ticket'];
                                        $experience_type=$booking_details['experience_type'];
                                        $attraction=$booking_details['attraction'];
                                        $notify_email=$booking_details['notify_email'];
                                        $alternate_email=$booking_details['alternate_email'];
                                        $inclusions=$booking_details['inclusions'];
                                        $exclusions=$booking_details['exclusions'];
                                        $start_latitude=$booking_details['start_latitude'];
                                        $start_longitude=$booking_details['start_longitude'];
                                        $start_address1=$booking_details['start_address1'];
					$start_address2=$booking_details['start_address2'];
                                        $start_city_name=$booking_details['start_city_name'];
                                        $start_postal_code=$booking_details['start_postal_code'];
					$start_state=$booking_details['start_state'];
                                        $start_country_code=$booking_details['start_country_code'];
                                        $end_latitude=$booking_details['end_latitude'];
					$end_longitude=$booking_details['end_longitude'];
                                        $end_address1=$booking_details['end_address1'];
                                        $end_address2=$booking_details['end_address2'];
					$end_city_name=$booking_details['end_city_name'];
                                        $end_postal_code=$booking_details['end_postal_code'];
                                        $end_state=$booking_details['end_state'];
                                        $end_country_code=$booking_details['end_country_code'];	
                                        $meeting_point_instruction=$booking_details['meeting_point_instruction'];	
                                        $critic_review=$booking_details['critic_review'];	
                                        $user_review=$booking_details['user_review'];	
                                        $response=DB::table('package_booking_details')->insert(['package_id' =>$package_id, 'tour' =>$tour,'type' =>"$type",'best_discount' =>"$best_discount",'exclusive' =>"$exclusive",'instant_ticket'=>"$instant_ticket",'experience_type'=>"$experience_type",'attraction'=>"$attraction",'notify_email'=>"$notify_email",'alternate_email'=>"$alternate_email",'inclusions'=>"$inclusions",'exclusions'=>"$exclusions",'start_latitude'=>"$start_latitude",'start_longitude' =>$start_longitude,'start_address1' =>"$start_address1",'start_address2' =>"$start_address2",'start_city_name' =>"$start_city_name",'start_postal_code'=>"$start_postal_code",'start_state'=>"$start_state",'start_country_code'=>"$start_country_code",'end_latitude'=>"$end_latitude",'end_longitude'=>"$end_longitude",'end_address1'=>"$end_address1",'end_address2'=>"$end_address2",'end_city_name'=>"$end_city_name",'end_postal_code' =>"$end_postal_code",'end_state' =>"$end_state",'end_country_code' =>"$end_country_code",'meeting_point_instruction' =>"$meeting_point_instruction",'critic_review'=>"$critic_review",'user_review'=>"$user_review",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                                        if(!$response)
                                        {
                                                DB::rollback();
                                                $error_code=503;
                                                $message="Could not create Package.Please try again after some time";
                                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                                return response()->json($response,$error_code);
                                        }
                                }
				if($request->input('access_details'))
                                {
                                        $access_details=$request->input('access_details');
                                        $identification_card=$access_details['identification_card'];
                                        $identification_val=$access_details['identification_val'];
                                        $allowed_member=$access_details['allowed_member'];
                                        $height_feet=$access_details['height_feet'];
                                        $height_inchaes=$access_details['height_inchaes'];
                                        $age_restriction=$access_details['age_restriction'];
                                        $min_age=$access_details['min_age'];
                                        $max_age=$access_details['max_age'];
                                        $allowed_age_group=$access_details['allowed_age_group'];
                                        $response=DB::table('package_access_details')->insert(['package_id' =>$package_id, 'identification_card' =>"$identification_card",'identification_val' =>"$identification_val",'allowed_member' =>"$allowed_member",'height_feet' =>"$height_feet",'height_inchaes'=>"$height_inchaes",'age_restriction'=>"$age_restriction",'min_age'=>"$min_age",'max_age'=>"$max_age",'allowed_age_group'=>"$allowed_age_group",'created_at' => "$datetime",'updated_at' => "$datetime"]);
					if(!$response)
                                        {
                                                DB::rollback();
                                                $error_code=503;
                                                $message="Could not create Package.Please try again after some time";
                                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                                return response()->json($response,$error_code);
                                        }
					if($allowed_age_group==1 && $request->input('age_groups'))
					{
						$age_groups=$request->input('age_groups');
						foreach($age_groups as $key=>$group_details)
                                        	{
                	                                $group_range=$group_details['group_range'];
							$age_group_val=1;
                                        	        $response=DB::table('package_age_restriction')->insert(['package_id' =>$package_id, 'age_group_attr' =>"$group_range",'age_group_val' =>"$age_group_val",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                                                	if(!$response)
                                                	{
                                                        	DB::rollback();
	                                                        $error_code=503;
        	                                                $message="Could not create Activity.Please try again after some time";
                	                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        	                                return response()->json($response,$error_code);
                                	                }
                                        	}	
					}
	
			      }		
                              DB::commit();
			      $success=true;
			      $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
				#$response= ["error"=>false,"message" => "","status_code"=>200];
			      return response()->json($response,$error_code);
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not create Activity .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Packages = Packages::get();
		$PackagesArr=$Packages->toArray();
		foreach($PackagesArr as $key=>$package_details)
		{
			$package_id=$package_details['package_id'];
			$price_details = DB::table('package_price_details')->select('*')->where('package_id', [$package_id])->get();		
			//$mapping=$city_mapping->toArray(); 
			$price_details=json_decode($price_details,true);
			$PackagesArr[$key]['price_details']=$price_details;			
				
			 $schedule_details = DB::table('package_schedule_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $schedule_details=json_decode($schedule_details,true);
                        $PackagesArr[$key]['schedule_details']=$schedule_details;
			
                        $booking_details = DB::table('package_booking_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $booking_details=json_decode($booking_details,true);
                        $PackagesArr[$key]['booking_details']=$booking_details;	

			 //access_details
                	$access_details = DB::table('package_access_details')->select('*')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
	                $access_details=json_decode($access_details,true);
        	        $access_details=$access_details[0];
                	if($access_details['allowed_age_group']==1)
	                {
        	                 $package_age_restriction = DB::table('package_age_restriction')->select('age_group_attr')->where('package_id', [$package_id])->get();
                	//$mapping=$city_mapping->toArray();
                        	$package_age_restriction=json_decode($package_age_restriction,true);
	                        $access_details['age_groups']=$package_age_restriction;
                	}
	                $PackagesArr[$key]['access_details']=$access_details;

		}	
		// $products = DB::table('products')->where('status', [$status])->get();		
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$PackagesArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function get_package_details($package_id)
        {
		$success=false;
		$error_code="200";
		$message="";
		$Packages = Packages::where('package_id',$package_id)->orderBy('package_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
		if(!$Packages)
		{
			$error_code="403";
			$message="Data Not Found for Package Id $package_id";
			$response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
			return response()->json($response,$error_code);
		}
         	$PackagesArr=$Packages->toArray();
		$price_details = DB::table('package_price_details')->select('*')->where('package_id', [$package_id])->get();
                $PackagesArr['price_details']=$price_details[0];

                $schedule_details = DB::table('package_schedule_details')->select('*')->where('package_id', [$package_id])->get();
                $PackagesArr['schedule_details']=$schedule_details[0];

                $booking_details = DB::table('package_booking_details')->select('*')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
                $PackagesArr['booking_details']=$booking_details[0];


		//access_details
		$access_details = DB::table('package_access_details')->select('*')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
	        $access_details=json_decode($access_details,true);
		$access_details=$access_details[0];
		if($access_details['allowed_age_group']==1)
		{
			 $package_age_restriction = DB::table('package_age_restriction')->select('age_group_attr')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
	                $package_age_restriction=json_decode($package_age_restriction,true);
			$access_details['age_groups']=$package_age_restriction;
		}	
                $PackagesArr['access_details']=$access_details;

		$success=true;		
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$PackagesArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function show_merchant_packages($merchant_id)
        {
                //$Packages = Packages::get();
		$Packages = Packages::where('merchant_id',$merchant_id)->orderBy('package_id','desc')->get();
                $PackagesArr=$Packages->toArray();
                foreach($PackagesArr as $key=>$package_details)
                {
                        $package_id=$package_details['package_id'];
                        $price_details = DB::table('package_price_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $price_details=json_decode($price_details,true);
                        $PackagesArr[$key]['price_details']=$price_details;

                         $schedule_details = DB::table('package_schedule_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $schedule_details=json_decode($schedule_details,true);
                        $PackagesArr[$key]['schedule_details']=$schedule_details;

                        $booking_details = DB::table('package_booking_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $booking_details=json_decode($booking_details,true);
                        $PackagesArr[$key]['booking_details']=$booking_details;
			//access_details
                        $access_details = DB::table('package_access_details')->select('*')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
                        $access_details=json_decode($access_details,true);
                        $access_details=$access_details[0];
                        if($access_details['allowed_age_group']==1)
                        {
                                 $package_age_restriction = DB::table('package_age_restriction')->select('age_group_attr')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                                $package_age_restriction=json_decode($package_age_restriction,true);
                                $access_details['age_groups']=$package_age_restriction;
                        }
                        $PackagesArr[$key]['access_details']=$access_details;

                }
                // $products = DB::table('products')->where('status', [$status])->get();
                $success=true;
                $error_code="200";
                $message="";
                #$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$PackagesArr,"success"=>$success];
                return response()->json($response,$error_code);
	}	
	public function get_merchant_packages_details($merchant_id,$package_id)
        {
                $success=false;
                $error_code="200";
                $message="";
                $Packages = Packages::where('merchant_id',$merchant_id)->where('package_id',$package_id)->orderBy('package_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
                if(!$Packages)
                {
                        $error_code="403";
                        $message="Data Not Found for Package Id $package_id";
                        $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $PackagesArr=$Packages->toArray();
                $price_details = DB::table('package_price_details')->select('*')->where('package_id', [$package_id])->get();
                $PackagesArr['price_details']=$price_details[0];

                $schedule_details = DB::table('package_schedule_details')->select('*')->where('package_id', [$package_id])->get();
                $PackagesArr['schedule_details']=$schedule_details[0];

                $booking_details = DB::table('package_booking_details')->select('*')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
                $PackagesArr['booking_details']=$booking_details[0];
		$access_details = DB::table('package_access_details')->select('*')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
                $access_details=json_decode($access_details,true);
                $access_details=$access_details[0];
                if($access_details['allowed_age_group']==1)
                {
                         $package_age_restriction = DB::table('package_age_restriction')->select('age_group_attr')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
                        $package_age_restriction=json_decode($package_age_restriction,true);
                        $access_details['age_groups']=$package_age_restriction;
                }
                $PackagesArr['access_details']=$access_details;

                $success=true;
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$PackagesArr],"success"=>$success];
                return response()->json($response,$error_code);
	}
	public function update_package_details(Request $request,$package_id)
	{
	    $allowed_access_columns=array('identification_card','identification_val','allowed_member','height_feet','height_inchaes','age_restriction','min_age','max_age','allowed_age_group');
            $allowed_price_columns=array('list_price','web_price','cost_price','vat_required','bast_cashback','best_cashback_type','free_allowed','age_category','age_value_type','minimum_age','maximum_age','age');
                //allowed_booking_columns
            $allowed_booking_columns=array('tour','type','best_discount','exclusive','instant_ticket','experience_type','attraction','notify_email','alternate_email','inclusions','exclusions','start_latitude','start_longitude','start_address1','start_address2','start_city_name','start_state','start_postal_code','start_country_code','end_latitude','end_longitude','end_address1','end_address2','end_city_name','end_state','end_postal_code','end_country_code','meeting_point_instruction','critic_review','user_review');
	    $allowed_schedule_columns=array('activity_startdate','activity_enddate','activity_type','activity_on_M','activity_on_T','activity_on_W','activity_on_TH','activity_on_F','activity_on_S','activity_on_SU','start_day','start_time','end_day','endtime','duration','meals','dinner','lunch','pickup','pickup_location','dropoff','dropoff_location');	
       	    $error_code=200;
            $message="Package Details updated successfully.";
            $success=false;
            $result="";
	    $datetime=date("Y-m-d H:i:s");	
	    $Packages = Packages::find($package_id);	
            if(!$Packages)
            {
                    $error_code="403";
                    $message="Please check Package id.Package  details not found for package id $package_id.";
                    $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
		    return response()->json($response,$error_code);	
            }		
	    DB::beginTransaction();
	    $Packages->package_id = $package_id;
	    $status=$request->input('status');
	    if(isset($status))
	    {		
	    	$Packages->status = $status;
	    }
	    if($request->input('activity_id'))
                        $Packages->activity_id = $request->input('activity_id');
	
	    if($request->input('package_name'))
	 		$Packages->package_name = $request->input('package_name');
	    
	    if($request->input('package_details'))
                        $Packages->package_details = $request->input('package_details');
	    if($request->input('package_description'))
                        $Packages->package_description = $request->input('package_description');
	    if($request->input('highlights'))
                        $Packages->highlights = $request->input('highlights');
	    if($request->input('package_terms_conditions'))
                        $Packages->package_terms_conditions = $request->input('package_terms_conditions');

	    if($request->input('package_more_details'))
                        $Packages->package_more_details = $request->input('package_more_details');
	    if($request->input('category1'))
                        $Packages->categoryL1 = $request->input('category1');	
	    if($request->input('tag_id'))
                        $Packages->tag_id = $request->input('tag_id');
	    if($request->input('category2'))
                        $Packages->categoryL2 = $request->input('category2');	
	    if($request->input('categorySVG'))
                        $Packages->categorySVG = $request->input('categorySVG');

            $response=$Packages->save();
	    if(!$response)				    	
	    {
		    DB::rollback();
		    $error_code="403";
                    $message="Unable to update activity details for activity  id $merchant_id.";
                    $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
		    return response()->json($response,$error_code);
	    } 		
	    if($request->input('schedule_details'))
	    {
			$Packages_schedule = Packages_schedule::find($package_id);
			if(!$Packages_schedule)
            		{
			    DB::rollback();	
	                    $error_code="403";
        	            $message="Please check Package id.Package schedule details not found for package id $package_id.";
                	    $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
	                    return response()->json($response,$error_code);
        	        }
			$Packages_schedule->package_id=$package_id;	    
			$schedule_details=$request->input('schedule_details');
                        foreach($schedule_details as $schedule_key=>$schedule_val)
                        {
                                if(in_array($schedule_key,$allowed_schedule_columns) && $schedule_val!='')
                                {
                                        $Packages_schedule->$schedule_key=$schedule_val;
                                }
                        }
			$response=$Packages_schedule->save();
 	                if(!$response)
        	        {
	                    DB::rollback();
        	            $error_code="403";
                	    $message="Unable to update package schedule details for pacakge id $merchant_id.";
	                    $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
        	            return response()->json($response,$error_code);
            		}						
	    }			
	    if($request->input('price_details'))
            {
                        $Packages_price = Packages_price::find($package_id);
                        if(!$Packages_price)
                        {
                            DB::rollback();
                            $error_code="403";
                            $message="Please check Package id.Package price details not found for package id $package_id.";
                            $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                            return response()->json($response,$error_code);
                        }
                        $Packages_price->package_id=$package_id;	
			$price_details=$request->input('price_details');
                        foreach($price_details as $price_key=>$price_val)
                        {
                                if(in_array($price_key,$allowed_price_columns) && $price_val!='')
                                {
                                        $Packages_price->$price_key=$price_val;
                                }
                        }
			$response=$Packages_price->save();
                        if(!$response)
                        {
                            DB::rollback();
                            $error_code="403";
                            $message="Unable to update package price details for pacakge id $package_id.";
                            $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                            return response()->json($response,$error_code);
                        }	
					
	    }	
	    if($request->input('booking_details'))
            {
                        $Packages_booking = Packages_booking::find($package_id);
                        if(!$Packages_booking)
                        {
                            DB::rollback();
                            $error_code="403";
                            $message="Please check Package id.Package Booking details not found for package id $package_id.";
                            $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                            return response()->json($response,$error_code);
                        }
                        $Packages_booking->package_id=$package_id;
			$booking_details=$request->input('booking_details');
                        foreach($booking_details as $booking_key=>$booking_val)
                        {
                                if(in_array($booking_key,$allowed_booking_columns) && $booking_val!='')
                                {
                                        $Packages_booking->$booking_key=$booking_val;
                                }
                        }
                        $response=$Packages_booking->save();
			if(!$response)
                        {
                            DB::rollback();
                            $error_code="403";
                            $message="Unable to update package Booking  details for pacakge id $package_id.";
                            $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                            return response()->json($response,$error_code);
                        }
	    }			
	    //allowed_access_columns	
	    if($request->input('access_details'))
            {
                        $Packages_access = Packages_access::find($package_id);
                        if(!$Packages_access)
                        {
                            DB::rollback();
                            $error_code="403";
                            $message="Please check Package id.Package Access details not found for package id $package_id.";
                            $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                            return response()->json($response,$error_code);
                        }
			$access_details=$request->input('access_details');
			foreach($access_details as $update_key=>$update_val)
			{
				if(in_array($update_key,$allowed_access_columns) && $update_val!='')	
				{
					$Packages_access->$update_key=$update_val;
				}	
			}
                        $Packages_access->package_id=$package_id;
			$response=$Packages_access->save();
                        if(!$response)
                        {
                            DB::rollback();
                            $error_code="403";
                            $message="Unable to update package Access details for pacakge id $package_id.";
                            $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                            return response()->json($response,$error_code);
                        }
			if($access_details['allowed_age_group'] && $access_details['age_groups'])
			{
				 $age_groups=$access_details['age_groups'];
				 $delete_response=DB::table('package_age_restriction')->where('package_id', '=', $package_id)->delete();
		                if(!$delete_response)
                		{
		                        DB::rollback();
                		        $error_code=503;
		                        $message="Could not update Package Access Details.Please try again after some time";
                		        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$Packages,"success"=>$success];
	                	        return response()->json($response,$error_code);
        		        }
                                 foreach($age_groups as $key=>$group_details)
                                 {
                                         $group_range=$group_details['group_range'];
                                         $age_group_val=1;
                                         $response=DB::table('package_age_restriction')->insert(['package_id' =>$package_id, 'age_group_attr' =>"$group_range",'age_group_val' =>"$age_group_val",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                                         if(!$response)
                                         {
                                                 DB::rollback();
                                                 $error_code=503;
                                                 $message="Unable to update Pacakge access details with age group .Please try again after some time";
                                                 $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$Packages,"success"=>$success];
                                                 return response()->json($response,$error_code);
                                         }
                                 }						
			}
	    }		
	    DB::commit();
	    $PackagesArr=$Packages->toArray();
	    $success=true;	
	    $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$PackagesArr,"success"=>$success];	
	    return response()->json($response,$error_code);
	}
}
?>
