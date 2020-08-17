//var savevariable = -1;

//used for clearing the error/successmessage
var timeout = null;
//used for setting the current page and do stuff on the hand of that
var page = "";
//used on the main page, to keep all the info gotten from the server
var todos = {};
//used for the timeout of search on keyup
var timeoutTimer;

/**
 * Global vars for the clock
 */
/**
 * Holds the seconds for the clock
 * @type {Number}
 */
var seconds = 0;
/**
 * Hold the clock interval.
 * @type {object}
 */
var clockinterval = null;
/**
 * setting a timeout for refreshing.
 * @type {object}
 */
var timeoutResizer = null;

$(document).ready(function () {
    /*This function activates the readmore and close function in project description*/
    $('article').readmore({
        afterToggle: function(trigger, element, expanded) {
            if(!expanded) {
              $('html, body').animate( { scrollTop: element.offset().top }, {duration: 100 } );
            }
        }
    });
    
    
    getTime();
    //Fires when remote data is loaded successfully.
    //Adds spans on number in the bootstrap table pagination, for styling purposes.
    $('.table').on("load-success.bs.table", function(){
        var x = $(".pagination-info").eq(0).text();
        var newDigits = Array();
        var digits = x.match(/\d+/g).map(Number);
        for(var i = 0; i < digits.length; i++){
            newDigits[i] = "<span>" + digits[i] + "</span>";
        }
        var html = "showing " + newDigits[0] + " of " + newDigits[1] + " from " + newDigits[2] + "";
        $('.pagination-info').html(html);
    });
    /**
     * Set the localstorage logButton to default value, if it does not exist.
     */
    if(typeof localStorage.getItem("logButton") === "undefined" || localStorage.getItem("logButton") === null){
        localStorage.setItem("logButton", "stop");
    }
    
    var logButton;
    /**
     * Add the click listeners to the play, pause and stop buttons
     */
    $('#play, #pause, #stop').click(function () {
        var bool = false;
        if (this.id === 'play') {
            logButton = "play";
            getProjectId();
            localStorage.setItem("logButton", "play");
            bool = true;
        } else if(this.id === 'pause'){
            logButton = "pause";
            localStorage.setItem("logButton", "pause");
        } else {
           logButton = "stop";
           localStorage.setItem("projectID", -1);
           localStorage.setItem("logButton", "stop");
        }
        getStats(bool);
    });
    /**
     * Add the collapse functionality to the menu.
     */
    if($("#sideMenuMain").length > 0){
        $("#sideMenuMain").metisMenu();
    }
    /**
     * Load the default functions for the standard pages,
     */
    if(typeof pageToUse != 'undefined' && pageToUse !== 'home'){
        load(pageToUse);
    }
    /** believe unused, cannot find data-href anywhere */
//    if ($('.table').length > 0) {
//        $('.table tr[data-href]').each(function () {
//            $(this).css('cursor', 'pointer').hover(
//                function () {
//                    $(this).addClass('active');
//                },
//                function () {
//                    $(this).removeClass('active');
//                }).click(function () {
//                    document.location = $(this).attr('data-href');
//                }
//            );
//        });
//    }
    /**
     * Add functionality to standard table.
     */
    if($('#exporttable').length > 0){
        AddClickListeners();
        $('#exporttable').on('click-row.bs.table', function (all, row, element, field) {
            if(field !== 'edit' && field !== 'delete' && field.indexOf("_ignore") === -1){
                if(typeof row.id === "undefined"){
                    return ErrorHandler("Could not find the target.");
                }
                window.location.href = $('#exporttable').data('url').replace('json', "overview/" + row.id);
            }
        });
    }
    /**
     * Add the date picker with options to date-something
     */
    if ($('.date-something').length > 0) {
        $('.date-something').datepicker({
            format: 'dd-mm-yyyy',
            clearBtn: true,
            startDate: "01-01-2000",
            todayHighlight: true
        });
    }
    /**
     * Add the date picker with options to ddatepicker
     */
    if($('.datepicker').length > 0){
        $('.datepicker').datepicker({
            todayBtn: "linked",
            todayHighlight: true
        });
    }
    /**
     * fileupload cannot be found, so remove?
     */
//    if($('#fileupload').length > 0){
//        $('#fileupload').fileupload({
//            url: 'module/bla',
//            add: function (e, data) { 
//                var goUpload = true;
//                var uploadFile = data.files[0];
//                if (!(/\.(rar|zip)$/i).test(uploadFile.name)) { 
//                    console.log('You must select an image file only');
//                    goUpload = false; 
//                } 
//                if (uploadFile.size > 2000000) { // 2mb 
//                    console.log('Please upload a smaller image, max size is 2 MB'); 
//                    goUpload = false; 
//                } 
//                if (goUpload == true) { 
//                    data.submit(); 
//                } 
//            }
//        });
//    }
    /**
     * Set an timer on the removing success or error messages, if they exist.
     */
    if($('.successmessage').length > 0 || $('.errormessage').length > 0){
        setTimeout(function(){
            $('#alertcontainer').removeClass('active').removeClass('active2').empty();
        },3000);
    }
    /**
     * Add searchonKeyUp on the neccesary inputs
     */
    if($('.searchOnKeyUp').length > 0){
        /**
        * Add the dropdown to the body and add listener to the scroll to close it, if the maincontainter is scrolled
        */
       if($('.seng_dropdown_calc_pos').length > 0){
           $('.maincontainer').scroll(function(){
               $(".seng_dropdown_calc_pos").hide();
           });
           $('.seng_dropdown_calc_pos').each(function(){
               $(this).detach().appendTo('body');
           });
       }
        recalcPositionDropdowns();
        $('.searchOnKeyUp').each(function(){
            $(this).on('keyup', function (){
                var dataFor = $(this).data('callback');
                var target = $(this);
                var data = {'searchval': target.val(), 'extra_info' : ""};
                if(typeof $(this).data('extra') != 'undefined'){
                    data.extra_info = $(this).data('extra');
                }
                if(typeof $(this).data('extradata') != 'undefined' && typeof window[$(this).data('extradata')] === "function"){
                    data = window[$(this).data('extradata')](data);
                }
                if(target.val().length <= 0 && $('#' + dataFor).length > 0){
                    $('#' + dataFor).val('0');
                }
                window.clearTimeout(timeoutTimer);
                timeoutTimer = window.setTimeout(function(){
                    $(".seng_dropdown_searchOnKeyUp[data-for='" + dataFor + "'] .info").empty();
                    if(target.val().length < 1){
                        return;
                    }
                    $("body").addClass('lazysearching');
                    $.ajax({
                        url: base_url + target.data('control') + '/'+ target.data('method') + '/',
                        type: "POST",
                        data: data,
                        complete: function (responseServer) {
                            $("body").removeClass('lazysearching');
                            var response = JSON.parse(responseServer.responseText);
                            if(response.success === 'success'){
                                if(!target.data('function')){
                                    handleReturnDropdownCreation(response['response'], $(".seng_dropdown_searchOnKeyUp[data-for='" + dataFor + "']"));
                                }else{
                                    window[target.data('function')](response, $(".seng_dropdown_searchOnKeyUp[data-for='" + dataFor + "']"));
                                }               
                            }
                            
                        }
                    });
                },600);
            });
        });
    }
    /**
     * Add an preview on the file, when changed.
     */
    if($('#file').length > 0){
        $("#file").change(function() {
            createPreview(this);
        });
    }
    /*
     * add tooltip
     */
    if($('.fa-info-circle').length > 0){
        $('[data-toggle="tooltip"]').tooltip({container: 'body'}); 
    }
    clockinterval = setInterval(increaseTime, 1000);
});


