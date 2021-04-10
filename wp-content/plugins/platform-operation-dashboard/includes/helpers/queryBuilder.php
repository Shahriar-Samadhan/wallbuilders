<?php


namespace Samadhan {


    class QueryBuilder
    {
/*
        protected int $pageSize;
        protected int $offset;
        protected int $currentPage;
        protected string $searchData;
        protected string $tableName;
        protected string $sqlQuery;
        protected string $sqlQueryWithPagination;
        protected string $orderBy;
        protected $orderByArray;
*/
        protected  $pageSize;
        protected  $offset;
        protected  $currentPage;
        protected  $searchData;
        protected  $tableName;
        protected  $sqlQuery;
        protected  $sqlQueryWithPagination;
        protected  $orderBy;
        protected  $orderByArray;

        function __construct($tableName, $pageSize, $currentPage, $orderByArray)
        {
            $this->tableName = $tableName;
            $this->pageSize = $pageSize;
            $this->currentPage = $currentPage;
            $this->offset = $this->currentPage - 1;
            $this->orderByArray = $orderByArray;
            $this->sqlQuery = "";
        }


        public function buildQueryParts($newPart, $condition = 'AND')
        {
            if (empty($this->sqlQuery)) $this->sqlQuery = " WHERE ";
            else $this->sqlQuery .= " " . $condition . " ";
            $this->sqlQuery .= " " . $newPart;
        }

        public function BuildSearchQuery($searchData, $columnsToSearchArray)
        {
            if (isset($searchData) && !empty($searchData)) {
                $searchQuery = "(";
                foreach ($columnsToSearchArray as  $value) {
                    $searchQuery .= $value . " LIKE '%$searchData%' "  ;
                    if (next($columnsToSearchArray)==true) $searchQuery  .= " OR ";
                }
                $searchQuery .= ")";
                $this->buildQueryParts($searchQuery);
            }
        }

        protected function addOrderBy()
        {
            $this->orderBy = " ORDER BY ";
            foreach ($this->orderByArray as $key => $value) {
                $this->orderBy .= $key . " " . $value  ;
                if (next($orderByArray)==true) $this->orderBy  .= ", ";
            }

        }

        protected function addPagination()
        {
            $this->sqlQueryWithPagination = $this->sqlQuery;
            if (isset($this->pageSize) && $this->pageSize > 0) {
                $this->sqlQueryWithPagination .= " LIMIT " . $this->pageSize;
            }

            if (isset($this->offset) && $this->offset > 0 & isset($this->pageSize) && $this->pageSize > 0) {
                $this->sqlQueryWithPagination .= " OFFSET " . $this->offset * $this->pageSize;
            } else {
                $this->sqlQueryWithPagination .= " OFFSET 0";
            }
        }


        public function GetTotal(): int
        {
            global $wpdb;
            /**************** total record query ********************/
            $sqlQuery = "SELECT COUNT(id) AS total_records FROM {$wpdb->prefix}$this->tableName  " . $this->sqlQuery;
            //var_dump($sqlQuery);

            $totalRecords = $wpdb->get_var($wpdb->prepare($sqlQuery));
            if(is_numeric($totalRecords)) return  $totalRecords;
            return 0;

        }

        public function ExecuteQuery(){
            global $wpdb;
            $this->addPagination();
            $this->addOrderBy();
            $sqlQuery = "SELECT * FROM {$wpdb->prefix}$this->tableName  " . $this->sqlQueryWithPagination;
            //var_dump($sqlQuery);
            return $wpdb->get_results($wpdb->prepare($sqlQuery));
        }

        public function GetQueryResult(){
            $obj = new \stdClass();
            $obj->TotalRecords = $this->GetTotal();
            $obj->RecordSet = $this->ExecuteQuery();
            return $obj;
        }
    }
}
