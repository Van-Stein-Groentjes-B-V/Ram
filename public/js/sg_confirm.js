var sg_confirmClass = null;
/**
 * Call this as confirm.
 * @param {string|function}   callFunction    The function to call on confirm.
 * @param {string}            text            The text that will be shown in the box.
 * @param {object}            variables       The variables to send with it.
 * @returns {undefined}
 */
function sg_confirm(callFunction, text, variables){
    if(sg_confirmClass){
        sg_confirmClass.confirm(callFunction, text, variables);
    }else{
        //fallback to simple method
        if(confirm(text)){
            if(typeof callFunction === "function"){
                callFunction(variables);
            }else if(typeof callFunction === "string" && typeof window[callFunction] === "function"){
                window[callFunction](variables);
            }
        }
    }
}
(function ($) {
    $("document").ready(function(){
        /**
         * Fill confirmClass with the class.
         */
        sg_confirmClass = new (function(){
            this.element = null;
            this.functionToCall = null;
            this.timeout = null;
            this.variablesSuccess = [];
            
            /**
             * Create the modal if element does not exist  yet.
             * @returns {undefined}
             */
            this.init = function(){
                if(!this.element){
                    this.createModal();
                }
            };

            /**
             * Create the html and append it to the body.
             * @returns {undefined}
             */
            this.createModal = function(){
                var html =  "<div id='sg_confirm_modal'>" +
                                "<div class='sg_confirm_background'></div>" +
                                "<div class='sg_confirm_modal'>" + 
                                    "<div class='sg_confirm_tekst'></div>" +
                                    "<div class='sg_confirm_button_row'>" +
                                        "<button class='sg_confirm_cancel'>" + "Cancel" + "</button>" +
                                        "<button class='sg_confirm_accept'>" + "Yes" + "</button>" +
                                    "</div>" +
                                "</div>" +
                            "</div>";
                $('body').append(html);
                var that = this;
                setTimeout(function(){
                    that.setListener();
                },100);
            };
            
            /**
             * Set the listeners on the buttons/background.
             * @returns {undefined}
             */
            this.setListener = function(){
                $('#sg_confirm_modal .sg_confirm_cancel, #sg_confirm_modal .sg_confirm_background').on("click", this.closeModal);
                $('#sg_confirm_modal .sg_confirm_accept').on("click", {that: this}, this.triggerSuccess);
            };

            /**
             * Close the modal animation.
             * @returns {undefined}
             */
            this.closeModal = function(){
                $("#sg_confirm_modal").addClass("closing");
                clearTimeout(this.timeout);
                this.timeout = setTimeout(function(){
                    $("#sg_confirm_modal").removeClass("closing").removeClass("open").removeClass("show");
                },300);
            };
            /**
             * Open the modal animation.
             * @returns {undefined}
             */
            this.openModal = function(){
                $("#sg_confirm_modal").addClass("show");
                setTimeout(function(){
                    $("#sg_confirm_modal").addClass("opening");
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(function(){
                        $("#sg_confirm_modal").addClass("open").removeClass("opening");
                    },300);
                },70);
            };

            /**
             * Trigger the function given.
             * @param {object|undefined}  e   The event data, if called through event.
             * @returns {undefined}
             */
            this.triggerSuccess = function(e){
                var that;
                if(e && e.data && e.data.that){
                    that = e.data.that;
                }else{
                    that = this;
                }
                if(that.functionToCall){
                    if(typeof that.functionToCall === "function"){
                        that.functionToCall(that.variablesSuccess);
                    }else if(typeof that.functionToCall === "string" && typeof window[that.functionToCall] === "function"){
                        window[that.functionToCall](that.variablesSuccess);
                    }
                }
                that.closeModal();
            };

            /**
             * Call this to trigger the class flow.
             * @param {string|function}     functionToBeCalled  The function to be executed on success.
             * @param {string}              textToBeFilled      The string in the confirmation box.
             * @returns {undefined}
             */
            this.confirm = function(functionToBeCalled, textToBeFilled, variables){
                $("#sg_confirm_modal .sg_confirm_tekst").text(textToBeFilled);
                this.functionToCall = functionToBeCalled;
                this.variablesSuccess = variables;
                this.openModal();
            };

            this.init();
        })();
    });
}(jQuery));