/**
 * Handle actions for the resice of the window.
 */
$(window).on("resize", function(){
    clearTimeout(timeoutResizer);
    timeoutResizer = setTimeout(function(){
        recalcPositionDropdowns();
    },20);
});

/**
 * Show password.
 * @param {String} target id.
 * @returns Void.
 */
function showHidePassword(target){
    var type = $('#'+target).prop('type');
    if(type === 'password' || type === 'password2'){
        $('#'+target).prop('type', 'text');
    }else{
        $('#'+target).prop('type', 'password');
    }
}
/**
 * Add click listeners to the class remove this.
 * @return {undefined}
 */
function AddClickListeners(){
    $(document).on('click',".remove_this", function(){
        sg_confirm("removeCallAjax" , $(this).data("confirm"), [$(this).data('target-id'), $(this).data('target-string'), $(this).data('id'),$(this).data('callback') ]);
    });
}

/**
 * Handle the remove item through ajax(jsonp).
 * @param {array} array The array with all neccessary data.
 * @return {undefined}
 */
function removeCallAjax(array){
    var project_id = $('#deleteProjectMember').data('extra');
    var id = array[0]; 
    var string = array[1]; 
    var callback = array[2];
    if(typeof callback == 'undefined' || typeof callback == 'null'){
        callback = "callbackRemove";
    }
    $.ajax({
        type: "GET",
        url: base_url + "api/delete/" + string + '/' + project_id + '/',
        jsonpCallback: callback,
        dataType: "jsonp",
        contentType: "application/json; charset=utf-8",
        data: {'id': id},
        error: function(e) {
            console.log(e);
        }
    });
   
}

