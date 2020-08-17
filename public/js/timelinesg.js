/**
 * Timeline creater.
 *
 * This creates the html for the timelines.
 *
 * @category   Javascript
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * <TODO>Add functionality of taking a color per project.</TODO>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details. <https://www.gnu.org/licenses/>
 */

/**
 * To create a scope for the timetable.
 * @param {object} $        The jquery library.
 * @param {object} window   The window.
 * @param {object} document The document.
 * @returns {undefined}
 */
(function($, window, document) {
    'use strict';
    /**
     * the class it standard targets.
     * @type {String}
     */
    var DEFAULT_SGTIMELINE = '.modal_alert';
    /**
     * Allowed data to be filled in and the type it should be.
     * @type object
     */
    var ALLOWED_DATA = {
        element             : {requires : "object", value: null},
        width               : {requires : "string", value: null},
        height              : {requires : "string", value: null},
        hover               : {requires : "function", value: null},
        click               : {requires : "function", value: null},
        colors              : {requires : "array", value: null},
        rows                : {requires : "number", value: null},
        title               : {requires : "string", value: null},
        length              : {requires : "string", value: null},
        "class"             : {requires : "string", value: null},
        "yearDefaultWidth"  : {requires : "number", value: null},
        between             : {requires : "number", value: null}
    };
    /**
     * The allowed lengths
     * @type Array
     */
    var acceptedLength = ["week", "month", "year"];
        
    /**
     * Sort function for objects with the left key.
     * @param {object} a The first object to be compared.
     * @param {object} b The second object to be compared.
     * @returns {Number}
     */
    function sortByLeft(a, b){
        if(a.left < b.left){
            return -1;
        }else if(a.left > b.left){
            return 1;
        }else{
            return 0;
        }
    }
        
    /**
     * Extend date for getting weeknumbers.
     * @returns {Number}
     */
    Date.prototype.getWeekNumber = function(){
        var d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()));
        var dayNum = d.getUTCDay() || 7;
        d.setUTCDate(d.getUTCDate() + 4 - dayNum);
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
        return Math.ceil((((d - yearStart) / 86400000) + 1)/7);
    };
    
    var sgTimeline = function(element, options){
        this.element = {};
        this.width = "100%";
        this.height = "80px";
        this.rows   = 4;
        this.length = "week";
        this.between = 2;
        this.minwidth = 0.7;
        this.class = "";
        this.hover = null;
        this.click = null;
        this.colors = ['grey','darkblue', 'green', 'purple', 'blue', 'lightblue',  'violett'];
        this.rowsData = [];
        this.currentRows = [];
        this.title ="";
        this.yearDefaultWidth = "5";
        this.realWidth = 0;
        this.lineleft = 0;
        this.clickAdded = false;
        
        
        /**
        * initialize hte modalAlert
        * @returns {undefined}
        */
        this.init = function(){
            if(!this.element){
                if($(this.target).length === 1){
                    this.element = $(this.target);
                }else if($(this.target).length > 1){
                    this.element = $(this.target).eq(0);
                }else{
                    this.printError("Element did not exist.");
                    return;
                }
            }
            this.realWidth = this.element.width();
            for(var i = 0; i < this.rows; i++){
                this.currentRows.push({"items" : [], "right" : 0, "left" : 0});
            }
            if($('#timesheet-tooltip').length <= 0){
                this.createTooltip();
            }
            this.createObject();
            var that = this;
            if(typeof this.element.data("clickable") === "undefined"){
                if(this.click !== "" && typeof this.click === "function"){
                    this.element.on("click", ".bubble.bubble-lorem", function(){
                        that.click(this);
                    });
                }
                if(this.hover !== "" && typeof this.hover === "function"){
                    this.element.on("mouseover", ".bubble.bubble-lorem", function(){
                        that.hover(this, "mouseover");
                    });
                    this.element.on("mouseout", ".bubble.bubble-lorem", function(){
                        that.hover(this, "mouseout");
                    });
                }
                that.element.data("clickable", true);
            }
        };
        
        /**
         * Create the tooltip. (Should only be called After checking it exists.)
         * @returns {undefined}
         */
        this.createTooltip = function (){
            var html = "<div id='timesheet-tooltip'><div class='timesheet-tooltip-content'></div></div>";
            $("body").append(html);
        };
        
        /**
         * Create the html for the timesheets.
         * @returns {undefined}
         */
        this.createObject = function(){
            var extraClass = "timesheet-week";
            var arrayItems = this.getWeekItems();
            if(this.length === "month"){
                extraClass = "timesheet-month";
                arrayItems = this.monthItems();
            }else if(this.length === "year"){
                extraClass = "timesheet-year";
                arrayItems = this.yearItems();
            }
            
            var html =  "<div class=\"line\">" +
                            "<section><div></div></section>"+ 
                        "</div>" +
                        "<div class=\"scale " + extraClass + "\">";
            for(var i =0; i< arrayItems.length; i++){
                var classSection = "";
                if(i > 27){
                    var j = i + 1;
                    classSection = "class='hidden-section section-" + j + "'";
                }
                html += "<section " + classSection + "><div>" + arrayItems[i] + "</div></section>";
            }
            html += "</div><ul class='data'></div>";
            this.element.html(html); 
        };
        
        /**
         * Return the section titles for weeks.
         * <TODO> add possibility for multiple languages </TODO>
         * @returns {Array}
         */
        this.getWeekItems = function(){
            return ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        };
        
        /**
         * Return the section titles for months.
         * <TODO> add possibility for multiple languages </TODO>
         * @returns {Array}
         */
        this.monthItems = function(){
            return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
        };
        
        /**
         * Return the section titles for years.
         * <TODO> add possibility for multiple languages </TODO>
         * @returns {Array}
         */
        this.yearItems = function(){
            return ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        };
        
        /**
         * Return the fullnames of the month.
         * @param {integer} i The index of the month.
         * <TODO> add possibility for multiple languages </TODO>
         * @returns {Array}
         */
        this.getMonthName = function(i){
            return [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ][i];
        };
        
        /**
         * Throw the data to the correct parser.
         * @param {object} response The response gotten from the site.
         * @returns {undefined}
         */
        this.parseData = function(response){
            if(this.length === "week"){
                this.handleWeeks(response);
            }else if(this.length === "month"){
                this.handleMonths(response);
            }else{
                this.handleYears(response);
            }
            this.distributeData();
        };
        
        /**
         * Sort and trhow the data in the correct rows.
         * @returns {undefined}
         */
        this.distributeData = function(){
            var that = this;
            this.sortRowsData();
            $.each(this.rowsData, function(unused,project){
                that.addToCorrectRow(project);
            });
            this.createHtml();
        };
        
        /**
         * Sort the rows on the amount of left.
         * @returns {undefined}
         */
        this.sortRowsData = function(){
            this.rowsData = this.rowsData.sort(sortByLeft);
        };
        
        /**
         * Create the html for the bubbles.
         * @returns {undefined}
         */
        this.createHtml = function (){
            this.element.find('.line section').css('left', this.lineleft + "%");
            var html = '';
            $.each(this.currentRows, function(i,v){
                var tempHtml = '<div class="timesheet-row">';
                $.each(v.items, function(step,item){
                    var left = item.left;
                    tempHtml += '<div class="timesheet-segment" style="margin-left:' + left + '%;width:' + item.width + '%">' +
                                    '<div style="background-color:' + item.data.color + '" class="bubble bubble-lorem timesheet-clicklistener" data-id="' + item.data.id + '" data-duration="6" data-name="' + item.data.name + '"></div>' +
                                '</div>';
                });
                tempHtml += '</div>';
                html += tempHtml;
            });
            this.element.find('.data').empty().append(html);
        };
        
        /**
         * Add the projects to an row, if there is space.
         * @param {object} project The project to be added, contains left, width, name and id at least.
         * @returns {Boolean}
         */
        this.addToCorrectRow = function(project){
            var that = this;
            var found = false;
            var totalWidth = project.width + project.left + that.between;
            $.each(this.currentRows, function(i,row){
                var newLeft,newRight;
                if((row.right > project.left && row.left < totalWidth) || row.right + project.width > 100 ){
                    return true;
                }
                if(row.left >= totalWidth){
                    newLeft = project.left;
                    newRight = row.right;
                }else{
                    newLeft = project.left - row.right;
                    newRight = row.right + newLeft + project.width + that.between;
                }
                that.currentRows[i]['items'].push({
                    left : project.left,
                    width: project.width,
                    data : project
                });
                that.currentRows[i]['right'] = newRight;
                if(that.currentRows[i]['left'] === 0 || row.left >= totalWidth){
                    that.currentRows[i]['left'] = newLeft;
                }
                found = true;
                return false;
            });
            return found;
        };
        
        /**
         * Handle the response of the server for weeks.
         * @param {object} response The response gotten from the server
         * @return {undefined}
         * Information: 
         *      1000 = ms to s,
         *      60 = from s to min
         */
        this.handleWeeks = function(response){
            var that = this;
            /** width 100% / days / hours / minutes **/
            var widthperMinute = 100 / 7 / 24 / 60;
            var firstDay = new Date(response.start_day);
            firstDay.setHours(0,0,0,0);
            var now = new Date();
            this.lineleft =  Math.ceil(((now - firstDay) / (1000*60)) * widthperMinute * 10 ) / 10;
            if(this.title !== "" && $(this.title).length > 0){
                $(this.title).text(firstDay.getWeekNumber());
            }
            //foreach project a row
            var counter = 0;
            $.each(response.data, function(i,v){
                $.each(v.date, function(index, value){
                    $.each(value, function(indexlessdeep, valuedeep){
                        var start = new Date(valuedeep.starttime);
                        var end = new Date(valuedeep.endtime);
                        var width = ((end - firstDay)-(start - firstDay)) / (1000*60) * widthperMinute;
                        that.rowsData.push({
                            id   : valuedeep.id,
                            name : v.project_name,
                            color: that.colors[counter],
                            left : (start - firstDay) / (1000*60) * widthperMinute,
                            width   : width > that.minwidth ? width : that.minwidth});
                    });
                });
                ++counter;
            });
        };
        
        /**
         * Handle the response of the server for months.
         * @param {object} response The response gotten from the server
         * @return {undefined}
         * Information: 
         *      1000 = ms to s,
         *      60 = from s to min,
         *      60 = from min to hours
         */
        this.handleMonths = function(response){
            var that = this;
            var firstDay = new Date(response.start_day);
            firstDay.setHours(0,0,0,0);
            var daysTotal = this.getDaysInMonth(firstDay.getFullYear(), firstDay.getMonth());
            var widthperHour = parseInt(100) / daysTotal / 24;
            var now = new Date();
            this.lineleft = Math.ceil(((now - firstDay) / (1000*60*60)) * widthperHour * 10 ) / 10;
            this.element.find('.scale').addClass('number-' + daysTotal);
            if(this.title !== "" && $(this.title).length > 0){
                $(this.title).text(this.getMonthName(firstDay.getMonth()));
            }
            var counter = 0;
            $.each(response.data, function(i,v){
                $.each(v.date, function(index, value){
                    $.each(value, function(indexlessdeep, valuedeep){
                        var start = new Date(valuedeep.starttime);
                        var end = new Date(valuedeep.endtime);
                        var width = ((end - firstDay)-(start - firstDay)) / (1000*60*60) * widthperHour;
                        that.rowsData.push({
                            id   : valuedeep.id,
                            name : v.project_name,
                            color: that.colors[counter],
                            left    : (start - firstDay) / (1000*60*60) * widthperHour,
                            width   : width > that.minwidth ? width : that.minwidth
                        });
                    });
                });
                ++counter;
            });
        };
        
        /**
         * Handle the response of the server for years.
         * @param {object} respons The response gotten from the server
         * @return {undefined}
         * Information: 
         *      1000 = ms to s,
         *      60 = from s to min,
         *      60 = from min to hours
         *      24 = from hours to days
         */
        this.handleYears = function(respons){
            var that = this;
            var realNow = new Date();
            var now = new Date(respons.year);
            var days = this.isLeapYear(now.getFullYear()) ? 366 : 365;
            var widthperDay = parseInt(100) / days;
            var firstDay = new Date(now.getFullYear() + '-01-01');
            firstDay.setHours(0,0,0,0);
            this.lineleft =  ((realNow - firstDay) / (1000*60*60*24)) * widthperDay;
            if(this.title !== "" && $(this.title).length > 0){
                $(this.title).text(now.getFullYear());
            }
            var counter = 0;
            $.each(respons.data, function(i,v){
                var project = {
                    id   : v.id,
                    name : v.name,
                    color: that.colors[counter],
                    left : 0,
                    width : 0
                };
                var deadline;
                if(typeof v.deadline === "undefined" || v.deadline === null || v.deadline === ""){
                    deadline = new Date(v.created);
                }else{
                    deadline = new Date(v.deadline);
                }
                var created = new Date(v.created);
                created.setHours(0,0,0,0);
                var left = 0;
                var widthBubble;
                if(created.getFullYear() !== now.getFullYear()){
                    widthBubble = (deadline - firstDay) / (1000*60*60*24) * widthperDay;
                }else{
                    widthBubble = (deadline - created) / (1000*60*60*24) * widthperDay;
                    left = (created - firstDay) / (1000*60*60*24) * widthperDay;
                }
                project['left'] = left;
                project['width'] = widthBubble;
                that.rowsData.push(project);
                ++counter;
            });
            console.log(that.rowsData);
        };
        
        /*
         * check whether year == leapyear
         * @param {number} year The year to be checked if it is a leapyear
         * @return {boolean}
         */
        this.isLeapYear = function(year){
            return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
        };
        
        /**
         * Get the amount of days in a month, STARTS AT 0!.
         * @param {number} year     The year of the month, to be able to determine if leapyear.
         * @param {number} month    The month number - 1 (0 based array, so starts at 0);
         * @returns {number}
         */
        this.getDaysInMonth = function(year, month){
            return [31, (this.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
        };
        
        /**
         * Handle the errors
         * @param {string|object} error The error to be displayed.
         * @return {undefined}
         */
        this.printError = function(error) {
            if(typeof error === "string"){
                console.warn(error);
            }else{
                console.warn(error.name, error.message);
            }
        };
        
        /**
        * Set the options.
        * @param {Object} object The object with the default values
        * @returns {undefined}
        */
        this.setOptions = function(object){
            if(typeof object['hover'] !== "undefined" && object['hover'].value && typeof object['hover'].value === "function"){
                this.hover = object['hover'].value;
            }
            if(typeof object['click'] !== "undefined" && object['click'].value && typeof object['click'].value === "function"){
                this.click = object['click'].value;
            }
            if(typeof object['length'] !== "undefined" && object['length'].value && typeof object['length'].value === "string" && acceptedLength.indexOf(object['length'].value)){
                this.length = object['length'].value;
            }
            var settings = ["width", "height", "rows","title", "colors", "between", "class", "yearDefaultWidth"];
            var that = this;
            $.each(settings, function(i,v){
                if(typeof object[v] === "object" && object[v].value && typeof object[v].value === object[v].requires){
                    var value;
                    if(object[v].value === "true"){
                        value = true;
                    }else if(object[v].value === "false"){
                        value = false;
                    }else{
                        value = object[v].value;
                    }
                    that[v] = value;
                }else if(typeof object[v] === "object" && object[v].value && typeof object[v].value === "object" && object[v].requires === "array"){
                    that[v] = object[v].value;
                }
            });
        };
        
        /**
         * Set the element if possible.
         */
        if(typeof element === "object" && typeof element.data === "function"){
            this.element = element;
        }else if(typeof element === "string" && $(element).length === 1){
            this.element = $(element);
        }
        
        /**
         * Set the options.
         */
        if(typeof options === "object"){
            this.setOptions(options);
        }
        
        this.init();
        
    };
    
    /**
     * Handle the objects and get the data to information. Also add the timeline as data attribute to the domelement.
     * @param {object} defaults The defaults to be used.
     * @returns {undefined}
     */
    function plugin(defaults){
        if(typeof this !== "undefined" && this !== window){
            defaults = this;
        }
        if($(defaults).length > 0){
            $(defaults).each(function(){
                var $this = $(this); 
                var dataAttributes = $this.data();
                var config = Object.assign({}, ALLOWED_DATA);
                $.each(dataAttributes, function(i,v){
                    if(typeof config[i] !== "undefined" && typeof v === config[i].requires){
                        config[i].value = v;
                    }else if(typeof config[i] !== "undefined" && typeof v === "string" && typeof window[v] === "function"){
                        config[i].value = window[v];
                    }else if(typeof config[i] !== "undefined" && config[i].requires === "array" && typeof v === "string"){
                        config[i].value = v.indexOf(',') !== -1 ? v.split(",") : v.split(" ");
                    }else if(typeof config[i] !== "undefined" && config[i].requires === "number" && typeof v === "number" && $.isNumeric(v)){
                        config[i].value = parseInt(v);
                    }
                });
                $this.data('sgTimeline', (new sgTimeline($this, config)));
            });
        }
    }

    /**
     * register sgTimeline as an jQuery function.
     */
    $.fn.sgTimeline = plugin;

    /*
     * add onhover effects on the bubbles
     */
    $('.container-fluid').on('mouseover', '.bubble.bubble-lorem', function(){
        if(typeof $(this).data('name') !== "undefined" && $("#timesheet-tooltip").length > 0){
            var offset = $(this).offset();
            $("#timesheet-tooltip").find('.timesheet-tooltip-content').text($(this).data('name'));
            var left = offset.left - (($("#timesheet-tooltip .timesheet-tooltip-content").width() - $(this).width()) / 2);
            var top = offset.top - $("#timesheet-tooltip").outerHeight() - 8;
            $("#timesheet-tooltip").css({left: left, top: top});
            $("#timesheet-tooltip").css({'opacity': 1, "z-index" : 1});
        }
    });
    $('.container-fluid').on('mouseout', '.bubble.bubble-lorem', function(){
        if($("#timesheet-tooltip").length > 0){
            $("#timesheet-tooltip").css({'opacity': 0, "z-index" : -1});
        }
    });
    
    /**
     * Add the timelines to the default objects.
     */
    $(document).ready(function(){
        $(DEFAULT_SGTIMELINE).sgTimeline();
    });
  
}(jQuery, window, document));