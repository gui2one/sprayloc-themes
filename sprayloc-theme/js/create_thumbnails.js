window.addEventListener("DOMContentLoaded", function () {



    (function ($) {

        $('#create_btn').click(function (event) {
            event.preventDefault();

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: sprayloc_globals.ajax_url,
                data: {
                    action: 'sprayloc_thumbnails_update',
                    _ajax_nonce: sprayloc_globals.nonce
                },
                success: function (response) {
                    if ('success' == response.type) {
                        $.ajax({
                            type: 'get',
                            url: (location.origin + '/api_test/create_thumbnails.php'),
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