/**
 * Handle the jsonp callback for remaval of an item.
 * @param {object} response The response from the server.
 * @return {undefined}
 */
function callbackRemove(response){
    if(response.success === 'success'){
        showSucces('success');
        $('#exporttable').bootstrapTable('remove', {field: 'id', values: [response.id]});
    } else {
        ErrorHandler(response.errormessage);
    }
}

/**
 * Show an error to the user.
 * @param {string} string The error to be shown
 * @return {undefined}
 */
function ErrorHandler(string){
    clearTimeout(timeout);
    var html = '<div class="col-md-12 column errormessage">' + string + '</div>';
    $('#alertcontainer').append(html);
    setTimeout(function(){
        $('#alertcontainer').addClass('active');
        $("html, body, .maincontainer ").animate({ scrollTop: "0px" });
        timeout = setTimeout(function(){
            $('#alertcontainer').removeClass('active').empty();
        },3400);
    },10);
}

/**
 * Show an successmessage to the user.
 * @param {string} string The success message to be shown
 * @return {undefined}
 */
function showSucces(string){
    clearTimeout(timeout);
    var html = '<div class="col-md-12 column successmessage active">' + string + '</div>';
    $('#alertcontainer').append(html);
    setTimeout(function(){
        $('#alertcontainer').addClass('active');
        $("html, body, .maincontainer ").animate({ scrollTop: "0px" });
        timeout = setTimeout(function(){
            $('#alertcontainer').removeClass('active').empty();
        },3400);
    },10);
}
/********************************************
 * GENERAL FUNCTIONS
 ********************************************/
/**
 * Play the notification sounds, if turned on.
 * @return {undefined}
 */
function playNotificationSound() {
    if(playSounds){
        document.getElementById('notificationsound').play();
    }
}

/**
 * Get all the tickets for the current user from the server.
 * @return {undefined}
 */
function getTickets() {
    //getStats
    var actionurl = api_url + "alertStats";
    $.get(actionurl, {}, function (data) {
        var currentunread = $("#ticketCount").text();
        if (data['unread'] > currentunread) {
            //play music :P
            playNotificationSound();
        }
        $("#ticketCount").text(data['unread']);
        if (data['unread'] == 0) {
            $("#ticketCount").text("");
        }
        var htmlmenu = "";
        for (var i = 0; i < data['tickets'].length; i++) {
            htmlmenu += '<li><a href="' + server_base_url + 'projects/' + 'overview/' + data['tickets'][i]['project_id'] + '"><i class="fa fa-ticket"></i> New ticket: ' + data['tickets'][i]['subject'] + '</a><hr></li>';
        }
        if (htmlmenu == "") {
            htmlmenu = "<li>No new tickets.</li>";
        }
        $("#ticketMenu").html(htmlmenu);


        //notificationcount
        var currentunread = $("#notificationCount").val();
        if (data['notificationcount'] > currentunread) {
            //play music
            playNotificationSound();
        }
        $("#notificationCount").text(data['notificationcount']);
        if (data['notificationcount'] == 0) {
            $("#notificationCount").text("");
        }
        var htmlmenu = "";
        for (var i = 0; i < data['notificationcount']; i++) {
            htmlmenu += '<li><a href="' + server_base_url + 'project/' + data['notifications'][i]['project_id'] + '"><i class="fa fa-exclamation-circle"></i> ' + data['notifications'][i]['message'] + '</a><hr></li>';
        }
        if (htmlmenu == "") {
            htmlmenu = "<li>No unread notifications.</li>";
        }
        $("#notificationMenu").html(htmlmenu);


        //chat messages
        var currentunread = $("#messageCount").text();
        ;
        if (data['unreadmessages'] > currentunread) {
            //play music
            playNotificationSound();
        }
        $("#messageCount").text(data['unreadmessages']);
        if (data['unreadmessages'] == 0) {
            $("#messageCount").text("");
        }
        var htmlmenu = "";
        for (var i = 0; i < data['unreadmessages']; i++) {
            if (data['messages'][i]['project_id'] > 0) {
                htmlmenu += '<li><a href="' + server_base_url + 'customerProject/' + data['messages'][i]['project_id'] + '" target="_blank"><i class="fa fa-comment-o"></i> ' + data['messages'][i]['message'] + '</a><hr></li>';
            } else {
                htmlmenu += '<li><a href="' + server_base_url + 'dashboard"><i class="fa fa-comment-o"></i> ' + data['messages'][i]['message'] + '</a><hr></li>';
            }
        }
        if (htmlmenu == "") {
            htmlmenu = "<li>No unread messages.</li>";
        }
        $("#messageMenu").html(htmlmenu);
        //messageCount messageMenu

    });
}

