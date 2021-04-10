<?php
namespace Samadhan;


use DateTime;

class CouponCode extends GetOrder {

    public function __construct()
    {
        add_shortcode('pod_coupon_code_details_reports','Samadhan\CouponCode::smdn_woocommerce_coupon_code_details_reports');
    }



    public static function smdn_woocommerce_coupon_code_details_reports(){

        $postFrom_date = '';
        $postTo_date= '';

        $linesPerPage = 10;
        $currentPageNumber = 1;

        if(isset($_POST['currentPageNumber']) && !empty($_POST['currentPageNumber'])){
            $currentPageNumber = $_POST['currentPageNumber'];
        }

        //$filterData= array( 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);


        if(isset($_POST['searchButton']) && !empty($_POST['filterName'])){
            $searchName=$_POST['filterName'];

            //$filterData= array('search'=>$searchName, 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);
        }

        if( isset($_POST["from_date"]) && isset($_POST["to_date"])) {
            $postFrom_date = $_POST["from_date"];
            $postTo_date = $_POST["to_date"];

            $from_date = new DateTime($postFrom_date);
            $from_date->setTime(00, 00, 00);
            $getFromDate = $from_date->format('Y-m-d\TH:i:s');

            $to_date = new DateTime($postTo_date);
            $to_date->setTime(23, 59, 59);
            $getToDate = $to_date->format('Y-m-d\TH:i:s');


        }

        $filterData = array('after' => $getFromDate, 'before' => $getToDate, 'page' =>$currentPageNumber);



        self::smdn_coupon_code_details_form($postFrom_date,$postTo_date,$currentPageNumber);


        //$wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $wc_api = parent::smdn_order_API_call();

        $getOrder=$wc_api->get_orders($filterData);

        //$orders= self::woocommerce_get_all_orders($from_date,$to_date);
//var_dump($orders);

        $membership_table="<div><h2> COUPON CODE SUMMERY </h2>
                            
                            </div>
                            <br/> ";

        $membership_table .= '<table class="table">';
        $membership_table .='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                    
                                    <tr>
                                    <th rowspan="2">Coupon Code</th>
                                    <th rowspan="2">Description</th>
                                    <th rowspan="2">Order Count</th>
                                    <th rowspan="2">Product Total</th>
                                    <th rowspan="2">Total Tax</th>
                                    <th rowspan="2">Total Order Amount</th>
                                    </tr>
                                    
                                    </thead>';
        $membership_table.='<tbody>';


        $getCoupons=$wc_api->get_coupons();
        foreach($getCoupons as $coupon){


            $line_number++;


            $couponId=$coupon->code;
            $description=$coupon->description;
            $usage_count=$coupon->usage_count;
            $product_ids=$coupon->product_ids;

            $productAmount=$coupon->amount;
            $minimum_amount=$coupon->minimum_amount;
            $maximum_amount=$coupon->maximum_amount;





            $membership_table.="<tr>
                                      
                                       <td>$couponId</td>
                                       <td>$description</td>
                                       <td >$usage_count</td>  
                                       <td>$productAmount</td>                                            
                                       <td>" . wc_price($minimum_amount) . "</td>
                                       <td>" . wc_price($maximum_amount) . "</td>  
                                    
                                     
                                </tr>";



        }


        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                    <th scope="row" colspan="9"></th>                                
                                  
                                    </tfoot>';

        $membership_table.='</table>';

        $membership_table .="<div><h2> COUPON CODE DETAILS </h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table .='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="9"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">Sl#</th>
                                        <th rowspan="2">Order ID</th>
                                        <th rowspan="2">Customer ID</th>
                                        <th rowspan="2">Name</th>
                                        <th rowspan="2">Address</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip Code</th>
                                        <th rowspan="2">Product Total</th>
                                        <th rowspan="2">Tax Amount</th>
                                        <th rowspan="2">Order Total</th>
                                        <th rowspan="2">Coupon code</th>
                                        
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;


        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;

        foreach($getOrder as $order){
//_recorded_coupon_usage_counts

            // samadhan_pod_api_get_post_meta($order->id,'_recorded_coupon_usage_counts',true);
            if(!empty($order->coupon_lines)){
                $line_number++;

                $order_id=$order->id;
                $firstname= $order->billing->first_name;
                $lastname=$order->billing->last_name;
                $user_email =$order->billing->email;
                $user_phone =$order->billing->phone;
                $paid_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_created));
                $ship_to_city =$order->billing->city;
                $ship_to_state =$order->billing->state;
                $ship_to_zip =$order->billing->postcode;
                $ship_to_country =$order->billing->country;
                $ship_to_company =$order->billing->company;
                $order_status =$order->status;
                $order_ship_status =$order->billing->first_name;
                $order_paid_status =$order->payment_method;
                $order_customer_id =$order->customer_id;
                $order_billing_addresss1 =$order->billing->address_1;
                $order_billing_addresss2 =$order->billing->address_2;

                $order_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_paid) ); //. " *  " .$order->order_date;


                $couponCode=' ';
                foreach ($order->coupon_lines as $coupon){
                    $couponCode .=$coupon->code;
                }

                $item_quantity=0;
                $order_total=0;
                $tax_total=0;
                foreach ($order->line_items as $quantity){
                    $item_quantity +=$quantity->quantity;
                    $order_total +=$quantity->subtotal;
                    $tax_total +=$quantity->subtotal_tax;
                }

                $tax_total=wc_format_decimal($tax_total, 2);
                $order_total=wc_format_decimal($order_total, 2);
                $total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);

                $grand_total += $order_total;
                $grand_total_tax += $tax_total;

                $membership_table.="<tr>
                                       <td>$line_number</td>
                                       <td>$order_id</td>
                                       <td>$order_customer_id</td>
                                       <td>".$firstname." ".$lastname."</td>
                                       <td >".$order_billing_addresss1." ".$order_billing_addresss2."</td>  
                                       <td>$ship_to_city</td>
                                       <td>$ship_to_state</td>
                                       <td>$ship_to_zip</td>
                                       <td>$item_quantity</td>                                                       
                                       <td>" . wc_price($tax_total) . "</td>
                                       <td>" . wc_price($order_total) . "</td>  
                                      <td>$couponCode</td>
                                     
                                </tr>";

            }
        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                    <th scope="row" colspan="9">Page Total</th>
                                      <th>'.wc_price($grand_total_tax).'</th>                                  
                                      <th>'.wc_price($grand_total  ).'</th>                                  
                                      <th></th>
                                                                    
                                    </tr>
                                      <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="9"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;


    }

    public  static  function smdn_coupon_code_details_form($from_date, $to_date,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER COUPON CODE DETAILS DATES</caption>
                <thead>
                <tr>
                    <th  style="width: 1%; text-align: center">Date From</th>
                    <th  style="width: 1%; text-align: center ">Date To</th>
                    <th></th>
                    <th  style="width: 1%; text-align: center">Search</th>
                    <th  style="width: 1%; text-align: center">Download</th>
                </tr>
                </thead>
                </tbody>

                <tr style="background-color: #222222;">
                    <td style="vertical-align: top;; text-align: center">
                        <input id="pod_date_from"
                               type="date"
                               name='from_date'
                               value="<?php echo $from_date; ?>"
                               class="form-control"
                               placeholder="dd/mm/yyyy *"
                               required="required"
                               required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"
                               data-error="date is required."
                               style="height:32px; text-align: center;
    }"
                        >
                    </td>

                    <td style="vertical-align: top;; text-align: center">
                        <input id="pod_date_to"
                               type="date"
                               name='to_date'
                               value="<?php echo $to_date; ?>"
                               class="form-control"
                               placeholder="dd/mm/yyyy *"
                               required="required"
                               required pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"
                               data-error="date is required."
                               style="height:32px; text-align: center;
    }"
                        >
                    </td>
                    <td>
                        <input type='hidden' placeholder='Enter Name' name='filterName' value="<?php echo $currentPageNumber; ?>">
                    </td>

                    <td style="vertical-align: top; text-align: center">
                        <input type="submit" id="searchButton" class="btn btn-success btn-send" name="samadhan-pod-group-leader-search" value="Search">
                    </td>
                    <td style="vertical-align: top; text-align: center">

                        <input type="submit" class="btn btn-success btn-send" name="samadhan-woocommerce-report-download" value="Download CSV">
                    </td>
                </tr>

                </tbody>
            </table>
        </form>

        <?php
    }


}

new CouponCode();