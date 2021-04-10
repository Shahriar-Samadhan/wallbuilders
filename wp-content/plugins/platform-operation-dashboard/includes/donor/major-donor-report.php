<?php

namespace Samadhan;

use DateTime;

class MajorDonorReport extends GetOrder {

    public function __construct()
    {
        add_shortcode('pod_major_donor_reports','Samadhan\MajorDonorReport::smdn_woocommerce_major_donor_reports');
    }





    public static function smdn_woocommerce_major_donor_reports(){


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

            //$filterData = array('after' => $getFromDate, 'status'=>'completed','before' => $getToDate);
        }

        $filterData = array('after' => $getFromDate, 'status'=>'completed','before' => $getToDate, 'page'=>$currentPageNumber );


        self::smdn_major_donor_filter_form($postFrom_date,$postTo_date,$currentPageNumber);


        //$orders= self::woocommerce_get_all_donation_orders($from_date,$to_date);
//var_dump($orders);

        $membership_table="<div><h2> ALL MAJOR DONOR REPORTS</h2>
                                <form method='post' name='form' style='background: #0a4b78; float: right;width: 100%' >
                                    <div style='float: right'>
                                    <input type='hidden' id='currentPageNumber' name='currentPageNumber' value='".$currentPageNumber."'>
                                    <input type='text' placeholder='Enter email' name='filterName' value='".$searchName."'>
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

        //$wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $wc_api = parent::smdn_order_API_call();

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
                                      <td> </td>
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

    public  static  function smdn_major_donor_filter_form($from_date, $to_date,$currentPageNumber){
        ?>
        <form id="pod-point-entry" method="post" role="form">
            <?php  wp_nonce_field( 'leader_pod_report' ); ?>
            <input type='hidden' id='currentPageNumber' name='currentPageNumber' value="<?php echo $currentPageNumber; ?>">
            <input type="hidden" name="samadhan_woocommerce_report" value="SEARCH">
            <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


            <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                <caption>ENTER ALL MAJOR DONOR REPORTS DATES</caption>
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

new MajorDonorReport();