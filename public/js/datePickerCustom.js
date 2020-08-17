$(document).ready(function () {
//Triggers the datepicker to show the calendar when clicking the input as well as the arrow
    $('#deadline, #savetime_timedate').datepicker({
        format: 'dd-mm-yyyy',
        setDate: 'today',
        todayHighlight: true,
        toggleActive: true,
        autoclose: true,
        orientation: "Bottom auto"
    });
    
    var dateInputFieldValue = $('#deadline').val();
    var today = new Date();
    var dd = String(today.getDate());
    var mm = String(today.getMonth() + 1);
    var yyyy = today.getFullYear();
    today = dd + '-' + mm + '-' + yyyy;
    if(dateInputFieldValue === ""){
        $('#deadline').val(today);
    }

});