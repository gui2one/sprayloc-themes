/// not used anymore !!! I think

window.addEventListener("DOMContentLoaded", function () {

    let interval = 0;
    function read_progress(){
        console.log("interval !!!!");
        fetch(location.origin + '/wp-content/themes/sprayloc-theme/inc/temp_progress.txt')
        .then( function(response){
            console.log(reponse);
        })
    }
    
    (function ($) {
        
        $('#create_btn').click(function (event) {
            event.preventDefault();
            console.log("hey mother fucker ?!");
            setInterval(read_progress, 1000);

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: sprayloc_globals.ajax_url,
                data: {
                    action: 'sprayloc_thumbnails_update',
                    _ajax_nonce: sprayloc_globals.nonce
                },
                progress:function(data){
                    console.log(data);
                },
                success: function (response) {
                    clearInterval(interval);
                    if ('success' == response.type) {
                        $.ajax({
                            type: 'get',
                            url: (location.origin + '/wp-content/themes/sprayloc-theme/inc/create_thumbnails.php'),
                            success: function (response2) {
                                console.log(response2)
                                // console.log(document.domain)
                                // console.log(location)
                            }
                        })

                        $(".jsforwp-total-likes").html(0);
                        console.log("ahhh !!")
                    }
                    else {
                        console.error('Something went wrong!');
                    }
                }
            })
            console.log("AJAX")
        });

    })(jQuery);
})



