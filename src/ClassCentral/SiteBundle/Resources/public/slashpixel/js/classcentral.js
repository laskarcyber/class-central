jQuery(function($) {

    $.ajaxSetup ({
        cache: false
    });
    // Handle calls to add/remove courses to users library
    $('input[id="course-list-checkbox"]').change(function(){
        var clicked = this;
        // Check if the user is logged in
        $.ajax({
            url: "/ajax/isLoggedIn",
            cache: true
        })
        .done(function(result){
            var loggedInResult = $.parseJSON(result);
            if(loggedInResult.loggedIn) {
                addRemoveCourse($(clicked).val(), $(clicked).data('course-id'),$(clicked).is(':checked'));
            } else {
                // redirect to signup page
                window.location.replace("/signup/cc/" +$(clicked).data('course-id')+ "/"+ $(clicked).val());
            }
        });
    });

    function addRemoveCourse(listId, courseId, checked) {

        if(checked){
            $.ajax( "/ajax/user/course/add?c_id=" +courseId +"&l_id="+ listId)
                .done(
                function(result){
                    //console.log("jquery" + result);
                }
            );
        } else {
            $.ajax( "/ajax/user/course/remove?c_id=" +courseId +"&l_id="+ listId)
                .done(
                function(result){
                    //console.log("jquery" + result);
                }
            );
        }
    }

    // Select dropdown on course pages
    $('#sessionOptions').change(function() {
        var selected = $(this).find('option:selected');
        var url = selected.data("url");
        var sessionType = selected.data('sessiontype');
        var sessionStatus = selected.data('sessionstatus');
        var sessionStartDate = selected.data('sessionstartdate');

        // Update the user of the href tag
        $('#btnProviderCoursePage').attr("href",url);

        // Update the add to calendar button
        $('._start').html(sessionStartDate);
        $('._end').html(sessionStartDate);
        if(sessionType=='upcoming' && sessionStatus == '1') {
            $('.btnAddToCalendar').show();
        }
        else {
            $('.btnAddToCalendar').hide();
        }

    });

});