/**
 * get all the active todos for the user.
 * @return {undefined}
 */
function GetUserTodos() {
    //get the action-url of the form
    $.ajax({
        url: base_url + 'api' + '/'+ 'getTodos' + '/',
        type: "POST",
        data: {

        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                addTodosToDashboard(response.response);
            }
        }
    });
}

/**
 * Render all the todos and add them to the dashboard
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function addTodosToDashboard(e){
    todos = {};
    var html = "";
    $.each(e, function(i, l){
        var img = l.project.image ? l.project.image : "no_avatar.jpg";
        html += '<div class="todo-project"><a href="' + base_url + 'projects/overview/' + l.project.id + '"><div class="name-entry" style="display:inline-block;"><div class="img-circle fixedimage"><img alt="Project logo" src="' + base_url + 'public/img/projects/' + img + '" class="img-responsive img-circle"></div><span style="float:right;margin-top:4px;">' + l.project.name + '</span></div></a></div>';
        var lengte =  l.todos.length;
        for(var i = 0;i < lengte; i++){
            var t = l.todos[i];var label = "danger"; var textLabel = "High";
            todos[t.id] = {"todo": t, "coupledPersons": l.coupledPersons, 'project_id':  l.project.id};
            if(t.prio == 0){
                label = "default";textLabel = "Low";
            }else if(t.prio == 1){
                label = "warning";textLabel = "Med";
            }
            html += '<div class="todorow todo-mainscreen" data-id="' + t.id + '">' +
                        '<div class="todo-item">' + 
                            '<div class="todo-title"><span class="todo-issue"> Issue: #' + t.id + '</span><span class="label label-' + label + '">' + textLabel + '</span></div>'+ 
                            '<div class="todo-message">' + t.message + '</div>';
            html += '<div class="todo-deadline">Deadline: <span class="deadline">' + t.deadlineWanted + '</span></div>';
            var arrayShow = ['','hidden','hidden',''];
            if(t.done == 2){
                arrayShow = ['hidden','','',''];
            }
            html += '<div class="todo-buttons fadebuttons">' + 
                        '<button class="btn btn-xs btn-warning ' + arrayShow[1] + '" data-target-todo="' + t.id + '" onClick="undoneTodo(this, \'dash\')">' + 
                            '<i class="fa fa-chevron-left"></i>' + 
                        '</button>'+ 
                        '<button class="btn btn-xs btn-success ' + arrayShow[2] + '" data-target-todo="' + t.id + '" onClick="progressTodo(this, \'dash\')">' + 
                            '<i class="fa fa-check"></i>' + 
                        '</button>'+ 
                        '<button class="btn btn-xs btn-warning ' + arrayShow[3] + '" data-target-todo="' + t.id + '" onClick="startEditTodo(this, \'dash\')">' + 
                            '<i class="fa fa-edit"></i>' + 
                        '</button>'+ 
                        '<button data-target-todo="' + t.id + '" onClick="progressTodo(this, \'dash\')" class="btn btn-xs btn-info ' + arrayShow[0] + '">' + 
                            '<i class="fa fa-chevron-right"></i>' + 
                        '</button>' + 
                    '</div>'+
                '</div></div>';
        }
        //var html = '<tr><th colspan="3"><a href="' + base_url + 'project/' i"><div class="name-entry" style="display:inline-block;"><div class="img-circle fixedimage" style="margin-right:10px;"><img alt="Project logo" src="uploads/img/c.png" class="img-responsive img-circle"></div><span style="float:right;margin-top:4px;">Culture Match</span></div></a></th></tr>
    });
    if(html === ""){
        $('#MyTodos').hide();
        $('#MyTodosAreLoading').hide();
        $('#MyTodosAreDone').show();
    }else{
        $('#MyTodos').show();
        $('#MyTodosAreDone').hide();
        $('#MyTodosAreLoading').hide();
        $('#MyTodos .todo-table').empty().append(html);
    }
}

/**
 * Handle the edit action off an todo for the 2 different screens.
 * @param {DomElement} ele The element that wass clicked
 * @param {string} string The string with the extra infor of which screen it is.
 * @return {undefined}
 */
