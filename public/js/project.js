/*
 * <TODO>
 *      -add error handler if callback is not success
*  </TODO>
*/
/**
 * create the lis of persons for the adding of a person to the team working on the project
 * @param {object} e    The response of the server.
 * @param {string} target The target id to be filled in.
 * @return {undefined}
 */
function addToTeam(e, target){
    var html = '';
    var noImage =  'no_avatar.jpg';
    $(".seng_dropdown_searchOnKeyUp[data-for='" + target.data('for') + "']").show();
    $.each(e.response, function(i, val){
        if(val.logo === ""){
            val.logo = noImage;
        }
        html += "<div class='option dropdown dropdown_" + val.id + "' data-value='" + val.id + "' onClick=\"javascript:selectThisOne(this, '" + target.data('for') + "')\">" + val.name + "</div>";
    });
    if(html.length <= 0){
       html = '<div class="option dropdown unselectable">nothing found</div>';
    }
    if(typeof e.response_extra !== "undefined"){
        html += "<div class=\"seperator unselectable\"></div>";
        $.each(e.response_extra, function(i, val){
            if(val.logo === ""){
                val.logo = noImage;
            }
            html += "<div class='option dropdown dropdown_" + val.id + "' data-value='" + val.id + "' onClick=\"javascript:selectThisOne(this, '" + target.data('for') + "')\">" + val.name + "</div>";
        });
    }
    target.find(".info").empty().append(html);
    target.show();
    $(document).on('mouseup.HideUs', function(e){
        if (!target.is(e.target) && target.has(e.target).length === 0){
            target.hide();
            $(document).off('.HideUs');
            $('input[data-callback="' + target.data('for') + '"]').val("");
        }
    });
}

/**
 * Add the new person to the project as a worker
 * @return {undefined}
 */
function addToList(){
    var id = $('#addToTeam_id').val();
    var project = $('#project_id').val();
    if(!$.isNumeric(id) || id < 1 || !$.isNumeric(project) || project < 1){
        return;
    }
    $.ajax({
        url: base_url + 'api' + '/'+ 'addPersonToProject' + '/',
        type: "POST",
        data: {
            'id': id,
            'project': project
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                addOptionToList(response);
                $('input[data-target-id="addToTeam_id"]').val('');
            } else {
                addOptionToList(response.errormessage);
                $('input[data-target-id="addToTeam_id"]').val('');
            }
        }
    });
}

/**
 * add a person to all the lists on the page.
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function addOptionToList(e){
    if(e.add){
        var html = '<div class=\"row\"><p class="teamMemberName col-md-4">' + e.add.name +'</p>';
        html += '<div class="btn btn-xs btn-danger fadebuttons fadebuttonsPTeam" onClick="callDelete(this)" data-target-id=' + e.id + '" data-target-string="coupled_person" data-confirm="Are you sure to delete this person"><i class="fa fa-trash"></i></div>'
        + "</div>";
        $('#teamofproject').append(html); 
        var option = '<option value="' + e.add.account_id + '">' + e.add.name + '</option>';
        $('#todo_user_id').append(option);
        showSucces('success');
    } else {
        ErrorHandler(e);
    }
}

// We can attach the `fileselect` event to all file inputs on the page
$(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

// We can watch for our custom `fileselect` event like this
$(document).ready( function() {
    $(':file').on('fileselect', function(event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        if( input.length ) {
            input.val(log);
        }
    });
    getAllTheTodosProject();
});

/**
 * upload an attachment to the serrver.
 * @return {undefined}
 */
function uploadAttachment(){
    var form = new FormData();
    form.append('upload', document.getElementById('upload').files[0]);
    form.append('id', $('#project_id').val());
    $.ajax({
        url: base_url + 'api' + '/'+ 'addAttachmentToProject' + '/',
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data : form,
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                addFileToList(response.response);
                showSucces('success');
            }else{
                ErrorHandler(response.errormessage);
            }
        }
    });
}

/**
 * Add the new file to the list of atachments
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function addFileToList(e){
    var html = '<div class="row" data-id="' + e.id + '"><div class="half-by-half">' + e.by + '</div><div class="half-by-half"><a href="' + base_url + 'projects/downloadpdf/' + e.location + '" target="_blank"><i class="fa fa-' + e.type + '"></i> ' + e.fileName + '</a></div>';
    html += '<div class="half-by-half-del"><div class="btn btn-xs btn-danger fadebuttons" onClick="callDelete(this)" data-target-id=' + e.id + '" data-target-string="attachment" data-confirm="Are you sure to delete this attachment"><i class="fa fa-trash"></i></div></div></div>';                   
    $('#attachmentsProjects').append(html);
    $('#addAttachmentToProject > input').val("");
}

/**
 * Handle the delete.
 * @param {domelement} ele The domelement clicked.
 * @return {undefined}
 */
function callDelete(ele){
    sg_confirm("removeCallAjax", $(ele).data('confirm'), [$(ele).data('target-id'), $(ele).data('target-string'), $(ele).data('callback')]);
}

