<?php namespace App\Controllers;

class Razorpay extends BaseController {

	public function __construct() {
		$this->session 	= \Config\Services::session();
	}

	public function index() {
		$data = [];
		$data['title']              = 'Checkout payment | Infovistar';  
        $data['callback_url']       = base_url().'/razorpay/callback';
        $data['surl']               = base_url().'/razorpay/success';;
        $data['furl']               = base_url().'/razorpay/failed';;
        $data['currency_code']      = 'INR';
		echo view("checkout", $data);
	}

	// initialized cURL Request
    private function curl_handler($payment_id, $amount)  {
        $url            = 'https://api.razorpay.com/v1/payments/'.$payment_id.'/capture';
        $key_id         = "YOUR_KEY_ID";
        $key_secret     = "YOUR_SECRET";
        $fields_string  = "amount=$amount";
        //cURL Request
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $key_id.':'.$key_secret);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        return $ch;
    }   

    // callback method
    public function callback() {   
    	if (!empty($this->request->getPost('razorpay_payment_id')) && !empty($this->request->getPost('merchant_order_id'))) {

    		$razorpay_payment_id 	= $this->request->getPost('razorpay_payment_id');
            $merchant_order_id 		= $this->request->getPost('merchant_order_id');

            $this->session->set('razorpay_payment_id', $this->request->getPost('razorpay_payment_id'));
            $this->session->set('merchant_order_id', $this->request->getPost('merchant_order_id'));
            $currency_code = 'INR';
            $amount = $this->request->getPost('merchant_total');

            $success = false;
            $error = '';
            try {                
                $ch = $this->curl_handler($razorpay_payment_id, $amount);
                //execute post
                $result = curl_exec($ch);
                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($result === false) {
                    $success = false;
                    $error = 'Curl error: '.curl_error($ch);
                } else {
                    $response_array = json_decode($result, true);
                        //Check success response
                        if ($http_status === 200 and isset($response_array['error']) === false) {
                            $success = true;
                        } else {
                            $success = false;
                            if (!empty($response_array['error']['code'])) {
                                $error = $response_array['error']['code'].':'.$response_array['error']['description'];
                            } else {
                                $error = 'RAZORPAY_ERROR:Invalid Response <br/>'.$result;
                            }
                        }
                }
                //close curl connection
                curl_close($ch);
            } catch (Exception $e) {
                $success = false;
                $error = 'Request to Razorpay Failed';
            }

            if ($success === true) {
                if(!empty($this->session->get('ci_subscription_keys'))) {
                    $this->session->unset('ci_subscription_keys');
                }
                if (!$order_info['order_status_id']) {
                    return redirect()->to($this->request->getPost('merchant_surl_id'));
                } else {
                    return redirect()->to($this->request->getPost('merchant_surl_id'));
                }

            } else {
                return redirect()->to($this->request->getPost('merchant_furl_id'));
            }
    	} else {
            echo 'An error occured. Contact site administrator, please!';
        }
    }

    public function success() {
        $data['title'] = 'Razorpay Success | TechArise';
        echo "<h4>Your transaction is successful</h4>";  
        echo "<br/>";
        echo "Transaction ID: ".$this->session->get('razorpay_payment_id');
        echo "<br/>";
        echo "Order ID: ".$this->session->get('merchant_order_id');
    }  
    public function failed() {
        $data['title'] = 'Razorpay Failed | TechArise';  
        echo "<h4>Your transaction got Failed</h4>";            
        echo "<br/>";
        echo "Transaction ID: ".$this->session->get('razorpay_payment_id');
        echo "<br/>";
        echo "Order ID: ".$this->session->get('merchant_order_id');
    }

}