function startEditTodo(ele, string){
    var id = $(ele).data('target-todo');
    var info = todos[id];
    if(info && string == 'dash'){
        $('#todo_id_todo').val(id);
        $('#project_id_todo').val(info.project_id);
        $('#message_todo').val(info.todo.message);
        $('#prio_todo option[value="' + info.todo.prio + '"]').prop("selected", true);
        $('#user_id_todo option:not(.standard)').remove();
        $('#user_id_todo').append(getDropDownCoupledPersons(info.coupledPersons));
        if(info.todo.user_id > 0){
            $('#user_id_todo option[value="' + info.todo.user_id + '"]').prop("selected", true);
        }else{
            $('#user_id_todo option[value="-1"]').prop("selected", true);
        }
        $('#deadline_todo').val(info.todo.deadline);
        $('#editTodo').modal('show');
    }else if(info && string == 'project'){
        $('#todo_id_todo').val(id);
        $('#project_id_todo').val($('#project_id').val());
        $('#message_todo').val(info.message);
        $('#prio_todo option[value="' + info.prio + '"]').prop("selected", true);
        $('#user_id_todo option').remove();
        $('#user_id_todo').append($('#todo_user_id').html());
        if(info.user_id > 0){
            $('#user_id_todo option[value="' + info.user_id + '"]').prop("selected", true);
        }else{
            $('#user_id_todo option[value="-1"]').prop("selected", true);
        }
        $('#deadline_todo').val(info.deadline);
        $('#editTodo').modal('show');
    }
}

/**
 * Get the drop down with all the coupled people.
 * @param {array} array The array with all the coupled people
 * @return {String}
 */
function getDropDownCoupledPersons(array){
    var length = array.length; var html = "";
    for(var i = 0; i<length; i++){
        html += '<option value="' + array[i].id + '">' + array[i].name + '</option>';
    }
    return html;
}

/**
 * Update an todo with the new info filled in, if correct.
 * @return {undefined}
 */
function upateTodoFromMain(){
    $('#todo_message').removeClass('has-error');
    $('#todo_deadline').removeClass('has-error');
    var message = $('#message_todo').val();
    var prio = $('#prio_todo').val();
    var user = $('#user_id_todo').val();
    var deadline = $('#deadline_todo').val();
    var project = $('#project_id_todo').val();
    var id = $('#todo_id_todo').val();
    var gadoor = true;
    if(!$.isNumeric(project) || project < 1 || !$.isNumeric(id) || id < 1){
        return;
    }
    var today = new Date();
    today.setHours(0,0,0,0);
    var tempDeadline = new Date(deadline);
    if(today > tempDeadline){
        $('#deadline_todo').addClass('has-error');
        $('.errorrow-modal').text("Deadline has passed, please update the deadline.");
        gadoor = false;
    }
    if(message.length < 2){
        $('#message_todo').addClass('has-error');
        $('.errorrow-modal').text("The message is to short.");
        gadoor = false;
    }
    if(gadoor){
        $.ajax({
            url: base_url + 'api' + '/'+ 'addTodoToProject' + '/',
            type: "POST",
            data: {
                'prio'      :   prio,
                'project_id':   project,
                'message'   :   message,
                'user_id'   :   user,
                'deadline'  :   deadline,
                'id'        :   id
            },
            complete: function (data) {
                var response = JSON.parse(data.responseText);
                if(response.success === 'success'){
                    updateTodoMainScreen();
                }
            }
        });
    }else{
        $('.errorrow-modal').css({"height": "50px", "margin-bottom" : "16px"});
        setTimeout(function(){$('.errorrow-modal').css({"height": "0px", "margin-bottom" : "0px"});}, 3500);
    }
}

/**
 * Handle the delete action for a todo.
 * @param {string} string The confirmation string.
 * @return {undefined}
 */
function removeTodoFromMain(string){
    $('#todo_message').removeClass('has-error');
    $('#todo_deadline').removeClass('has-error');
    var id = $('#todo_id_todo').val();
    if(!$.isNumeric(id) || id < 1){
        return;
    }
    sg_confirm("removeTodoFromMainAjax" , string, [id]);
}

/**
 * Handle the call to the server for deletion of the todo.
 * @param {array} array The array with information from the confirm.
 * @return {undefined}
 */
function removeTodoFromMainAjax(array){
    $.ajax({
        url: base_url + 'api' + '/'+ 'deleteTodo' + '/',
        type: "POST",
        data: {
            'id'        :   array[0]
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                updateTodoMainScreen();
            }
        }
    });
}

/**
 * Update the todos.
 * @return {undefined}
 */
function updateTodoMainScreen(){
    if($('#project_id').length > 0){
        getAllTheTodosProject();
        checkWhetherLAstItem();
    }else{
        GetUserTodos();
    }
    $('#editTodo').modal('hide');
}

