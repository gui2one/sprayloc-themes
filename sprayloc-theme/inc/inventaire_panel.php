
<script type="text/javascript">

function reloadThumbnails(){

    let data = new FormData();
    data.append("site_url", "<?php echo get_site_url(); ?>"); 
    fetch("<?php echo get_template_directory_uri(); ?>/inc/test.php",
     {
         method:"POST", 
         body : data
    })
    .then(function(response){
        return response.text()
    })
    .then(function(result){
        console.log(result)
    })


}

</script>

<div id="spray-admin-panel">
    <a onclick="reloadThumbnails();">reload thumbnails</a>

    <button id="create_btn">create</button>
</div>