/**
 * The handler for a successful call to the server to delete a attachment.
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function removeattachmentsProjects(e){
    if(e.success == 'success'){
        showSucces('success');
        $('#attachmentsProjects .row[data-id="' + e.id + '"]').remove();
    }else{
        ErrorHandler(e.errormessage);
    }
}

/**
 * The handler for a successful call to the server to delete a person from the team.
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function removeTeamProjectMember(e){
    if(e.success == 'success'){
        showSucces('success');
        $('#teamofproject .row[data-id="' + e.id + '"]').remove();
    }else{
        ErrorHandler(e.errormessage);
    }
}

/**
 * Check and then create a ticket for the open project.
 * @return {undefined}
 */
function sendTicket(){
    $('#subjectTicket').removeClass('has-error');
    $('#messageTicket').removeClass('has-error');
    var subject = $('#subjectTicket').val();
    var message = $('#messageTicket').val();
    var project = $('#project_id').val();
    if(!$.isNumeric(project) || project < 1){
        return;
    }
    var continueThis = true;
    if(subject.length < 2){
        $('#subjectTicket').addClass('has-error');
        continueThis = false;
    }
    if(message.length < 2){
        $('#messageTicket').addClass('has-error');
        continueThis = false;
    }
    if(continueThis){
        $.ajax({
            url: base_url + 'api' + '/'+ 'addTicketToProject' + '/',
            type: "POST",
            data: {
                'subject': subject,
                'project': project,
                'message': message
            },
            complete: function (data) {
                var response = JSON.parse(data.responseText);
                if(response.success === 'success'){
                    addTicketToList(response.response);
                    $('#messageTicket').val('');
                    $('#subjectTicket').val('');
                } else {
                    addTicketToList(response.errormessage);
                }
            }
        });
    }
}

/**
 * Add the ticket created to the list.
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function addTicketToList(e){
    if(e === "variables are not correct."){
        $('#ticketsoffproject').val();
        ErrorHandler(e);
    } else {
        var html = '<div class="row" data-id="'+ e.id + '"><div class="quarter-by-quarter">';
        html += e.send + '</div><div class="quarter-by-quarter">' + e.from + '</div><div class="quarter-by-quarter">';
        html += '<a data-toggle="collapse" href="#ticket' + e.id + '" aria-expanded="false" aria-controls="ticket' + e.id + '" class="">' + e.subject + '</a>';
        html += '</div><div class="quarter-by-quarter del"><div class="btn btn-danger fadebuttons" data-callback="removeticketsProjects" onClick="callDelete(this)" data-target-id="' + e.id + '" data-target-string="ticket" data-confirm="Are you sure to delete this ticket?">';
        html += '<i class="fa fa-trash"></i></div></div><div class="collapse" id="ticket' + e.id + '" aria-expanded="false">' + e.message + '</div></div>';
        $('#ticketsoffproject').append(html);
        $('#ticketsoffproject').val();
    }
}

/**
 * Remove the ticker from the project
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function removeticketsProjects(e){
    if(e.success == 'success'){
        showSucces('success');
        $('#ticketsoffproject .row[data-id="' + e.id + '"]').remove();
    }else{
        ErrorHandler(e.errormessage);
    }
}

/**
 * Add a todo to the current project.
 * @return {undefined}
 */
function addTodoToProject(){
    $('#todo_message').removeClass('has-error');
    $('#todo_deadline').removeClass('has-error');
    var message = $('#todo_message').val();
    var prio = $('#todo_prio').val();
    var user = $('#todo_user_id').val();
    var deadline = $('#todo_deadline').val();
    var project = $('#project_id').val();
    var continueThis = true;
    if(!$.isNumeric(project) || project < 1){
        return;
    }
    var today = new Date();
    today.setHours(0,0,0,0);
    var tempDeadline = new Date(deadline);
    if(today > tempDeadline){
        $('#todo_deadline').addClass('has-error');
        continueThis = false;
    }
    if(message.length < 2){
        $('#todo_message').addClass('has-error');
        continueThis = false;
    }
    if(continueThis){
        $.ajax({
            url: base_url + 'api' + '/'+ 'addTodoToProject' + '/',
            type: "POST",
            data: {
                'prio'      :   prio,
                'project_id':   project,
                'message'   :   message,
                'user_id'   :   user,
                'deadline'  :   deadline
            },
            complete: function (data) {
                var response = JSON.parse(data.responseText);
                if(response.success === 'success'){
                    addTodoToList(response.response);
                    $("#todo_message").val("");
                }
            }
        });
    }
}

/**
 * Get all the todos from the server.
 * @return {undefined}
 */
function getAllTheTodosProject(){
    var project = $('#project_id').val();
    if(!$.isNumeric(project) || project < 1){
        return;
    }
    $.ajax({
        url: base_url + 'api' + '/'+ 'getTodosFromProject' + '/',
        type: "POST",
        data: {
            'project_id' : project
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                addAllTodosToList(response.response);
            }
        }
    });
}