/**
 * Progress an todo to the new status.
 * @param {domelement} ele The domelement clicked.
 * @param {string} string The string saying which screen this is.
 * @return {undefined}
 */
function progressTodo(ele, string){
    var id = $(ele).data('target-todo');
    $.ajax({
        url: base_url + 'api' + '/'+ 'progressTodo' + '/',
        type: "POST",
        data: {
            'id'        :   id
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                updateProgressTodo(response.response, string);
            }else{
                ErrorHandler(response.errormessage);
            }
        }
    });
}

/**
 * Deprogress an todo to a lower status
 * @param {domelement} ele The domelement clicked.
 * @param {string} string The string saying which screen this is.
 * @return {undefined}
 */
function undoneTodo(ele, string){
    var id = $(ele).data('target-todo');
    $.ajax({
        url: base_url + 'api' + '/'+ 'deProgressTodo' + '/',
        type: "POST",
        data: {
            'id'        :   id
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                updateProgressTodo(response.response, string);
            }else{
                ErrorHandler(response.errormessage);
            }
        }
    });
}

/**
 * Update an todo to the new status.
 * @param {object} e The response from the server.
 * @param {string} string The string representing the page we are on.
 * @return {undefined}
 */
function updateProgressTodo(e, string){
    if(string == 'dash'){
        //dashboard
        var target = $('.todorow[data-id="' + e.id+ '"]').find('.fadebuttons');
        console.log(target);
        if(e.done == 1){
            target.find('button').eq(3).removeClass('hidden');
            target.find('button').eq(0).addClass('hidden');
            target.find('button').eq(1).addClass('hidden');
        }else if(e.done == 2){
            target.find('button').eq(3).addClass('hidden');
            target.find('button').eq(0).removeClass('hidden');
            target.find('button').eq(1).removeClass('hidden');
        }else{
            target = $('.todorow[data-id="' + e.id+ '"]');
            target.addClass('hidden');
            checkWhetherLAstItem();
        }
    }else{
        //project overzicht
        var Cloned = $('.todorow[data-id="' + e.id+ '"]').detach();
        if(e.done == 1){
            target = Cloned.find('.fadebuttons');
            console.log(Cloned);
            target.find('button').eq(4).removeClass('hidden');
            target.find('button').eq(0).addClass('hidden');
            target.find('button').eq(1).addClass('hidden');
            target.find('button').eq(2).addClass('hidden');
            $('#ScrumTodoPassive .todo-table').prepend(Cloned);
        }else if(e.done == 2){
            target = Cloned.find('.fadebuttons');
            target.find('button').eq(4).addClass('hidden');
            target.find('button').eq(0).removeClass('hidden');
            target.find('button').eq(1).removeClass('hidden');
            target.find('button').eq(2).addClass('hidden');
            $('#ScrumTodoActive .todo-table').prepend(Cloned);
        }else{
            target = Cloned.find('.fadebuttons');
            target.find('button').eq(4).addClass('hidden');
            target.find('button').eq(0).addClass('hidden');
            target.find('button').eq(1).addClass('hidden');
            target.find('button').eq(2).removeClass('hidden');
            $('#ScrumTodoDone .todo-table').prepend(Cloned);
        }
    }
}

/**
 * Handle showing the done if no more todo's available.
 * @return {undefined}
 */
function checkWhetherLAstItem(){
    if($('#MyTodos .todorow:not(.hidden)').length <= 0){
        $('#MyTodos').hide();
        $('#MyTodosAreLoading').hide();
        $('#MyTodosAreDone').show();
    }
}
/**
 * LOADS THE PAGE AND CALLS THE CORRECT LOADING FUNCTIONS PER PAGE
 * @param  {[String]} pageparam [current page]
 */
function load(pageparam) {
    page = pageparam;
    setInterval(function () {
        getTickets();
    }, 60000);//getTickets()
    getTickets();
    setInterval(function () {
        getStats(false);
    }, 60000);
    getStats(false);
    if (page == "dashboard") {
        setInterval(function () {
            GetUserTodos();
        }, 60000);
        GetUserTodos();
        if($('#MyStats').length > 0){
            setInterval(function () {
                getAdminInfo();
            }, 60000);
            getAdminInfo();
        }
    }

}

/**
 * Get the projectId if it exists in the page.
 * @return {undefined}
 */
function getProjectId(){
    var projectID = 0;
    if($('#project_id').length > 0 && $('#project_id').val() > 0){
        projectID = $('#project_id').val();
    }
    localStorage.setItem("projectID", projectID);
}

/**
 * Get the stats for the timer worked on and keepiong track if person still works on something.
 * @param {Boolean} Bool Whether we also need to get the time.
 * @return {undefined}
 */
