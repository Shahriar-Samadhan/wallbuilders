<?php
/*
	Plugin Name: Platform Operation Dashboard
	Plugin URI:  http://samadhan.com.bd
	Description: Simple Platform Operation Dashboard Woocommerce Reports.
	Version:     3.0.0
	Author:      samadhan
	Author URI:  http://samadhan.com.bd
*/


use Samadhan\PaginationBuilder;

require_once ('includes/helpers/queryBuilder.php');
require_once ('includes/helpers/paginationBuilder.php');
include_once ('includes/helpers/order.php');
//require_once ('includes/helpers/paginationBuilderVue.php');

//finance report
include_once ('includes/donation-finance-report.php');
include_once ('includes/sales-tax-report.php');
include_once ('includes/all-orders-report.php');
include_once ('includes/all-paid-orders-report.php');
include_once ('includes/all-pending-orders-report.php');
include_once ('includes/coupon-code-report.php');
include_once ('includes/rush-order-report.php');
include_once ('includes/response-code-report.php');
include_once ('includes/order-by-product-report.php');
include_once ('includes/order-by-shipping-method-report.php');


//donor report
include_once ('includes/donor/authorize-exception-report.php');
include_once ('includes/donor/authorize-transaction-without-donor.php');
include_once ('includes/donor/major-donor-report.php');




/*
$columnsToSearchArray = array("FirstName", "LastName");
$OrderBy = array(
        "TotalAmount" => "DESC",
        "FirstName" => "ASC"
);
$test = new Samadhan\QueryBuilder("MajorDonorHistory", 20,1,$OrderBy);
$searchData = "Vernon";
$test->BuildSearchQuery($searchData, $columnsToSearchArray);
$totalRecords = $test->GetTotal();
echo("Total Records = " . $totalRecords . "<br>");
$result = $test->ExecuteQuery();
foreach ($result as $item) {
    echo($item->FirstName . "<br>");
}
 */


class SmdnAccountsWoocommerceReport{

    public $consumer_key = '';
    public $consumer_secret = '';
    public $store_url = '';


    public  function __construct(){

         $this->get_js_and_css_files();

         /*

         //$this->consumer_key = get_option('SAMADHAN_STORE_CONSUMER_KEY');
         //$this->consumer_secret = get_option('SAMADHAN_STORE_CONSUMER_SECRET');
         //$this->store_url = get_option('SAMADHAN_STORE_API_ENDPOINT');

        //add_shortcode('pod_sales_tax_reports',array($this,'smdn_woocommerce_sale_tax_report'));
        //add_shortcode('pod_allorders_reports',array($this,'smdn_woocommerce_allorders_reports'));
        //add_shortcode('pod_orders_by_product_reports',array($this,'smdn_woocommerce_orders_by_product_reports'));
        //add_shortcode('pod_order_by_shipping_method_reports',array($this,'smdn_woocommerce_orderby_shipping_method_reports'));
        //add_shortcode('pod_rush_order_reports',array($this,'smdn_woocommerce_rush_order_reports'));
        //add_shortcode('pod_response_code_reports',array($this,'smdn_woocommerce_response_code_reports'));
        //add_shortcode('pod_coupon_code_details_reports',array($this,'smdn_woocommerce_coupon_code_details_reports'));
        //add_shortcode('pod_all_nop_orders_pending_reports',array($this,'smdn_woocommerce_all_nop_orders_pending_reports'));
        //add_shortcode('pod_paid_orders_partial_payment_reports',array($this,'smdn_woocommerce_paid_orders_partial_payment_reports'));


        //add_shortcode('pod_major_donor_reports',array($this,'smdn_woocommerce_major_donor_reports'));
        //add_shortcode('pod_maintain_authorize_exception_reports',array($this,'smdn_woocommerce_maintain_authorize_exception_reports'));
        //add_shortcode('pod_maintain_authorize_transactions_without_donors',array($this,'smdn_woocommerce_maintain_authorize_exception_reports_without_donor'));


         */


    }


    protected function get_js_and_css_files(){
        $ver='1.1.0' . rand(1, 100000);
        $stylePath = plugins_url('assets/style.css', __FILE__);
        $scriptPath = plugins_url('assets/scripts.js', __FILE__);
        wp_register_style('smdn_pod_style', $stylePath ,[],$ver);
        wp_enqueue_style('smdn_pod_style');

        wp_register_script('smdn_pod_script', $scriptPath ,['jquery'],$ver);
        wp_enqueue_script('smdn_pod_script');
    }

    // Sale tax reports
    public function smdn_woocommerce_sale_tax_report(){


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




        self::smdn_sales_tax_filter_form($postFrom_date,$postTo_date,$state,$currentPageNumber);


        //$orders= self::woocommerce_get_all_orders($from_date,$to_date);

//var_dump($orders);

        $membership_table="<div><h2> ALL SALES TAX  REPORTS</h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="13"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order Id</th>
                                        <th rowspan="2">Paid Date</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">Ship To City</th>
                                        <th rowspan="2">Ship To State</th>
                                        <th rowspan="2">Ship To Zip</th>
                                        <th rowspan="2">Ship To Country</th>
                                        <th rowspan="2">Order Amount</th>
                                        <th rowspan="2">Donation</th>
                                        <th rowspan="2">Tax</th>
                                        <th rowspan="2">Taxable Sales</th>
                                        <th rowspan="2">Shipping</th>
                                        <th rowspan="2">Total Taxable Shipping</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $total_order_count = $wc_api->get_reports_total();
        $current_page = 0;
        $count_order_per_page = 0;
        $order_per_page = 0;



        $getOrder=$wc_api->get_orders($filterData);



        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;
        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;
        foreach($getOrder as $order ){

            //var_dump($order);
            $line_number++;
            $count_order_per_page++;
            //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
            $order_per_page = $current_page-1 + $count_order_per_page;

            $order_id=$order->id;

            $shipping_lines=$order->shipping_lines;
            //var_dump($shipping_lines);
            foreach ($shipping_lines as $shipping_line)
            {
                $shipping_line->total_tax;
                $shipping_tax = $shipping_line->shipping_tax;
            }



            $firstname= $order->shipping->first_name; //billing_first_name;
            $lastname= $order->shipping->last_name; //billing_last_name;
            $user_email =$order->shipping->email;
            $paid_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_paid) );
            $ship_to_city =$order->shipping->city;
            $ship_to_state =$order->shipping->state;
            $ship_to_zip =$order->shipping->postcode;
            $ship_to_country =$order->shipping->country;
            $ship_to_company =$order->shipping->company;
            $order_status =$order->status;
            $order_ship_status =$order->payment_method;
            $order_paid_status =$order->payment_method;
            $order_customer_id =$order->customer_id;
            $order_billing_addresss1 =$order->shipping->address_1;
            $order_billing_addresss2 =$order->shipping->address_2;

