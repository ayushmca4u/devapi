<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
use  QuickBooks_Utilities;
use QuickBooks_Loader;
#use Illuminate\Http\Response;
class QuickBookController  extends Controller
{
	 private $IntuitAnywhere;
	    private $context;
    private $realm;
	public function __construct()
	{
        	if (!\QuickBooks_Utilities::initialized(env('QBO_DSN'))) {
            // Initialize creates the neccessary database schema for queueing up requests and logging
            \QuickBooks_Utilities::initialize(env('QBO_DSN'));
        }
        $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));
    	}
	public function  qboConnect(){

	try
	{
		$checkResponse=$this->IntuitAnywhere->check(env('QBO_USERNAME'), env('QBO_TENANT'));
		$testresponse=$this->IntuitAnywhere->test(env('QBO_USERNAME'), env('QBO_TENANT'));
		var_dump($checkResponse,$testresponse);
		echo  "checkResponse  [$checkResponse] and testresponse===[$testresponse]";die;
        if ($this->IntuitAnywhere->check(env('QBO_USERNAME'), env('QBO_TENANT')) && $this->IntuitAnywhere->test(env('QBO_USERNAME'), env('QBO_TENANT'))) {
            // Set up the IPP instance
            $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
            // Get our OAuth credentials from the database
            $creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), env('QBO_TENANT'));
            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                \QuickBooks_IPP::AUTHMODE_OAUTH,
                env('QBO_USERNAME'),
                $creds);

            if (env('QBO_SANDBOX')) {
                // Turn on sandbox mode/URLs
                $IPP->sandbox(true);
            }
            // This is our current realm
            $this->realm = $creds['qb_realm'];
            // Load the OAuth information from the database
            $this->context = $IPP->context();
		echo  "I am heeeeeeeeeeeeeeeeeee connected ";
		die;
            return true;
        } else {
		echo  "I Not able  to connect";
		die;
            return false;
        }
	}
	catch(Exception $e)
	{
		echo  "<pre>";print_r($e);die;
	}	
    }
	public function qboOauth(){
        if ($this->IntuitAnywhere->handle(env('QBO_USERNAME'), env('QBO_TENANT')))
        {
            ; // The user has been connected, and will be redirected to QBO_SUCCESS_URL automatically.
        }
        else
        {
            // If this happens, something went wrong with the OAuth handshake
            die('Oh no, something bad happened: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
        }
    }

    public function qboSuccess(){
        return view('qbo_success');
    }

    public function qboDisconnect(){
        $this->IntuitAnywhere->disconnect(env('QBO_USERNAME'), env('QBO_TENANT'),true);
        return redirect()->intended("/yourpath");// afer disconnect redirect where you want
 
    }

		
        public function create(Request $request)
        {
		
	}
	public function createCustomer()
	{
		$this->qboConnect();
        $CustomerService = new \QuickBooks_IPP_Service_Customer();

        $Customer = new \QuickBooks_IPP_Object_Customer();
        $Customer->setTitle('Ms');
 $Customer->setGivenName('Shannon');
 $Customer->setMiddleName('B');
 $Customer->setFamilyName('Palmer');
 $Customer->setDisplayName('Shannon B Palmer ' . mt_rand(0, 1000));
        // Terms (e.g. Net 30, etc.)
        $Customer->setSalesTermRef(4);

        // Phone #
        $PrimaryPhone = new \QuickBooks_IPP_Object_PrimaryPhone();
        $PrimaryPhone->setFreeFormNumber('860-532-0089');
 $Customer->setPrimaryPhone($PrimaryPhone);

        // Mobile #
        $Mobile = new \QuickBooks_IPP_Object_Mobile();
        $Mobile->setFreeFormNumber('860-532-0089');
 $Customer->setMobile($Mobile);

        // Fax #
        $Fax = new \QuickBooks_IPP_Object_Fax();
        $Fax->setFreeFormNumber('860-532-0089');
 $Customer->setFax($Fax);

        // Bill address
        $BillAddr = new \QuickBooks_IPP_Object_BillAddr();
        $BillAddr->setLine1('72 E Blue Grass Road');
 $BillAddr->setLine2('Suite D');
 $BillAddr->setCity('Mt Pleasant');
 $BillAddr->setCountrySubDivisionCode('MI');
 $BillAddr->setPostalCode('48858');
 $Customer->setBillAddr($BillAddr);

        // Email
        $PrimaryEmailAddr = new \QuickBooks_IPP_Object_PrimaryEmailAddr();
        $PrimaryEmailAddr->setAddress('support@consolibyte.com');
        $Customer->setPrimaryEmailAddr($PrimaryEmailAddr);

        if ($resp = $CustomerService->add($this->context, $this->realm, $Customer))
        {
            //print('Our new customer ID is: [' . $resp . '] (name "' . $Customer->getDisplayName() . '")');
            //return $resp;
            //echo $resp;exit;
            //$resp = str_replace('{','',$resp);
            //$resp = str_replace('}','',$resp);
            //$resp = abs($resp);
            return $this->getId($resp);
        }
        else
        {
            //echo 'Not Added qbo';
            print($CustomerService->lastError($this->context));
        }
    }

}
?>
