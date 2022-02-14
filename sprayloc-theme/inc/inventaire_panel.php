
<script type="text/javascript">

function fetchData(){

    fetch("api_test/create_thumbnails.php")
        .then( function(response){
            return response.text();
        })
        .then(function(result){
            console.log("result", result);
        })
}

window.addEventListener("DOMContentLoaded", function(){
    let btn2 = document.querySelector("#btn_2");
    console.log(btn2)
    if( btn2){
        btn2.addEventListener("click", function(event){
            fetchData();
        })
    }
})
</script>

<div id="spray-admin-panel">

    <button id="create_btn">create</button>
    <button id="btn_2">press me !!!!</button>
</div>