/**
 * Add a single new todo to the list.
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function addTodoToList(e){
    var html = buildHtmlTodo(e);
    $('#ScrumTodoPassive .todo-table').prepend(html);
}

/**
 * Loop through all todos and add them.
 * @param {object} e The response of the server.
 * @return {undefined}
 */
function addAllTodosToList(e){
    var htmlTodo = "";
    var htmlProgress = "";
    var htmlDone = "";
    $.each(e, function(i,v){
        todos[v.id] = v;
        var temp = buildHtmlTodo(v);
        if(v.done == 1){
            htmlTodo += temp;
        }else if(v.done == 2){
            htmlProgress += temp;
        }else{
            htmlDone += temp;
        }
    });
    $('#ScrumTodoPassive .todo-table').empty().append(htmlTodo);
    $('#ScrumTodoActive .todo-table').empty().append(htmlProgress);
    $('#ScrumTodoDone .todo-table').empty().append(htmlDone);
}

/**
 * Build the html of the todo.
 * @param {object} e The single todo to be made into html.
 * @return {String}
 */
function buildHtmlTodo(e){
    var label = "danger";
    var textLabel = "High";
    if(e.prio == 0){
        label = "default";
        textLabel = "Low";
    }else if(e.prio == 1){
        label = "warning";
        textLabel = "Med";
    }
    var arrayShow = ['','hidden','hidden','hidden',''];
    if(e.done == 2){
        arrayShow = ['hidden','','','hidden',''];
    }
    if(e.done == 3){
        arrayShow = ['hidden','hidden','hidden','',''];
    }
    todos[e.id] = e;
    var person = e.person_data ? e.person_data : {"name" : ""};
    var html = '<div class="todorow" data-id="' + e.id + '">' + 
                    '<div class="todo-item"><div class="todo-title"><span class="todo-name">' + person.name + 
                        '</span><span class="todo-issue"> Issue: #' + e.id + '</span>' + 
                        '<span class="label label-' + label + '" style="">' + textLabel + '</span></div>' + 
                        '<div class="todo-buttons fadebuttons">' + 
                            '<button class="btn btn-xs btn-warning ' + arrayShow[1] + '" data-target-todo="' + e.id + '" onClick="undoneTodo(this, \'project\')">' + 
                                '<i class="fa fa-chevron-left"></i>' + 
                            '</button>'+ 
                            '<button class="btn btn-xs btn-success ' + arrayShow[2] + '" data-target-todo="' + e.id + '" onClick="progressTodo(this, \'project\')">' + 
                                '<i class="fa fa-check"></i>' + 
                            '</button>'+ 
                            '<button class="btn btn-xs btn-success ' + arrayShow[3] + '" data-target-todo="' + e.id + '" onClick="undoneTodo(this, \'project\')">' + 
                                '<i class="fa fa-undo"></i>' + 
                            '</button>'+     
                            '<button class="btn btn-xs btn-warning ' + arrayShow[4] + '" data-target-todo="' + e.id + '" onClick="startEditTodo(this, \'project\')">' + 
                                '<i class="fa fa-edit"></i>' + 
                            '</button>'+     
                            '<button data-target-todo="' + e.id + '" onClick="progressTodo(this, \'project\')" class="btn btn-xs btn-info ' + arrayShow[0] + '" >' + 
                                '<i class="fa fa-chevron-right"></i>' + 
                            '</button>' + 
                        '</div><div class="todo-message">' +
                        e.message + '</div><div class="todo-deadline">Deadline: <span class="deadline">' + e.deadlineWanted + '</span></div></div>' +
                '</div>';
    return html;
}

/**
 * Send the call to the server to change the status.
 * @returns {undefined}
 */
function changeStatusProjectOverview(){
    var project = $('#project_id').val();
    var newStati = $('#changestatuspicker').val();
    console.log(project, newStati);
    if(!$.isNumeric(project) || project < 1 || !$.isNumeric(newStati) || newStati < 0){
        return;
    }
    $.ajax({
        url: base_url + 'api' + '/'+ 'changeStatusProject' + '/' + project + '/' + newStati + '/',
        type: "POST",
        data: {
        },
        complete: function (data) {
            var response = JSON.parse(data.responseText);
            if(response.success === 'success'){
                $('#changeStatus').modal('hide');
                changeStatusBlockProjectOverview(response.title, response.extension);
                showSucces('success');
            }else{
                var target = $('#changeStatus .errorrow-modal');
                target.text(response.errormessage);
                target.css({"height": "50px", "margin-bottom" : "16px"});
                setTimeout(function(){target.css({"height": "0px", "margin-bottom" : "0px"});}, 3500);
            }
        }
    });
}
/**
 * Change the block in which the status is set.
 * @param {String} title        The title of the new status;
 * @param {String} extension    The class of the new status.
 * @returns {undefined}
 */
function changeStatusBlockProjectOverview(title, extension){
    $('.statusDiv .circleOfStatus').removeAttr("class").addClass('circleOfStatus  alert alert-' + extension);
    $('.statusDiv .statusText').text(title);
}