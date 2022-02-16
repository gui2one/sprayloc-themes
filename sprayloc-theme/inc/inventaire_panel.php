
<script type="text/javascript">


function fetchData(){
    let update_output = document.querySelector("#update-thumbnails-output");
    if( update_output){
        update_output.style.opacity = 1.0;
    }
    fetch("wp-content/themes/sprayloc-theme/inc/create_thumbnails.php")
        .then( function(response){
            return response.text();
        })
        .then(function(result){
            console.log(result);
            console.log("thumbnails updated");
            if( update_output){
                // update_output.style.opacity = 1.0;
                update_output.style.color = "green";
                update_output.innerHTML = "Done";
            }
        })
}

window.addEventListener("DOMContentLoaded", function(){
    let btn2 = document.querySelector("#btn_2");
    console.log(btn2)
    if( btn2){
        btn2.addEventListener("click", function(event){
            fetchData();
            console.log("working much ?");
        })
    }
})
</script>

<div id="spray-admin-front-end-panel">
    <h5>Update thumbnails</h5>
    <!-- <button id="create_btn">create</button> -->
    <button id="btn_2">Update</button>
    <div id="update-thumbnails-output" style="opacity : 0; font-weight : bold;">Working</div>
</div>

