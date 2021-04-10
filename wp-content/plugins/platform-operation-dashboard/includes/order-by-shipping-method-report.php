<?php

namespace Samadhan;

use DateTime;

class OrderbyShippingMethod extends GetOrder {

    public function __construct()
    {
        add_shortcode('pod_order_by_shipping_method_reports','Samadhan\OrderbyShippingMethod::smdn_woocommerce_orderby_shipping_method_reports');
    }



    public static function smdn_woocommerce_orderby_shipping_method_reports(){


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

        //$wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $wc_api = parent::smdn_order_API_call();
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


}

new OrderbyShippingMethod();
