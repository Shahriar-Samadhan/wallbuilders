<?php

namespace Samadhan {
    class DonationFinanceReport
    {
        function __construct()
        {
            add_shortcode('pod_donations_reports',array($this,'smdn_woocommerce_donations_reports'));
        }


        protected static function pod_get_all_donations($from_date,$to_date, $currentPage, $searchText){

            $columnsToSearchArray = array("FirstName", "LastName");
            $OrderBy = array(
                "OriginalCreateDate" => "DESC",
                "Id" => "ASC"
            );
            $dataQuery = new QueryBuilder("MajorDonorHistory", 10,$currentPage,$OrderBy);
            if(isset($searchText) && strlen($searchText)> 3 ) {
                $searchData = $searchText;
                $dataQuery->BuildSearchQuery($searchData, $columnsToSearchArray);
            }

            $query = " OriginalCreateDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' ";
            $dataQuery->buildQueryParts($query);

            return  $dataQuery->GetQueryResult();

        }

        // All maintain authorize exception donation order reports
        public function smdn_woocommerce_donations_reports(){


            $from_date = '';
            $to_date= '';
            $search_text = '';
            $current_page = 1;

            if( isset($_POST["from_date"])) $from_date = $_POST["from_date"];
            if( isset($_POST["to_date"])) $to_date = $_POST["to_date"];
            if( isset($_POST["samadhan_report_search_text"])) $search_text = $_POST["samadhan_report_search_text"];
            if( isset($_POST["samadhan_report_current_page"])) $current_page = $_POST["samadhan_report_current_page"];


            self::smdn_donations_form($from_date,$to_date,$current_page,$search_text);


            //$orders= self::woocommerce_get_all_donation_orders($from_date,$to_date);
            $queryResult= self::pod_get_all_donations($from_date,$to_date,$current_page, $search_text);
            $orders = $queryResult->RecordSet;

            $paginationBuilder = new PaginationBuilder(10,$current_page,$queryResult->TotalRecords,17);

//var_dump($orders);

            $membership_table= '<table class="table">';
            $membership_table.='<thead style="color: white; background-color: rgb(69, 88, 97);">
                                    <tr>
                                        <th rowspan="2">Order/Auth ID</th>
                                        <th rowspan="2">Source</th>
                                        <th rowspan="2">Paid Date</th>
                                        <th rowspan="2">Customer#</th>
                                        <th rowspan="2">First Name</th>
                                        <th rowspan="2">Last Name</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">Address Line 1</th>
                                        <th rowspan="2">Address Line 2</th>
                                        <th rowspan="2">City</th>
                                        <th rowspan="2">State</th>
                                        <th rowspan="2">Zip</th>
                                        <th rowspan="2">Phone Number</th>
                                        <th rowspan="2">Purchase Order</th>
                                        <th rowspan="2">Response Code 1</th>
                                        <th rowspan="2">Response Code 2</th>
                                        <th rowspan="2">Donation Amount</th>
                                      </tr>
                                     
                                  </thead>';
            $membership_table.='<tbody>';


            $sl_no = 0;
            $grand_total=0;
            $grand_total_tax=0;

            foreach($orders as $order ){
                // var_dump($order);
                $sl_no++;

                $settleDate=date('m-j-Y',strtotime($order->OriginalCreateDate));

                $response_code_1 = "";
                $response_code_2 = "";

                $membership_table.="<tr>
                                      <td>$order->Id</td>
                                      <td>$order->Source</td>
                                      <td>$settleDate</td>
                                      <td>$order->OriginalClientId</td>
                                      <td>$order->FirstName</td>
                                      <td>$order->LastName</td>
                                      <td>$order->Company</td>
                                      <td>$order->Address1</td>
                                      <td>$order->Address2</td>
                                      <td>$order->City</td>
                                      <td>$order->State</td>
                                      <td>$order->Zip</td>   
                                      <td>$order->Phone</td> 
                                      <td>$order->OrderNumber</td>
                                      <td>$response_code_1</td>
                                      <td>$response_code_2</td>
                                      <td>".wc_format_decimal($order->TotalAmount)."</td>
                                      
                                   
                                     
                                </tr>";

            }

            $membership_table.=' </tbody> ';
            $membership_table.= $paginationBuilder->GetPaginationRow();
            $membership_table.='</table>';

            return $membership_table;

        }


        public  static  function smdn_donations_form($from_date, $to_date, $current_page=1, $search_text=""){
            ?>
            <form id="pod-point-entry" method="post" role="form">
                <?php  wp_nonce_field( 'leader_pod_report' ); ?>
                <input type="hidden" name="samadhan_report_current_page" id="samadhan_report_current_page" value="<?php echo $current_page; ?>">

                <input type="hidden" name="samadhan_report_type" value="ACCOUNTS_WOOCOMMERCE_SALES">


                <table id="searchResults" class="display" cellspacing="0" width="100%" xmlns="http://www.w3.org/1999/html">
                    <caption>DONATION FINANCE REPORT</caption>
                    <thead>
                    <tr>
                        <th  style="width: 1%; text-align: center">Date From</th>
                        <th  style="width: 1%; text-align: center ">Date To</th>
                        <th  style="width: 1%; text-align: center" colspan="2">Search</th>
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
                        <td style="vertical-align: top;; text-align: center">
                            <input type="text" name="samadhan_report_search_text" id="samadhan_report_search_text" value="<?php echo $search_text; ?>">
                        </td>
                        <td style="vertical-align: top; text-align: center">
                            <input type="submit" class="btn btn-success btn-send" id="btn-report-submit"  value="Search">
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

    new DonationFinanceReport();
}




