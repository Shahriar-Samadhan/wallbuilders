<?php

namespace Samadhan;

use DateTime;

class Orderbyproduct extends GetOrder {

    public function __construct()
    {
        add_shortcode('pod_orders_by_product_reports','Samadhan\Orderbyproduct::smdn_woocommerce_orders_by_product_reports');
    }


    public static function smdn_woocommerce_orders_by_product_reports(){


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

        //$wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $wc_api = parent::smdn_order_API_call();
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

        //$wc_api = new WC_API_Client( $this->consumer_key,$this->consumer_secret,$this->store_url ,true);
        $wc_api = parent::smdn_order_API_call();

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


}

new Orderbyproduct();
