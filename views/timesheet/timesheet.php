<?php
?>
<div class="col-md-12">
    <div id="timeline"></div>
</div>

<script>
    $('document').ready(function(){
        var settings = {
            "script_path" : base_url + "public/locale/",
            start_at_end: true,
            scale_factor:1,
            initial_zoom:0
        };
        window.timeline = new TL.Timeline('timeline', base_url + 'api/getTimesheetWeek/', settings);   
        awaitIt(0);   
    });
    
    function awaitIt(i){
        if(!window.timeline.ready){
            i += 1000;
            return setTimeout(function(){
                awaitIt(i);
            },1000);
        }
        $('#timeline>.tl-menubar').removeAttr('style');
        $('.tl-slidenav-next').removeAttr('style');
        $('.tl-slidenav-previous').removeAttr('style');
    }
    
</script>
<style>
    .tl-timeline{
        min-height:100%;
    }
    .tl-storyslider{
        max-height:900px;
    }
    .tl-menubar{
        top: calc(100% - 140px);
    }
    .tl-storyslider .tl-slider-container-mask .tl-slider-container{
        min-height:600px;
    }
</style>