<?php

namespace KLB\Themes\Http\Controllers\Admin;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class SekureController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');

        $this->_config = request('_config');
    }

    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\View\View
    //  */
    // public function index()
    // {
    //     return view($this->_config['view']);
    // }

    /**
     * authorize a payment
     *
     * @return \Illuminate\View\View
     */
    public function authorize($Card, $amount)
    {
    /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
       $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
       $merchantAuthentication->setName("66fE4Xjs5A");
       $merchantAuthentication->setTransactionKey("82P3W79436nstNJ2");
       
       // Set the transaction's refId
       $refId = 'ref' . time();
   
       // Create the payment data for a credit card
       $creditCard = new AnetAPI\CreditCardType();
       $creditCard->setCardNumber($Card[0]);
       $creditCard->setExpirationDate($Card[1]);
       $creditCard->setCardCode($Card[2]);
   
       // Add the payment data to a paymentType object
       $paymentOne = new AnetAPI\PaymentType();
       $paymentOne->setCreditCard($creditCard);
           
       // Create a TransactionRequestType object and add the previous objects to it
       $transactionRequestType = new AnetAPI\TransactionRequestType();
       $transactionRequestType->setTransactionType("authOnlyTransaction"); 
       $transactionRequestType->setAmount($amount);
       $transactionRequestType->setPayment($paymentOne);

       // Assemble the complete transaction request
       $request = new AnetAPI\CreateTransactionRequest();
       $request->setMerchantAuthentication($merchantAuthentication);
       $request->setRefId($refId);
       $request->setTransactionRequest($transactionRequestType);
        // Log::debug(json_encode($request));
       // Create the controller and get the response
       $controller = new AnetController\CreateTransactionController($request);
       $response = $controller->executeWithApiResponse('https://apitest.authorize.net');

   
       if ($response != null) {
           // Check to see if the API request was successfully received and acted upon
           if ($response->getMessages()->getResultCode() == "Ok") {
               // Since the API request was successful, look for a transaction response
               // and parse it to display the results of authorizing the card
               $tresponse = $response->getTransactionResponse();
           
               if ($tresponse != null && $tresponse->getMessages() != null) {
                   Log::debug(" Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n");
                   Log::debug( " Transaction Response Code: " . $tresponse->getResponseCode() . "\n");
                   Log::debug( " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n");
                   Log::debug( " Auth Code: " . $tresponse->getAuthCode() . "\n");
                   Log::debug( " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n");
                   return $tresponse;
               } else {
                Log::debug( "Transaction Failed \n");
                   if ($tresponse->getErrors() != null) {
                    Log::debug( " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
                    Log::debug( " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
                    return 'error';
                   }
               }
               // Or, print errors if the API request wasn't successful
           } else {
            Log::debug( "Transaction Failed \n");
               
               $tresponse = $response->getTransactionResponse();
               if ($tresponse != null && $tresponse->getErrors() != null) {
                Log::debug( " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n");
                Log::debug( " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n");
               } else {
                Log::debug( " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n");
                Log::debug( " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n");
               }

               return 'error';
           }      
       } else {
        Log::debug(  "No response returned \n");
       }
         // return $response->getTransactionResponse();
    }
    /**
     * capture a previously authorized payment
     *
     * @return \Illuminate\Http\Response
     */
    function capturePreviouslyAuthorizedAmount($transactionid)
    {
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName('66fE4Xjs5A');
        $merchantAuthentication->setTransactionKey("82P3W79436nstNJ2");
        
        // Set the transaction's refId
        $refId = 'ref' . time();
    
        // Now capture the previously authorized  amount
        echo "Capturing the Authorization with transaction ID : " . $transactionid . "\n";
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
        $transactionRequestType->setRefTransId($transactionid);
    
        
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setTransactionRequest( $transactionRequestType);
    
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        
        if ($response != null)
        {
          if($response->getMessages()->getResultCode() == "Ok")
          {
            $tresponse = $response->getTransactionResponse();
            
             if ($tresponse != null && $tresponse->getMessages() != null)   
            {
                echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
                echo "Successful." . "\n";
                echo "Capture Previously Authorized Amount, Trans ID : " . $tresponse->getRefTransId() . "\n";
                echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
                 echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
                 return $response;
            }
            else
            {
              echo "Transaction Failed \n";
              if($tresponse->getErrors() != null)
              {
                echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";            
              }
              return 'error';
            }
          }
          else
          {
            echo "Transaction Failed \n";
            $tresponse = $response->getTransactionResponse();
            if($tresponse != null && $tresponse->getErrors() != null)
            {
              echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
              echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";                      
            }
            else
            {
              echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
              echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
            }
            return 'error';
          }      
        }
        else
        {
          echo  "No response returned \n";
          return 'error';
        }
    
      }
   /**
     * get details of transaction
     *
     * @return \Illuminate\Http\Response
     */
    public function getTransactionDetails($transactionId)
    {
         /* Create a merchantAuthenticationType object with authentication details
         retrieved from the constants file */
         $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
         $merchantAuthentication->setName("66fE4Xjs5A");
         $merchantAuthentication->setTransactionKey("82P3W79436nstNJ2");
         
         // Set the transaction's refId
         // The refId is a Merchant-assigned reference ID for the request.
         // If included in the request, this value is included in the response. 
         // This feature might be especially useful for multi-threaded applications.
         $refId = 'ref' . time();

         $request = new AnetAPI\GetTransactionDetailsRequest();
         $request->setMerchantAuthentication($merchantAuthentication);
         $request->setTransId($transactionId);

         $controller = new AnetController\GetTransactionDetailsController($request);

         $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

         if (($response != null) && ($response->getMessages()->getResultCode() == "Ok"))
         {
            echo "SUCCESS: Transaction Status:" . $response->getTransaction()->getTransactionStatus() . "\n";
            echo "                Auth Amount:" . $response->getTransaction()->getAuthAmount() . "\n";
            echo "                   Trans ID:" . $response->getTransaction()->getTransId() . "\n";
         }
         else
         {
            echo "ERROR :  Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
         }
         $temp = $response->getTransaction()->getPayment()->getCreditCard();
         //Log::debug(json_encode($temp));
         //Log::debug($temp);
         // $temp = $temp->get('payment');
         return $temp;
      }
         /**
     * get details of transaction
     *
     * @return \Illuminate\Http\Response
     */
    public function refundTransaction($transId, $cardN, $cardExp, $amount)
    {
      $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
      $merchantAuthentication->setName('66fE4Xjs5A');
      $merchantAuthentication->setTransactionKey('82P3W79436nstNJ2');
      
      // Set the transaction's refId
      $refId = 'ref' . time();
  
      // Create the payment data for a credit card
      $creditCard = new AnetAPI\CreditCardType();
      $creditCard->setCardNumber($cardN);
      $creditCard->setExpirationDate($cardExp);
      $paymentOne = new AnetAPI\PaymentType();
      $paymentOne->setCreditCard($creditCard);
      //create a transaction
      $transactionRequest = new AnetAPI\TransactionRequestType();
      $transactionRequest->setTransactionType( "refundTransaction"); 
      $transactionRequest->setAmount($amount);
      $transactionRequest->setPayment($paymentOne);
      $transactionRequest->setRefTransId($transId);
   
  
      $request = new AnetAPI\CreateTransactionRequest();
      $request->setMerchantAuthentication($merchantAuthentication);
      $request->setRefId($refId);
      $request->setTransactionRequest( $transactionRequest);
      $controller = new AnetController\CreateTransactionController($request);
      $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
  
      if ($response != null)
      {  
        if($response->getMessages()->getResultCode() == "Ok")
        {
          $tresponse = $response->getTransactionResponse();
          
           if ($tresponse != null && $tresponse->getMessages() != null)   
          {
            echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
            echo "Refund SUCCESS: " . $tresponse->getTransId() . "\n";
            echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
             echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
          }
          else
          {
            echo "Transaction Failed \n";
            if($tresponse->getErrors() != null)
            {
              echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
              echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";            
            }
            return 'error';
          }
        }
        else
        {
          echo "Transaction Failed \n";
          $tresponse = $response->getTransactionResponse();
          if($tresponse != null && $tresponse->getErrors() != null)
          {
            echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
            echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";                      
          }
          else
          {
            echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
            echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
          }
          return 'error';
        }      
      }
      else
      {
        echo  "No response returned \n";
      }
  
      return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view($this->_config['view']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

}
