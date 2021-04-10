function FirstPageClicked(){
    document.getElementById("samadhan_report_current_page").value = 1 ;
    SubmitPagination();
}
function PrevButtonClicked(){
    var pageToGo =  Number(document.getElementById("samadhan_report_current_page").value) -1 ;
    if(pageToGo < 1) pageToGo = 1;
    document.getElementById("samadhan_report_current_page").value = pageToGo ;
    SubmitPagination();
}
function NextButtonClicked(){
    document.getElementById("samadhan_report_current_page").value = Number(document.getElementById("samadhan_report_current_page").value) +1;
    SubmitPagination();
}
function SubmitPagination(){

     document.getElementById("btn-report-submit").click();
}