function getStats(Bool) {
    var logButton = localStorage.getItem('logButton');
    var projectID = localStorage.getItem("projectID");
    if(!$.isNumeric(projectID) || projectID <= 0){
        projectID = -1;
    }
    $.ajax({
        url: base_url + 'api' + '/'+ 'updateUserStats' + '/',
        type: "POST",
        data: {
            'project' :  projectID,
            'logButton' : logButton
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                if(typeof response.project !== "undefined"){
                    $('#project-log-timer .projectname-log-timer').text(response.project);
                }
                if(Bool === true){
                    getTime();
                }
            }
        }
    });
}

/**
 * Recalculating the position of the dropdown menu. So that we can place the dropdown in the right position.
 * @returns void.
 */
function recalcPositionDropdowns(){
    $('.seng_dropdown_calc_pos').each(function(){
        var toChange = $(this);
        var standardOffset = {top:33, left:0};
        if(typeof toChange.data("parent") !== "undefined" && $(toChange.data("parent")).length > 0){
            standardOffset.top = standardOffset.top - $(toChange.data("parent")).scrollTop();
        }
        var input = $('input.searchOnKeyUp[data-target-id="' + toChange.data('for') + '"]');
        var offset = input.offset();
        var newTop = offset.top + 33;
        toChange.css({left: offset.left + "px", top: newTop + "px", width: input.outerWidth() + "px"});
    });
}

/**
 * Create the dropdowns for the lazysearch.
 * @param {object} e The response of the server.
 * @param {object} target The jQuery object of the element targeted.
 * @return {undefined}
 */
function handleReturnDropdownCreation(e, target){
    var html = '';
    $(".seng_dropdown_searchOnKeyUp[data-for='" + target.data('for') + "']").show();
    $.each(e, function(i, val){
        html += "<div class='option dropdown dropdown_" + val.id + "' data-value='" + val.id + "' onClick=\"javascript:selectThisOne(this, '" + target.data('for') + "')\">" + val.name + "</div>";
    });
    if(html.length > 0){
        target.find(".info").empty().append(html);
    }else{
        target.find(".info").empty().append('<div class="option dropdown unselectable">nothing found</div>');
    }
    target.show();
    /**
     * Add the functionality for listening to clicks outside of the dropdown
     */
    $(document).on('mouseup.HideUs', function(e){
        if (!target.is(e.target) && target.has(e.target).length === 0){
            target.hide();
            $(document).off('.HideUs');
            var def = $('input[data-callback="' + target.data('for') + '"]').prop("defaultValue");
            def = def ? def : "";
            var def2 = $('input[data-id="' + target.data('for') + '"]').prop("defaultValue");
            $('input[data-callback="' + target.data('for') + '"]').val(def);
            $('input[data-id="' + target.data('for') + '"]').val(def2);
        }
    });
}

/**
 * Select an item in the lazysearch dropdown.
 * @param {domelement} element The element clicked
 * @param {string} targetfor The target for the newly selected value.
 * @return {undefined}
 */
function selectThisOne(element, targetfor){
    var target = $(".searchOnKeyUp[data-callback='" + targetfor + "']");
    var targetForId = $("#" + target.data('target-id'));
    var bronElement = $(element);
    target.val(bronElement.text());
    targetForId.val(bronElement.data('value'));
    $(".seng_dropdown_searchOnKeyUp[data-for='" + targetfor + "']").hide();
    $(document).off('.HideUs');
}

/**
 * Creates a preview for the uploaded image if the file uploaded is a png, jpg, jpeg.
 * 
 * @param {array} input array with the inputs
 * @returns {void}
 */
function createPreview(input){
    if (input.files && input.files[0] && input.files[0].name.match(/\.(jpg|jpeg|png|PNG|JPG|JPEG)$/)) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImage').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }else{
        ErrorHandler("invalid image.");
    } 
}

/**
 * Get the information for the admins.
 * @return {undefined}
 */
