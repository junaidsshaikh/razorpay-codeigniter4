<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment Gateway</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>

<?php
$description        = "Product Description";
$txnid              = date("YmdHis");     
$key_id             = "YOUR_KEY_ID";
$currency_code      = $currency_code;            
$total              = (1* 100); // 100 = 1 indian rupees
$amount             = 1;
$merchant_order_id  = "ABC-".date("YmdHis");
$card_holder_name   = 'Junaid Shaikh';
$email              = 'coexistech@gmail.com';
$phone              = '9158876092';
$name               = "RazorPay Infovistar";
?>

    <div class="container">
        <div class="page-header">
            <h1>Pay with Razorpay</h1>
        </div>
        <div class="page-body">
            <form name="razorpay-form" id="razorpay-form" action="<?php echo $callback_url; ?>" method="POST">
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
                <input type="hidden" name="merchant_order_id" id="merchant_order_id" value="<?php echo $merchant_order_id; ?>"/>
                <input type="hidden" name="merchant_trans_id" id="merchant_trans_id" value="<?php echo $txnid; ?>"/>
                <input type="hidden" name="merchant_product_info_id" id="merchant_product_info_id" value="<?php echo $description; ?>"/>
                <input type="hidden" name="merchant_surl_id" id="merchant_surl_id" value="<?php echo $surl; ?>"/>
                <input type="hidden" name="merchant_furl_id" id="merchant_furl_id" value="<?php echo $furl; ?>"/>
                <input type="hidden" name="card_holder_name_id" id="card_holder_name_id" value="<?php echo $card_holder_name; ?>"/>
                <input type="hidden" name="merchant_total" id="merchant_total" value="<?php echo $total; ?>"/>
                <input type="hidden" name="merchant_amount" id="merchant_amount" value="<?php echo $amount; ?>"/>
            </form>

            <table width="100%">
                <tr>
                    <th>No.</th>
                    <th>Product Name</th>
                    <th class="text-right">Cost</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>HeadPhones</td>
                    <td class="text-right">&#x20B9; 1.00</td>
                </tr>
            </table>
            <div class="mt-2 text-right">
                <input  id="pay-btn" type="submit" onclick="razorpaySubmit(this);" value="Buy Now" class="btn btn-primary" />
            </div>
        </div>
    </div>

    

    

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var options = {
            key:            "<?php echo $key_id; ?>",
            amount:         "<?php echo $total; ?>",
            name:           "<?php echo $name; ?>",
            description:    "Order # <?php echo $merchant_order_id; ?>",
            netbanking:     true,
            currency:       "<?php echo $currency_code; ?>", // INR
            prefill: {
                name:       "<?php echo $card_holder_name; ?>",
                email:      "<?php echo $email; ?>",
                contact:    "<?php echo $phone; ?>"
            },
            notes: {
                soolegal_order_id: "<?php echo $merchant_order_id; ?>",
            },
            handler: function (transaction) {
                document.getElementById('razorpay_payment_id').value = transaction.razorpay_payment_id;
                document.getElementById('razorpay-form').submit();
            },
            "modal": {
                "ondismiss": function(){
                    location.reload()
                }
            }
        };

        var razorpay_pay_btn, instance;
        function razorpaySubmit(el) {
            if(typeof Razorpay == 'undefined') {
                setTimeout(razorpaySubmit, 200);
                if(!razorpay_pay_btn && el) {
                    razorpay_pay_btn    = el;
                    el.disabled         = true;
                    el.value            = 'Please wait...';  
                }
            } else {
                if(!instance) {
                    instance = new Razorpay(options);
                    if(razorpay_pay_btn) {
                    razorpay_pay_btn.disabled   = false;
                    razorpay_pay_btn.value      = "Pay Now";
                    }
                }
                instance.open();
            }
        }  
    </script>

</body>
</html>