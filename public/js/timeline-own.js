var date = 'now';
var project_id = -1;
var specific = false;
var person_id = -1;
$('document').ready(function(){
    /*
     * automatic call to server to fill it with the data
     */
    if($('#mainTimePage').length > 0){
        callToServerForDataTimeshitty();
    }
    /*
     * add listener to checkbox to enable/disable input box for project
     */
    if($('#specificTimePage').length > 0){
        if($('#specific_Project').is(':checked')){
            $('#projectPicker input').prop('disabled', false);
        }
        $('#specific_Project').on('change', function (){
            $('#specific_Project').is(':checked') ? $('#projectPicker input').prop('disabled', false) : $('#projectPicker input').prop('disabled', true);
        });
    }
    /*
     * set timepicker to savetime form
     */
    if($('#savetime_timepicker1').length > 0){
        $('#savetime_timepicker1').timepicker({showMeridian: false});
        $('#savetime_timepicker2').timepicker({showMeridian: false});
    }
    /*
     * set timepicker to savetime modal
     */
    if($('#time_timepicker1').length > 0){
        $('#time_timepicker1').timepicker({showMeridian: false});
        $('#time_timepicker2').timepicker({showMeridian: false});
    }
});
/*
 * call to server to get information
 */
function callToServerForDataTimeshitty(){
    $('#weektimesheet').sgTimeline();
    $('#monthtimsheet').sgTimeline();
    $('#yeartimesheet').sgTimeline();
    $.ajax({
        url: base_url + 'api' + '/'+ 'getEverytimeLinePossible' + '/',
        type: "POST",
        data: {
            date : date,
            project_id : project_id,
            specific: specific,
            person_id: person_id
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                createTheTimeShittys(response);
            }else{
                $('html').scrollTop(0);
                ErrorHandler(data['errormessage']);
            }
        }
    });
}

/*
 * create the html for the tables
 * or create the lower than 500px view
 */
function createTheTimeShittys(e){
    $('#weektimesheet').data('sgTimeline').parseData(e.week);
    $('#monthtimsheet').data('sgTimeline').parseData(e.month);
    $('#yeartimesheet').data('sgTimeline').parseData(e.year);
}

/**
 * Adds time stat to database.
 * @return false so that form does not submit.
 */
function saveTime(time_id){
    $("#addtimebutton").html('<i class="fa fa-refresh fa-spin"></i> Adding...');
    //getCustomerList
    var project_id = $('#selectPickerTimeSheet option').filter(':selected').val();
    //savetime_timedate, savetime_timepicker1, savetime_timepicker2, so
    var so = 0;
    if($("#so").is(":checked")){
        so = 1;
    }
    var date = $("#savetime_timedate").val();
    var starttime = $("#savetime_timepicker1").val();
    var endtime = $("#savetime_timepicker2").val();
    var actionurl = base_url + "api/saveTime/";

    $.get( actionurl,{project_id:project_id, timedate:date, starttime:starttime, so:so, endtime:endtime, time_id:time_id}, function( data ) {
        $("#addtimebutton").html('<i class="fa fa-plus"></i> Add');
        if (data!=""){
            if (data['success'] == 'success'){
                showSucces(data['success']);
                callToServerForDataTimeshitty();
            }
            if (data['errormessage']){
                $('html').scrollTop(0);
                ErrorHandler(data['errormessage']);
            }
        }
    });
    return false;
}
/**
 * Change an existing time.
 * @param {object} ele The dom element which was clicked.
 * @returns {undefined}
 */
function changeTime(ele){
    var id = $(ele).data("id");
    $.ajax({
        url: base_url + 'api' + '/'+ 'getTimeById' + '/',
        type: "POST",
        data: {
            id : id,
            person_id:person_id
        },
        complete: function(response){
            var e = JSON.parse(response.responseText);
            if(e.success == "success"){
                fillInTheTimeSlot(e.requested);
            }
        }
    });
}

/**
 * Go to the overview of specific project
 * @param {object} ele The dom element which was clicked.
 * @return {undefined}
 */
function goToOverviewProject(ele){
    var id = $(ele).data("id");
    window.open(base_url + "projects/overview/" + id + "/");
}
/*
 * callback function
 *  fill it in into the modal
 */
function fillInTheTimeSlot(e){
    $('.modal-errormessage').text('');
    $('#time_id').val(e.id);
    $('#time_project_id').val(e.project_id);
    $('#time_date').val(e.date);
    $('#time_timepicker1').timepicker('setTime', e.start);
    $('#time_timepicker2').timepicker('setTime',e.end);
    $('#so2').prop('checked',e.so > 0);
    $('#editTimeDialog').modal('show');
}
/*
 * send the data you changed to server
 */
function saveChangeTime(){
    var project_id = $("#time_project_id").val();
        //savetime_timedate, savetime_timepicker1, savetime_timepicker2
    var date = $("#time_date").val();
    var starttime = $("#time_timepicker1").val();
    var so = 0;
    if($("#so2").is(":checked")){
        so = 1;
    }
    var endtime = $("#time_timepicker2").val();
    var time_id = $("#time_id").val();
    var actionurl = base_url + "api/saveTime/";
    $.get( actionurl,{project_id:project_id, timedate:date, starttime:starttime, so:so, endtime:endtime, time_id:time_id, person_id:person_id}, function( data ) {
        if (data!=""){
            if (data['success'] == 'success'){
                $('#editTimeDialog').modal('hide');
                showSucces(data['success']);
                callToServerForDataTimeshitty();
            }
            if (data['errormessage']){
                $('.modal-errormessage').text(data['errormessage']);
            }
        }
    });
    return false;
}
/*
 * set the data for specific searches.
 */
function getSpecificStatsUser(){
    specific = $('#project_id').val();
    if(specific > 0){
        specific = true;
        project_id = $('#project_id').val();
    }
    date = $('#savetime_timedate').val();
    if($('#person_id').length > 0){
        person_id = $('#person_id').val();
    }
    callToServerForDataTimeshitty();
    $('.invisible').removeClass('invisible');
    return false;
}