function getAdminInfo() {
    //getStats
    var actionurl = base_url + "api/onlineAdminStats/";
    $.get(actionurl, {}, function (data) {
        if (data['users_online']) {
            $("#users_online").text(data['users_online']);
        }
        if (data['projects_in_progress'] != "") {
            $("#projects_in_progress").text(data['projects_in_progress']);
        }
        if (data['projects_in_feedback'] != "") {
            $("#projects_in_feedback").text(data['projects_in_feedback']);
        }
        if (data['projects_in_testing'] != "") {
            $("#projects_in_testing").text(data['projects_in_testing']);
        }
        if (data['secondsworked']) {
            var secs = data['secondsworked'];
            var mins = data['secondsworked'] / 60;
            var hours = parseInt(mins / 60);
            mins = parseInt(mins - parseInt(hours * 60));
            secs = parseInt(secs - parseInt(hours * 3600) - parseInt(mins * 60));
            $("#work_count").text(hours + ":" + mins + ":" + secs);
        }
        if (data['secondsworked_yesterday'] > 0) {
            var dif = parseInt(((data['secondsworked'] / data['secondsworked_yesterday'])) * 100 - 100);
            var html = "";
            if (dif > 0) {
                html = '<i class="green" ><i class="fa fa-sort-asc"></i>' + dif + '% </i> From yesterday';
            } else {
                html = '<i class="red" ><i class="fa fa-sort-desc"></i>' + dif + '% </i> From yesterday';
            }
            $("#work_perc").html(html);
        } else {
            $("#work_perc").html("");
        }
    });
}

/**
 * Save the user settings.
 * @return {Boolean}
 */
function storeUserSettings(){
    var show_stats = $('#show_stats').is(':checked') ? 1 : 0;
    var play_sound = $('#play_sounds').is(':checked') ? 1 : 0;
    var username = $('#username').val();
    var email = $('#email').val();
    $.ajax({
        url: base_url + 'api' + '/'+ 'storeUserSettings' + '/',
        type: "POST",
        data: {
            'show_stats' :  show_stats,
            'play_sound' :  play_sound,
            'username' :  username,
            'email' :  email
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.errormessage != ""){
                ErrorHandler(response.errormessage);
            }else if(response.success === 'success'){
                showSucces('success');
            }
        }
    });
    return false;
}

/**
 * Toggle the sidebar.
 * @return {undefined}
 */
function toggleSideBar(){
    $('#sidebar').toggleClass('show');
}

/**
 * Get the currenttime.
 * @returns void.
 */
function getTime(){
    var actionurl = base_url + "api/onlineAdminStats/";
    if(localStorage.getItem("logButton") === "stop"){
        seconds = 0;
        showTime(0);
        return;
    }
    $.get(actionurl, {}, function (data) {
        if (data['newtime']) {
            showTime(data['newtime']);
            seconds = data['newtime'];
        } else {
            showTime(0);
            seconds = 0;
        }
    });  
}

/**
 * Increases the secondes.
 * @returns void.
 */
function increaseTime(){
    if(localStorage.getItem("logButton") === "play"){   
        seconds ++;
    }
    showTime(seconds);
}

/**
 * parsing the values to hours, minuts, seconds and showing it in the view.
 * @param {int} $sec showing the seconds.
 * @returns void.
 */
function showTime($sec){
    if(localStorage.getItem("logButton") === "stop"){
        var s = 0;
        var m = 0;
        var h = 0;  
    } else { 
        var h = Math.floor($sec / 3600);
        var m = Math.floor(($sec % 3600) /60);
        var s = $sec % 60;
    }
    if(h<10){
        h = "0"+h;
    }
    if(m<10){
        m = "0"+m;
    }
    if(s<10){
        s = "0"+s;
    }
    var time = h + ":" + m + ":" + s + " ";
    localStorage.setItem("time", time);
    document.getElementById("MyClockDisplay").innerText = time;
    document.getElementById("MyClockDisplay").textContent = time;
}

/**
 * confirm the delete action.
 * @param {Domelement} ele The element that was clicked.
 * @returns {undefined}
 */
function confirmDeleteAction(ele){
    var $ele = $(ele);
    if($ele.length <= 0 || typeof $ele.data('href') === "undefined" || typeof $ele.data('confirm') === "undefined"){
        ErrorHandler("Values not correctly set. If this error consists on page reload, please contact administrator.");
        return;
    }
    sg_confirm("deletePersonCompany" , $ele.data("confirm"), [$ele.data('href'), $ele.data('id')]);
}

/**
 * Handle the delete person company.
 * @param {array} array The array returned by sg_confirm, with on 0 the link, and on 1 the id for the target.
 * @returns {undefined}
 */
function deletePersonCompany(array){
    $.ajax({
        url: array[0],
        type: "POST",
        data: {},
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.errormessage !== ""){
                ErrorHandler(response.errormessage);
            }else if(response.success === 'success'){
                RemoveImagePersonCompany(array[1]);
                showSucces('success');
            }
        }
    });
}

/**
 * Handle the response of the server to remove the element.
 * @param {string} target The data id to be removed.
 * @returns {undefined}
 */
function RemoveImagePersonCompany(target){
    var $ele = $('.personcompany-card[data-id="' + target + '"]');
    if($ele.length <= 0){
        return;
    }
    $ele.remove();
}