            $order_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_paid) ); //. " *  " .$order->order_date;

            $tax_total=wc_format_decimal($order->total_tax, 2);
            $order_total=wc_format_decimal($order->total, 2);
            $total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);

            $grand_total += $order_total;
            $grand_total_tax += $tax_total;

            $total_taxable_shipping = $order_total + $shipping_tax;
            $grand_taxable_shipping +=$total_taxable_shipping;

            //var_dump($grand_taxable_shipping);


            /*
                   $users = Samadhan\course_helpers::get_user_course_data($user_id);
                   foreach ($users as $user) {
                       $total_cpd = $user['total_ceus'];
                   }*/




            $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td>$order_id</td>
                                      <td>$paid_date</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_company</td>
                                      <td>$ship_to_city</td>
                                      <td>$ship_to_state</td>
                                      <td>$ship_to_zip</td>
                                      <td>$ship_to_country</td>
                                      <td >" . wc_price($order_total) . "</td>
                                      <td >" . wc_price(0) . "</td>   
                                      <td>" . wc_price($tax_total) . "</td>                                                       
                                      <td>" . wc_price($order_total) . "</td>
                                      <td>" .wc_price($shipping_tax)."</td>
                                      <td>" . wc_price($order_total) . "</td>
                                </tr>";

        }


        $membership_table.=' </tbody> ';


        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="10">Page Total</th>
                                      <th>'.wc_price($grand_total).'</th>  
                                      <th>'.wc_price(0).'</th>  
                                      <th>'.wc_price($grand_total_tax).'</th>                                  
                                      <th>'.wc_price($grand_total).'</th>  
                                      <th>'.wc_price(0).'</td> 
                                      <th>'.wc_price($grand_taxable_shipping).'</td>                                  
                                    </tr>
                                     <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="13">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_sales_tax_filter_form($from_date, $to_date,$state,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER SALES TAX DATES</caption>
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





    // All order reports
    public function smdn_woocommerce_allorders_reports(){


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



        self::smdn_allorders_reports_filter_form($postFrom_date,$postTo_date,$currentPageNumber);



        ///$orders= self::woocommerce_get_all_orders($from_date,$to_date);
//var_dump($orders);
        $membership_table="<div><h2> ALL ORDER REPORTS</h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="15"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order#</th>
                                        <th rowspan="2">Order Status</th>
                                        <th rowspan="2">Paid Status</th>
                                        <th rowspan="2">Ship Status</th>
                                        <th rowspan="2">Customer#</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">Address 1</th>
                                        <th rowspan="2">Address 2</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip</th>
                                        <th rowspan="2">Order Date</th>
                                        <th rowspan="2">Paid Date</th>
                                        <th rowspan="2">PO</th>
                                        <th rowspan="2">Amount</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $total_order_count = $wc_api->get_reports_total();
        $current_page = 0;
        $count_order_per_page = 0;
        $order_per_page = 0;

        $getOrder=$wc_api->get_orders($filterData);

        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;
        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;
        foreach($getOrder as $order ){
            $line_number++;
            $count_order_per_page++;
            //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
            $order_per_page = $current_page-1 + $count_order_per_page;
           //var_dump($order);
            $sl_no++;
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
            //$order_ship_status =$order->billing->first_name;
            $order_paid_status =$order->payment_method;
            $order_customer_id =$order->customer_id;
            $order_billing_addresss1 =$order->billing->address_1;
            $order_billing_addresss2 =$order->billing->address_2;

            $order_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_paid) ); //. " *  " .$order->order_date;

            $tax_total=wc_format_decimal($order->total_tax, 2);
            $order_total=wc_format_decimal($order->total, 2);
            $total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);

            $grand_total += $order_total;
            $grand_total_tax += $tax_total;

            /*
                   $users = Samadhan\course_helpers::get_user_course_data($user_id);
                   foreach ($users as $user) {
                       $total_cpd = $user['total_ceus'];
                   }*/




            $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td>$order_id</td>
                                      <td>$order_status</td>
                                      <td>$order_paid_status</td>
                                      <td> </td>
                                      <td>$order_customer_id</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_company</td>
                                      <td>$order_billing_addresss1</td>
                                      <td>$order_billing_addresss2</td>
                                      <td>$ship_to_city</td>
                                      <td>$ship_to_state</td>
                                      <td>$ship_to_zip</td>
                                      <td >$order_date</td>
                                      <td >$paid_date</td>   
                                      <td> </td>                                                       
                                      <td>" . wc_price($order_total) . "</td>
                                     
                                </tr>";

        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="15">Page Total</th>
                                      <th></th>                                  
                                      <th> </td>
                                      <th>'.wc_price($grand_total).'</td>                                  
                                    </tr>
                                     <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="15">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_allorders_reports_filter_form($from_date, $to_date,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ALL ORDER REPORTS DATES</caption>
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





    //  order by product reports
    public function smdn_woocommerce_orders_by_product_reports(){


        $product_id = 0;
        $postFrom_date = '';
        $postTo_date= '';

        $linesPerPage = 10;
        $currentPageNumber = 1;



        if(isset($_POST['currentPageNumber']) && !empty($_POST['currentPageNumber'])){
            $currentPageNumber = $_POST['currentPageNumber'];
        }

        $filterData= array( 'per_page'=>$linesPerPage,  'page'=>(int)$currentPageNumber);
        if(isset($_POST['product_id']) && !empty($_POST['product_id'])){
            $searchName=$_POST['product_id'];

            //$filterData= array('search'=>$searchName, 'product'=>$product_id, 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);
        }

        if( isset($_POST["from_date"]) && isset($_POST["to_date"]) && isset($_POST["product_id"])) {
            $postFrom_date = $_POST["from_date"];
            $postTo_date = $_POST["to_date"];
            $product_id = $_POST["product_id"];
            //$product_name = $_POST["product_id"];

            $from_date = new DateTime($postFrom_date);
            $from_date->setTime(00, 00, 00);
            $getFromDate = $from_date->format('Y-m-d\TH:i:s');

            $to_date = new DateTime($postTo_date);
            $to_date->setTime(23, 59, 59);
            $getToDate = $to_date->format('Y-m-d\TH:i:s');

            //$filterData = array('after' => $getFromDate, 'product'=>$product_id, 'before' => $getToDate);
        }

        $filterData = array('after' => $getFromDate, 'product'=>$product_id, 'before' => $getToDate,  'page'=>$currentPageNumber);

        self::smdn_orders_by_product_filter_form($product_id,$postFrom_date,$postTo_date,$currentPageNumber);

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $total_order_count = $wc_api->get_reports_total();
        $current_page = 0;
        $count_order_per_page = 0;
        $order_per_page = 0;
        //var_dump($order_count);

        //$paginationBuilder = new \Samadhan\PaginationBuilder($linesPerPage,$currentPageNumber,$order_count,15);

     //   $orders= self::woocommerce_get_all_orders_by_product_name($product_id,$postFrom_date,$postTo_date);
//var_dump($orders);

        $membership_table="<div><h2> ORDER BY PRODUCT</h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">

                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order ID</th>
                                        <th rowspan="2">Customer#</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Order Date</th>
                                        <th rowspan="2">Paid Date</th>
                                        <th rowspan="2">Purchase Order</th>
                                        <th rowspan="2">Product Name</th>
                                        <th rowspan="2">Order Qty</th>
                                        <th rowspan="2">Product Price</th>
                                        <th rowspan="2">Extended Price</th>
                                        <th rowspan="2">Order Subtotal</th>
                                        <th rowspan="2">Order Total</th>
                                        <th rowspan="2">Qty Returned</th>
                                        <th rowspan="2">Amt Returned</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $getOrder=$wc_api->get_orders($filterData);


        //$getOrd=$wc_api->get_products();
//var_dump($getOrd);
        $order_subtotal = 0;
        $order_quantity = 0;
        $wo_gst = 0;
        $total_order=0;

        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;
        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;


        foreach($getOrder as $order ){
            $line_number++;
             //var_dump($order);
            $sl_no++;

            $count_order_per_page++;
            //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
            $order_per_page = $current_page-1 + $count_order_per_page;

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

            $tax_total=wc_format_decimal($order->total_tax, 2);
            $order_total_price=wc_format_decimal($order->total, 2);
            //var_dump($tax_total);

            //$total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);

            //$grand_total += $order_total;
            $grand_total_tax += $tax_total;
            //$wo_gst += $total_wo_gst;

            $item_quantity=0;
            $order_total=0;
            $tax_total=0;
            $productName='';


            foreach ($order->line_items as $quantity){

                //var_dump($order->line_items);
                $item_quantity +=$quantity->quantity;
                $order_total +=$quantity->subtotal;

                $id_product = $quantity->product_id;
                $get_product = $wc_api->get_product($id_product);
                $product_price = $get_product->price;


                //var_dump($product_price);

                $tax_total +=$quantity->subtotal_tax;
                $productName .=$quantity->name;


            }
            $extended_price = $product_price * $item_quantity;


            $total_extended_price += $extended_price;
            //var_dump($total_extended_price);


            //$extended_price = $product_price * $item_quantity;


            //$total_extended_price += $extended;


            $total_product_price += $product_price;
            $total_order_amount +=$order_total_price;
            $total_order+=$order_total;
            $order_subtotal += $tax_total;
            $order_quantity+=$item_quantity ;



            $membership_table.="<tr>
                                       <td>$line_number</td>
                                       <td>$order_id</td>
                                       <td>$order_customer_id</td>
                                       <td>$ship_to_state</td>
                                       <td >$order_date</td>
                                       <td >$paid_date</td>  
                                       <td> </td>
                                       <td>$productName</td>
                                       <td>$item_quantity</td>
                                       <td>" . wc_price($product_price)."</td>
                                       <td>" . wc_price($extended_price) . "</td>                                                       
                                       <td>$tax_total</td>
                                       <td>" . wc_price($order_total_price) . "</td>  
                                      <td>0</td>
                                      <td>0</td>
                                     
                                </tr>";

        }


        $membership_table.=' </tbody> ';

        //$membership_table.= $paginationBuilder->GetPaginationRow();

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="8">Page Total</th>
                                      <th>'.$order_quantity.'</td>
                                      <th>'.wc_price($total_product_price).'</th>                                  
                                      <th>'.wc_price($total_extended_price).'</td>
                                      <th>'.$order_subtotal.'</td>
                                      <th>'.wc_price($total_order_amount).'</th>                                  
                                      <th>0</td>
                                      <th>0</td>
                                      <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="13">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                      

                                     <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>        
                                                             
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_orders_by_product_filter_form($product_id,$from_date, $to_date,$currentPageNumber){


        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ORDER BY PRODUCT DATES</caption>
                <thead>
                <tr>
                    <th style="width: 1%; text-align: center">Product ID</th>
                    <th  style="width: 1%; text-align: center">Date From</th>
                    <th  style="width: 1%; text-align: center ">Date To</th>
                    <th></th>
                    <th  style="width: 1%; text-align: center">Search</th>
                    <th  style="width: 1%; text-align: center">Download</th>
                </tr>
                </thead>
                </tbody>

                <tr style="background-color: #222222;">
                    <td style="vertical-align: top; text-align: center">
                        <input id="product_id"
                               type="text"
                               name='product_id'
                               value="<?php echo $product_id; ?>"
                               class="form-control"
                               required="required"
                               placeholder="Enter Product ID"
                               data-error="ID is requred."
                               style="height:32px; text-align: center;
}"
                        >
                    </td>
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





    // All order by shipping method reports
    public function smdn_woocommerce_orderby_shipping_method_reports(){


        //$datetime = new DateTime();
        //$shipping_method = '';
        //$postFrom_date = $datetime->format('Y-m-d\TH:i:s');
        $postFrom_date = '';
        $postTo_date = '';



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
            //$shipping_method = $_POST["shipping_method"];

            $from_date = new DateTime($postFrom_date);
            $from_date->setTime(00, 00, 00);
            $getFromDate = $from_date->format('Y-m-d\TH:i:s');

            $to_date = new DateTime($postTo_date);
            $to_date->setTime(23, 59, 59);
            $getToDate = $to_date->format('Y-m-d\TH:i:s');


        }

        $filterData = array('after' => $getFromDate, 'before' => $getToDate, 'page' =>$currentPageNumber);

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $total_order_count = $wc_api->get_reports_total();
        //$current_page = 0;

        //var_dump($filterData);
        $getOrder=$wc_api->get_orders($filterData);


//        if( isset($_POST["shipping_method"])) $from_date = $_POST["shipping_method"];
//        if( isset($_POST["from_date"])) $from_date = $_POST["from_date"];
//        if( isset($_POST["to_date"])) $to_date = $_POST["to_date"];

        self::smdn_orderby_shipping_method_filter_form($postFrom_date,$postTo_date,$wc_api,$shipping_method,$currentPageNumber);


      //  $orders= self::woocommerce_get_all_orders($from_date,$to_date);
//var_dump($orders);

        $membership_table="<div><h2> SHIPPING METHOD</h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="15"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order ID</th>
                                        <th rowspan="2">Order Status</th>
                                        <th rowspan="2">Payment Status</th>
                                        <th rowspan="2">Shipping Status</th>
                                        <th rowspan="2">Customer#</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Ship Addr1</th>
                                        <th rowspan="2">Ship Addr2</th>
                                        <th rowspan="2">Ship City</th>
                                        <th rowspan="2">Ship State</th>
                                        <th rowspan="2">Ship Zip</th>
                                        <th rowspan="2">Order Date</th>
                                        <th rowspan="2">Paid Date</th>
                                        <th rowspan="2">PO</th>
                                        <th rowspan="2">Order Amount</th>
                                        <th rowspan="2">Shipping Method</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;




        $get_shipping_method = $wc_api->get_shipping_method();
        //var_dump($get_shipping_method);

        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;
        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;
        $count_order_per_page = 0;
        $order_per_page = 0;

        foreach($getOrder as $order ){

            //var_dump($order);
            $line_number++;
            $count_order_per_page++;
            //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
            $order_per_page = $current_page-1 + $count_order_per_page;

            $order_id=$order->id;

            $shipping_lines=$order->shipping_lines;
            foreach ($shipping_lines as $shipping_line)
            {
                $shipping_line->method_title;
                $shipping_method_title= $shipping_line->method_title;
            }


            $firstname= $order->shipping->first_name; //billing_first_name;
            $lastname= $order->shipping->last_name; //billing_last_name;
            $user_email =$order->shipping->email;
            $paid_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_paid) );
            $ship_to_city =$order->shipping->city;
            $ship_to_state =$order->shipping->state;
            $ship_to_zip =$order->shipping->postcode;
            $ship_to_country =$order->shipping->country;
            $ship_to_company =$order->shipping->company;
            $order_status =$order->status;
            $order_ship_status =$order->payment_method;
            $order_paid_status =$order->payment_method;
            $order_customer_id =$order->customer_id;
            $order_billing_addresss1 =$order->shipping->address_1;
            $order_billing_addresss2 =$order->shipping->address_2;

            $order_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_created) ); //. " *  " .$order->order_date;

            $tax_total=wc_format_decimal($order->total_tax, 2);
            $order_total=wc_format_decimal($order->total, 2);
            $total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);

            $grand_total += $order_total;
            $grand_total_tax += $tax_total;

            $order_amount = $order->total;

            $total_order_amount +=$order_amount;

            /*
                   $users = Samadhan\course_helpers::get_user_course_data($user_id);
                   foreach ($users as $user) {
                       $total_cpd = $user['total_ceus'];
                   }*/




            $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td>$order_id</td>
                                      <td>$order_status</td>
                                      <td>$order_paid_status</td>
                                      <td> </td>
                                      <td>$order_customer_id</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$order_billing_addresss1</td>
                                      <td>$order_billing_addresss2</td>
                                      <td>$ship_to_city</td>
                                      <td>$ship_to_state</td>
                                      <td>$ship_to_zip</td>
                                      <td >$order_date</td>
                                      <td >$paid_date</td>   
                                      <td > </td>   
                                      <td>" . wc_price($order_amount) . "</td>                                                       
                                      <td>$shipping_method_title</td>
                                     
                                </tr>";

        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="15">Page Total</th>
                                      <th></th>                                  
                                      <th>'.wc_price($total_order_amount).'</td>
                                      <th></td>                                  
                                    </tr>
                                      <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      
                                      <th scope="row" colspan="16">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                                                      
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_orderby_shipping_method_filter_form($from_date, $to_date, $wc_api, $search_name,$currentPageNumber){

        $shipping_methods=$wc_api->get_shipping_method();


        $options="<option value='-1'>Select</option>";

        foreach ($shipping_methods as $shipping_title){

            $shipping = $shipping_title->title;
            $options .="<option value='$shipping'>$shipping</option>";
        }


        ?>

        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">

            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ORDER BY SHIPPING METHOD DATES</caption>
                <thead>
                    <tr>
                        <th style="width: 1%; text-align: center">Date From</th>
                        <th style="width: 1%; text-align: center ">Date To</th>
                        <th style="width: 1%; text-align: center ">Select Shipping Method</th>
                        <th></th>
                        <th style="width: 1%; text-align: center">Search</th>
                        <th style="width: 1%; text-align: center">Download</th>
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



                    <td style="vertical-align: top;; text-align: center">
                        <select id="shipping_method">
                            <?php echo $options; ?>
                        </select>

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





    // All Rush order reports
    public function smdn_woocommerce_rush_order_reports(){



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


        self::smdn_rush_order_filter_form($postFrom_date,$postTo_date,$currentPageNumber);


        //$orders= self::woocommerce_get_all_orders($from_date,$to_date);
        $membership_table="<div><h2> RUSH ORDER </h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="14"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order ID</th>
                                        <th rowspan="2">Order Date</th>
                                        <th rowspan="2">Paid Date</th>
                                        <th rowspan="2">Customer#</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">Address Line 1</th>
                                        <th rowspan="2">Address Line 2</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip Code</th>
                                        <th rowspan="2">Purchase Order</th>
                                        <th rowspan="2">Order Status</th>
                                        <th rowspan="2">Shipping Status</th>
                                        <th rowspan="2">Order Total</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $total_order_count = $wc_api->get_reports_total();
        $current_page = 0;
        $count_order_per_page = 0;
        $order_per_page = 0;

        $getOrder=$wc_api->get_orders($filterData);

        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;
        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;



        foreach($getOrder as $order ){

            $line_number++;

            $count_order_per_page++;
            //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
            $order_per_page = $current_page-1 + $count_order_per_page;

            $order_id=$order->id;
            $firstname= $order->billing->first_name;
            $lastname=$order->billing->last_name;
            $user_email =$order->billing->email;
            $user_phone =$order->billing->phone;
            $paid_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_paid));
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

            $order_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_created) ); //. " *  " .$order->order_date;

            $tax_total=wc_format_decimal($order->total_tax, 2);
            $order_total=wc_format_decimal($order->total, 2);
            $total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);

            $grand_total += $order_total;
            $grand_total_tax += $tax_total;

            /*
                   $users = Samadhan\course_helpers::get_user_course_data($user_id);
                   foreach ($users as $user) {
                       $total_cpd = $user['total_ceus'];
                   }*/




            $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td>$order_id</td>
                                      <td>$order_date</td>
                                      <td>$paid_date</td>
                                      <td>$order_customer_id</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_company</td>
                                      <td>$order_billing_addresss1</td>
                                      <td>$order_billing_addresss2</td>
                                      <td>$ship_to_city</td>
                                      <td>$ship_to_state</td>
                                      <td>$ship_to_zip</td>
                                      <td > </td> 
                                      <td >$order_status</td>
                                      <td > </td>                                       
                                     <td>" . wc_price($order_total) . "</td>
                                     
                                </tr>";

        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="14">Page Total</th>
                                      <th></th>                                  
                                      <th></td>
                                      <th>'.wc_price($grand_total).'</td>                                  
                                    </tr>
                                     <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="14">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_rush_order_filter_form($from_date, $to_date,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER RUSH ORDER DATES</caption>
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





    //  response code order reports
    public function smdn_woocommerce_response_code_reports(){



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

        self::smdn_response_code_filter_form($postFrom_date,$postTo_date,$currentPageNumber);


        // $orders= self::woocommerce_get_all_orders($from_date,$to_date);
//var_dump($orders);

        $membership_table="<div><h2> RESPONSE CODE </h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="12"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order ID</th>
                                        <th rowspan="2">Order Date</th>
                                        <th rowspan="2">Customer ID</th>
                                        <th rowspan="2">Name</th>
                                        <th rowspan="2">Address</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip Code</th>
                                        <th rowspan="2">Email</th>
                                        <th rowspan="2">Product Total</th>
                                        <th rowspan="2">Tax Amount</th>
                                        <th rowspan="2">Order Total</th>
                                        <th rowspan="2">What prompted order?</th>
                                        <th rowspan="2">Response code</th>
                                        
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $total_order_count = $wc_api->get_reports_total();
        $current_page = 0;
        $count_order_per_page = 0;
        $order_per_page = 0;

        $getOrder=$wc_api->get_orders($filterData);

        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;

        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;

        foreach($getOrder as $order ){
            var_dump($order);
            $order_id=$order->id;
            //$order_id= 3517;
            $response_code = samadhan_pod_api_get_post_meta($order_id, "response_code", true);
            $prompted_code = samadhan_pod_api_get_post_meta($order_id, "additional_prompted", true);

            //var_dump($order_id);





            if(!empty($response_code)){

                $line_number++;

                $count_order_per_page++;
                //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
                $order_per_page = $current_page-1 + $count_order_per_page;

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

                $order_total_amount = $order->total;


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

                $grand_total += $order_total_amount;
                $grand_total_tax += $tax_total;

                $membership_table.="<tr>
                                       <td>$line_number</td>
                                       <td>$order_id</td>
                                       <td >$order_date</td>
                                       <td>$order_customer_id</td>
                                       <td>".$firstname." ".$lastname."</td>
                                      
                                       <td >".$order_billing_addresss1." ".$order_billing_addresss2."</td>  
                                       <td>$ship_to_city</td>
                                       <td>$ship_to_state</td>
                                       <td>$ship_to_zip</td>
                                       <td>$user_email</td>
                                       <td>$item_quantity</td>                                                       
                                       <td>" . wc_price($tax_total) . "</td>
                                       <td>" . wc_price($order_total_amount) . "</td>  
                                      <td>$prompted_code</td>
                                      <td>$response_code</td>
                                     
                                </tr>";

            }
        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="11">Page Total</th>
                                      <th>'.wc_price($grand_total_tax).'</th>                                  
                                      <th>'.wc_price($grand_total  ).'</th>                                  
                                      <th></th>
                                      <th></th>                                  
                                    </tr>
                                      <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="12">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_response_code_filter_form($from_date, $to_date,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER RESPONSE CODE DATES</caption>
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




    //  response code  order details reports
    public function smdn_woocommerce_coupon_code_details_reports(){

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


        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

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





    // All pending order reports
    public function smdn_woocommerce_all_nop_orders_pending_reports(){


        $postFrom_date = '';
        $postTo_date= '';

        $linesPerPage = 10;
        $currentPageNumber = 1;

        if(isset($_POST['currentPageNumber']) && !empty($_POST['currentPageNumber'])){
            $currentPageNumber = $_POST['currentPageNumber'];
        }
            //$filterData= array( 'per_page'=>$linesPerPage, 'status'=>'pending', 'page'=>(int)$currentPageNumber);
        if(isset($_POST['searchButton']) && !empty($_POST['filterName'])){
            $searchName=$_POST['filterName'];

            //$filterData= array('search'=>$searchName,'status'=>'pending', 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);
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
        $filterData = array('after' => $getFromDate, 'status'=>'pending','before' => $getToDate,'page' =>$currentPageNumber);



        self::smdn_all_nop_orders_pending_filter_form($postFrom_date,$postTo_date,$currentPageNumber);


       // $orders= self::woocommerce_get_all_orders($from_date,$to_date);
//var_dump($orders);

        $membership_table="<div><h2> ALL PENDING REPORTS</h2>

                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="10"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order ID</th>
                                        <th rowspan="2">Order Status</th>
                                        <th rowspan="2">Customer#</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Order Date</th>
                                        <th rowspan="2">Purchase Order</th>
                                        <th rowspan="2">Order Amount</th>
                                        <th rowspan="2">Total Payments</th>
                                        <th rowspan="2">Balance</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $total_order_count = $wc_api->get_reports_total();
        $current_page = 0;
        $count_order_per_page = 0;
        $order_per_page = 0;

        $getOrder=$wc_api->get_orders($filterData);

        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;
        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;

        foreach($getOrder as $order ){
            $line_number++;

            //var_dump($order);

            $count_order_per_page++;
            //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
            $order_per_page = $current_page-1 + $count_order_per_page;

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

            $tax_total=wc_format_decimal($order->total_tax, 2);
            $order_total=wc_format_decimal($order->total, 2);
            $total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);


            $grand_total += $order_total;
            $grand_total_tax += $tax_total;


            /*
                   $users = Samadhan\course_helpers::get_user_course_data($user_id);
                   foreach ($users as $user) {
                       $total_cpd = $user['total_ceus'];
                   }*/




            $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td>$order_id</td>
                                      <td>$order_status</td>
                                      <td>$order_customer_id</td>
                                      <td>$ship_to_company</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_state</td>
                                      <td >$order_date</td>
                                      <td >$paid_date</td>   
                                      <td>$order_total</td>                                                       
                                      <td> 0.00 </td>
                                      <td> $order_total</td>
                                   
                                     
                                </tr>";

        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="10">Page Total</th>
                                      <th>'.wc_price($grand_total).'</th>                                  
                                      <th>0.00</td>
                                      <th>'.wc_price($grand_total ).'</td>                                  
                                    </tr>
                                     <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="10">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_all_nop_orders_pending_filter_form($from_date, $to_date,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ALL PENDING ORDER REPORTS DATES</caption>
                <thead>
                <tr>
                    <th  style="width: 1%; text-align: center">Date From</th>
                    <th  style="width: 1%; text-align: center ">Date To</th>
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



    // All PAYMENT order reports

    public function smdn_woocommerce_paid_orders_partial_payment_reports(){



        $postFrom_date = '';
        $postTo_date= '';

        $linesPerPage = 10;
        $currentPageNumber = 1;

        if(isset($_POST['currentPageNumber']) && !empty($_POST['currentPageNumber'])){
            $currentPageNumber = $_POST['currentPageNumber'];
        }
        //$filterData= array( 'per_page'=>$linesPerPage, 'status'=>'completed', 'page'=>(int)$currentPageNumber);
        if(isset($_POST['searchButton']) && !empty($_POST['filterName'])){
            $searchName=$_POST['filterName'];

            //$filterData= array('search'=>$searchName,'status'=>'completed', 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);
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
        $filterData = array('after' => $getFromDate, 'status'=>'completed','before' => $getToDate, 'page' =>$currentPageNumber);



        self::smdn_paid_orders_partial_payment_filter_form($postFrom_date,$postTo_date,$currentPageNumber);


        // $orders= self::woocommerce_get_all_orders($from_date,$to_date);
//var_dump($orders);

        $membership_table="<div><h2> ALL PAID REPORTS</h2>
                                
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="10"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">Order ID</th>
                                        <th rowspan="2">Order Status</th>
                                        <th rowspan="2">Customer#</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Order Date</th>
                                        <th rowspan="2">Purchase Order</th>
                                        <th rowspan="2">Order Amount</th>
                                        <th rowspan="2">Total Payments</th>
                                        <th rowspan="2">Balance</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;
        $grand_balance=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $total_order_count = $wc_api->get_reports_total();
        $current_page = 0;
        $count_order_per_page = 0;
        $order_per_page = 0;


        $getOrder=$wc_api->get_orders($filterData);

        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;

        $current_page = (($currentPageNumber-1) * $linesPerPage) + 1;

        foreach($getOrder as $order ){
            $line_number++;

            $count_order_per_page++;
            //$current_page = (($currentPageNumber-1) * $count_order_per_page) + 1;
            $order_per_page = $current_page-1 + $count_order_per_page;
//var_dump($order);
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

            $order_date= date_i18n( get_option( 'date_format' ), strtotime($order->date_created) ); //. " *  " .$order->order_date;

            $tax_total=wc_format_decimal($order->total_tax, 2);
            $order_total=wc_format_decimal($order->total, 2);
            $total_payment = wc_format_decimal($order->discount_total,2);
            $total_wo_gst=wc_format_decimal($order_total-$tax_total, 2);
            $balance = $order_total - $total_payment;

            $grand_total += $order_total;
            $grand_total_tax += $tax_total;
            //$grand_balance+= $balance;



            /*
                   $users = Samadhan\course_helpers::get_user_course_data($user_id);
                   foreach ($users as $user) {
                       $total_cpd = $user['total_ceus'];
                   }*/




            $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td>$order_id</td>
                                      <td>$order_status</td>
                                      <td>$order_customer_id</td>
                                      <td>$ship_to_company</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_state</td>
                                      <td >$order_date</td>
                                      <td > </td>   
                                      <td>".wc_price($order_total)."</td>                                                       
                                      <td>" . wc_price($order_total) . "</td>
                                      <td>".wc_price(0.00)."</td>
                                   
                                     
                                </tr>";

        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="10">Page Total</th>
                                      <th>'.wc_price($grand_total ).'</th>                                  
                                      <th>'.wc_price($grand_total).'</td>
                                      <th>'.wc_price(0.00).'</td>                                  
                                    </tr>
                                     <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="10">Showing '.$current_page.' to  '.$order_per_page.' of total '.$total_order_count.' entries</th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_paid_orders_partial_payment_filter_form($from_date, $to_date,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ALL PAYMENT ORDER REPORTS DATES</caption>
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




    // All magor donation order reports
    public function smdn_woocommerce_major_donor_reports(){


        $postFrom_date = '';
        $postTo_date= '';

        $linesPerPage = 10;
        $currentPageNumber = 1;

        if(isset($_POST['currentPageNumber']) && !empty($_POST['currentPageNumber'])){
            $currentPageNumber = $_POST['currentPageNumber'];
        }
        $filterData= array( 'per_page'=>$linesPerPage, 'status'=>'completed', 'page'=>(int)$currentPageNumber);
        if(isset($_POST['searchButton']) && !empty($_POST['filterName'])){
            $searchName=$_POST['filterName'];

            $filterData= array('search'=>$searchName,'status'=>'completed', 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);
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

            $filterData = array('after' => $getFromDate, 'status'=>'completed','before' => $getToDate);
        }


        self::smdn_major_donor_filter_form($postFrom_date,$postTo_date);


        //$orders= self::woocommerce_get_all_donation_orders($from_date,$to_date);
//var_dump($orders);

        $membership_table="<div><h2> ALL MAJOR DONOR REPORTS</h2>
                                <form method='post' name='form' style='background: #0a4b78; float: right;width: 100%' >
                                    <div style='float: right'>
                                    <input type='hidden' id='currentPageNumber' name='currentPageNumber' value='".$currentPageNumber."'>
                                    <input type='text' name='filterName' value='".$searchName."'>
                                    <input type='submit' id='searchButton' name='searchButton' value='Search'>
                                    </div>
                                </form>
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="13"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">Address 1</th>
                                        <th rowspan="2">Address 2</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip</th>
                                        <th rowspan="2">Phone</th>
                                        <th rowspan="2">Mail Option</th>
                                        <th rowspan="2">Email</th>
                                        <th rowspan="2">Level</th>
                                        <th rowspan="2">Donations (Qty)</th>
                                        <th rowspan="2">Donations ($)</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);

        $allOrders = $wc_api->get_orders($filterData);


        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;


        foreach($allOrders as $order ) {


            $product_ids = $order->line_items;
            $cateID = false;
            foreach ($product_ids as $product_id) {
                $product_id = $product_id->product_id;
                $products = $wc_api->get_product($product_id);
                //var_dump($products);
                $productsName = $products->categories[0]->name;
                if ($productsName == "Donor") {
                    $cateID = true;
                }

            }


            if ($cateID) {
                $line_number++;
                $order_id = $order->id;
                $order_status = $order->status;
                $order_paid_status = ($order->payment_details->paid ? 'Paid' : 'Unpaid');
                $order_ship_status = $order->shipping_methods;
                $order_customer_id = $order->id;
                $firstname = $order->billing->first_name;
                $lastname = $order->billing->last_name;
                $order_phone = $order->billing->phone;
                $user_email = $order->billing->email;
                $ship_to_company = $order->billing->company;
                $order_billing_addresss1 = $order->billing->address_1;
                $order_billing_addresss2 = $order->billing->address_2;
                $ship_to_city = $order->billing->city;
                $ship_to_state = $order->billing->state;
                $ship_to_zip = $order->billing->postcode;
                $order_date = date_i18n(get_option('date_format'), strtotime($order->created_at));
                $paid_date = date_i18n(get_option('date_format'), strtotime($order->completed_at));
                $total_wo_gst = $order->total_tax;
                $order_total = $order->total;

                $item_quantity=0;
                $order_total=0;
                $tax_total=0;
                $productName='';
                foreach ($order->line_items as $quantity){

                    //var_dump($quantity->name);
                    $item_quantity +=$quantity->quantity;
                    $order_total +=$quantity->subtotal;
                    $tax_total +=$quantity->subtotal_tax;
                    $productName .=$quantity->name;
                }

                $membership_table .= "<tr>
                                      <td>$line_number</td>
                                      <td>$order_id</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_company</td>
                                      <td>$order_billing_addresss1</td>
                                      <td>$order_billing_addresss2</td>
                                      <td>$ship_to_city</td>
                                      <td >$ship_to_state</td>
                                      <td >$ship_to_zip</td>   
                                      <td>$order_phone</td>                                                       
                                      <td>??</td>
                                      <td style='word-break: break-all;'>$user_email</td>
                                      <td>$productName</td>
                                      <td>$item_quantity</td>
                                      <td>$" . wc_format_decimal($order_total) . "</td>
                                   
                                     
                                </tr>";

            }
        }

        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="13"></th>
                                      <th></th>                                  
                                      <th></td>
                                      <th></td>                                  
                                    </tr>
                                       <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="13"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_major_donor_filter_form($from_date, $to_date){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ALL MAJOR DONOR REPORTS DATES</caption>
                <thead>
                <tr>
                    <th  style="width: 1%; text-align: center">Date From</th>
                    <th  style="width: 1%; text-align: center ">Date To</th>
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
                    <td style="vertical-align: top; text-align: center">
                        <input type="submit" class="btn btn-success btn-send" name="samadhan-pod-group-leader-search" value="Search">
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




    // All maintain authorize exception donation order reports
    public function smdn_woocommerce_maintain_authorize_exception_reports(){

        $postFrom_date = '';
        $postTo_date= '';

        $linesPerPage = 10;
        $currentPageNumber = 1;

        if(isset($_POST['currentPageNumber']) && !empty($_POST['currentPageNumber'])){
            $currentPageNumber = $_POST['currentPageNumber'];
        }
        $filterData= array( 'per_page'=>$linesPerPage, 'status'=>'completed', 'page'=>(int)$currentPageNumber);
        if(isset($_POST['searchButton']) && !empty($_POST['filterName'])){
            $searchName=$_POST['filterName'];

            $filterData= array('search'=>$searchName,'status'=>'completed', 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);
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

            $filterData = array('after' => $getFromDate, 'status'=>'completed','before' => $getToDate);
        }

        self::smdn_maintain_authorize_exception_form($postFrom_date,$postTo_date);


        //$orders= self::woocommerce_get_all_donation_orders($from_date,$to_date);
//var_dump($orders);


        $membership_table="<div><h2> AUTHORIZE.NET EXCEPTIONS</h2>
                                <form method='post' name='form' style='background: #0a4b78; float: right;width: 100%' >
                                    <div style='float: right'>
                                    <input type='hidden' id='currentPageNumber' name='currentPageNumber' value='".$currentPageNumber."'>
                                    <input type='text' name='filterName' value='".$searchName."'>
                                    <input type='submit' id='searchButton' name='searchButton' value='Search'>
                                    </div>
                                </form>
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="11"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">Settle Date</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">Address</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip</th>
                                        <th rowspan="2">Phone</th>
                                        <th rowspan="2">Email</th>
                                        <th rowspan="2">Amount</th>
                                         <th rowspan="2">Link to Major Donor</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;


        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $allOrders = $wc_api->get_orders($filterData);


        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;


        foreach($allOrders as $order ) {


            $product_ids = $order->line_items;
            $cateID = false;
            foreach ($product_ids as $product_id) {
                $product_id = $product_id->product_id;
                $products = $wc_api->get_product($product_id);
                //var_dump($products);
                $productsName = $products->categories[0]->name;
                if ($productsName == "Donor") {
                    $cateID = true;
                }

            }


            if ($cateID) {
                $line_number++;
                $order_id = $order->id;
                $order_status = $order->status;
                $order_paid_status = ($order->payment_details->paid ? 'Paid' : 'Unpaid');
                $order_ship_status = $order->shipping_methods;
                $order_customer_id = $order->id;
                $firstname = $order->billing->first_name;
                $lastname = $order->billing->last_name;
                $order_phone = $order->billing->phone;
                $user_email = $order->billing->email;
                $ship_to_company = $order->billing->company;
                $order_billing_addresss1 = $order->billing->address_1;
                $order_billing_addresss2 = $order->billing->address_2;
                $ship_to_city = $order->billing->city;
                $ship_to_state = $order->billing->state;
                $ship_to_zip = $order->billing->postcode;
                $order_date = date_i18n(get_option('date_format'), strtotime($order->created_at));
                $paid_date = date_i18n(get_option('date_format'), strtotime($order->completed_at));
                $total_wo_gst = $order->total_tax;
                $order_total = $order->total;

                $item_quantity=0;
                $order_total=0;
                $tax_total=0;
                $productName='';
                foreach ($order->line_items as $quantity){

                    //var_dump($quantity->name);
                    $item_quantity +=$quantity->quantity;
                    $order_total +=$quantity->subtotal;
                    $tax_total +=$quantity->subtotal_tax;
                    $productName .=$quantity->name;
                }


            $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td><a href='#'>$order_id</a></td>
                                      <td>$order_date</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_company</td>
                                      <td>$order_billing_addresss1</td>
                                      <td>$ship_to_city</td>
                                      <td >$ship_to_state</td>
                                      <td >$ship_to_zip</td>   
                                      <td>$order_phone</td> 
                                      <td style='word-break: break-all;'>$user_email</td>
                                      <td>".wc_format_decimal($order_total)."</td>
                                      <td><a href='#'>@</a> </td>
                                   
                                     
                                </tr>";

        }
        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="11"></th>
                                      <th></th>                                  
                                      <th></td>
                                      <th></td>                                  
                                    </tr>
                                       <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="11"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_maintain_authorize_exception_form($from_date, $to_date){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ALL AUTHORIZE.NET EXCEPTIONS REPORTS DATES</caption>
                <thead>
                <tr>
                    <th  style="width: 1%; text-align: center">Date From</th>
                    <th  style="width: 1%; text-align: center ">Date To</th>
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
                    <td style="vertical-align: top; text-align: center">
                        <input type="submit" class="btn btn-success btn-send" name="samadhan-pod-group-leader-search" value="Search">
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





    // All maintain authorize exception without donation order reports
    public function smdn_woocommerce_maintain_authorize_exception_reports_without_donor(){

        $postFrom_date = '';
        $postTo_date= '';

        $linesPerPage = 10;
        $currentPageNumber = 1;

        if(isset($_POST['currentPageNumber']) && !empty($_POST['currentPageNumber'])){
            $currentPageNumber = $_POST['currentPageNumber'];
        }
        $filterData= array( 'per_page'=>$linesPerPage, 'status'=>'completed', 'page'=>(int)$currentPageNumber);
        if(isset($_POST['searchButton']) && !empty($_POST['filterName'])){
            $searchName=$_POST['filterName'];

            $filterData= array('search'=>$searchName,'status'=>'completed', 'per_page'=>$linesPerPage, 'page'=>(int)$currentPageNumber);
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

            $filterData = array('after' => $getFromDate, 'status'=>'completed','before' => $getToDate);
        }

        self::smdn_maintain_authorize_exception_form($postFrom_date,$postTo_date);


        //$orders= self::woocommerce_get_all_donation_orders($from_date,$to_date);
//var_dump($orders);


        $membership_table="<div><h2> AUTHORIZE.NET EXCEPTIONS WITHOUT DONOR</h2>
                                <form method='post' name='form' style='background: #0a4b78; float: right;width: 100%' >
                                    <div style='float: right'>
                                    <input type='hidden' id='currentPageNumber' name='currentPageNumber' value='".$currentPageNumber."'>
                                    <input type='text' name='filterName' value='".$searchName."'>
                                    <input type='submit' id='searchButton' name='searchButton' value='Search'>
                                    </div>
                                </form>
                            </div>
                            <br/> ";
        $membership_table .= '<table class="table">';
        $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                   <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="11"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    <tr>
                                        <th rowspan="2">SL#</th>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2">Settle Date</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">Address</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip</th>
                                        <th rowspan="2">Phone</th>
                                        <th rowspan="2">Email</th>
                                        <th rowspan="2">Amount</th>
                                         <th rowspan="2">Link to Major Donor</th>
                                      </tr>
                                     
                                  </thead>';
        $membership_table.='<tbody>';


        $sl_no = 0;
        $grand_total=0;
        $grand_total_tax=0;

        $wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $allOrders = $wc_api->get_orders($filterData);


        $line_number =  ((int)$currentPageNumber -1 ) * $linesPerPage;


        foreach($allOrders as $order ) {


            $product_ids = $order->line_items;
            $cateID = false;
            foreach ($product_ids as $product_id) {
                $product_id = $product_id->product_id;
                $products = $wc_api->get_product($product_id);
                $productsName = $products->categories[0]->name;
                if ($productsName != "Donor") {
                    $cateID = true;
                }

            }


            if ($cateID) {
                $line_number++;
                $order_id = $order->id;
                $order_status = $order->status;
                $order_paid_status = ($order->payment_details->paid ? 'Paid' : 'Unpaid');
                $order_ship_status = $order->shipping_methods;
                $order_customer_id = $order->id;
                $firstname = $order->billing->first_name;
                $lastname = $order->billing->last_name;
                $order_phone = $order->billing->phone;
                $user_email = $order->billing->email;
                $ship_to_company = $order->billing->company;
                $order_billing_addresss1 = $order->billing->address_1;
                $order_billing_addresss2 = $order->billing->address_2;
                $ship_to_city = $order->billing->city;
                $ship_to_state = $order->billing->state;
                $ship_to_zip = $order->billing->postcode;
                $order_date = date_i18n(get_option('date_format'), strtotime($order->created_at));
                $paid_date = date_i18n(get_option('date_format'), strtotime($order->completed_at));
                $total_wo_gst = $order->total_tax;
                $order_total = $order->total;

                $item_quantity=0;
                $order_total=0;
                $tax_total=0;
                $productName='';
                foreach ($order->line_items as $quantity){

                    //var_dump($quantity->name);
                    $item_quantity +=$quantity->quantity;
                    $order_total +=$quantity->subtotal;
                    $tax_total +=$quantity->subtotal_tax;
                    $productName .=$quantity->name;
                }


                $membership_table.="<tr>
                                      <td>$line_number</td>
                                      <td><a href='#'>$order_id</a></td>
                                      <td>$order_date</td>
                                      <td>$firstname</td>
                                      <td>$lastname</td>
                                      <td>$ship_to_company</td>
                                      <td>$order_billing_addresss1</td>
                                      <td>$ship_to_city</td>
                                      <td >$ship_to_state</td>
                                      <td >$ship_to_zip</td>   
                                      <td>$order_phone</td> 
                                      <td style='word-break: break-all;'>$user_email</td>
                                      <td>".wc_format_decimal($order_total)."</td>
                                      <td><a href='#'>@</a> </td>
                                   
                                     
                                </tr>";

            }
        }


        $membership_table.=' </tbody> ';

        $membership_table.='<tfoot><tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="11"></th>
                                      <th></th>                                  
                                      <th></td>
                                      <th></td>                                  
                                    </tr>
                                       <tr  style="color: white; background-color: rgb(69, 88, 97);">
                                      <th scope="row" colspan="11"></th>
                                      <th></th>                                  
                                      <th><a href="JavaScript:PrevPage();">Prev</a> </td>
                                      <th><a href="JavaScript:NextPage();">Next</a> </td>                                   
                                    </tr>
                                    </tfoot>';

        $membership_table.='</table>';


        return $membership_table;

    }

    public  static  function smdn_maintain_authorize_exception__without_donor_form($from_date, $to_date){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ALL AUTHORIZE.NET EXCEPTIONS WITHOUT DONOR REPORTS DATES</caption>
                <thead>
                <tr>
                    <th  style="width: 1%; text-align: center">Date From</th>
                    <th  style="width: 1%; text-align: center ">Date To</th>
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
                    <td style="vertical-align: top; text-align: center">
                        <input type="submit" class="btn btn-success btn-send" name="samadhan-pod-group-leader-search" value="Search">
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



    //order functions
    public static function woocommerce_get_all_orders($from_date,$to_date){

        global $wpdb;

         $blogID=$wpdb->get_blog_prefix() ;
        $table=$wpdb->get_blog_prefix().'posts';
        //$table='wp_2_posts';
         $blogid=$wpdb->blogid;
         $base_prefix=$wpdb->base_prefix;
        //var_dump($blogID);
        // var_dump($wpdb);
        //var_dump($wpdb->get_blog_prefix());
        //var_dump($wpdb->get_blog_prefix());
        $results=$wpdb->get_results( $wpdb->prepare("SELECT * FROM  $table where post_type = 'shop_order' and post_status not in ('wc-cancelled','wc-on-hold', 'wc-refunded') and post_date between '%s' and '%s' order by post_date ASC",$from_date,$to_date));
        return $results;

    }

    public static function get_order_item($order_id){
        global $wpdb;
        $table=$wpdb->get_blog_prefix().'woocommerce_order_items';
        //$table='wp_2_woocommerce_order_items';
        $results= $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table where order_item_type='line_item' and order_id=%d", $order_id));
        return $results;
    }

    public static function woocommerce_get_all_donation_orders($from_date,$to_date){

        global $wpdb;

        $wpdb->get_blog_prefix() ;
        $table=$wpdb->get_blog_prefix().'posts';
        $woo_table=$wpdb->get_blog_prefix().'woocommerce_order_items';
        //$table='wp_2_posts';
        // var_dump($wpdb);
        //  SELECT p.Id as order_id FROM multisite.wp_2_posts as p  inner join multisite.wp_2_woocommerce_order_items as w on p.ID=w.order_id where post_type = 'shop_order' and post_status not in ('wc-cancelled','wc-on-hold', 'wc-refunded')  and order_item_type='line_item' and order_item_name='Donation'
        $results=$wpdb->get_results( $wpdb->prepare("SELECT * FROM  $table as p inner join  $woo_table as w on p.ID=w.order_id where post_type = 'shop_order' and p.post_status not in ('wc-cancelled','wc-on-hold', 'wc-refunded')  and w.order_item_type='line_item' and w.order_item_name='Donation' and p.post_date between '%s' and '%s' order by p.post_date ASC",$from_date,$to_date));
        return $results;
    }




    public static function woocommerce_get_all_orders_by_product_name($product_id,$from_date,$to_date){

        global $wpdb;

        $wpdb->get_blog_prefix() ;
        $table=$wpdb->get_blog_prefix().'posts';
        $table2=$wpdb->get_blog_prefix().'wc_order_product_lookup';
        $table3=$wpdb->get_blog_prefix().'woocommerce_order_items';
//$table='wp_2_posts';
//var_dump($wpdb);
//var_dump($wpdb->get_blog_prefix());
        if (is_numeric($product_id)){
            $results=$wpdb->get_results ("SELECT * FROM $table inner join $table2 on $table.ID = $table2.order_id inner join $table3 on $table2.order_id = $table3.order_id where post_type = 'shop_order' and post_status not in ('wc-cancelled','wc-on-hold', 'wc-refunded') and post_date between '$from_date' and '$to_date' and product_id=$product_id order by post_date ASC");
        }
        else{
            $results=$wpdb->get_results ("SELECT * FROM $table inner join $table2 on $table.ID = $table2.order_id inner join $table3 on $table2.order_id = $table3.order_id where post_type = 'shop_order' and post_status not in ('wc-cancelled','wc-on-hold', 'wc-refunded') and post_date between '$from_date' and '$to_date' and order_item_name like '%$product_id%' order by post_date ASC");
        }
//$results=$wpdb->get_results ("SELECT * FROM $table inner join $table2 on $table.ID = $table2.order_id inner join $table3 on $table2.order_id = $table3.order_id where post_type = 'shop_order' and post_status not in ('wc-cancelled','wc-on-hold', 'wc-refunded') and post_date between '$from_date' and '$to_date' and (product_id=$product_id or order_item_name like '%$product_id%') order by post_date ASC");

//$results=$wpdb->get_results ("SELECT * FROM $table where post_type = 'shop_order' and post_status not in ('wc-cancelled','wc-on-hold', 'wc-refunded') and post_date between '$from_date' and '$to_date' and ID=$product_id order by post_date ASC");
        return $results;

    }


}


new SmdnAccountsWoocommerceReport();
