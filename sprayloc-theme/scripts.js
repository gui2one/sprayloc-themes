const PageTop = function () {
    let page_top_btn = document.querySelector('.btn-page-top');

    window.addEventListener("scroll", function () {
        let scrollY = window.scrollY;
        if (scrollY > 300) {

            page_top_btn.classList.add("show");

        } else {
            page_top_btn.classList.remove("show");
        }
    })

    page_top_btn.addEventListener('click', function () {

        // page_top_btn.classList.remove("show");
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        })
    })


    if (window.scrollY > 300) {
        page_top_btn.classList.add("show");
    }

}

const LightBox = function () {
    lightbox.option({
        'resizeDuration': 150,
        'wrapAround': true
    })

    let nodes = document.querySelectorAll(".image-zoom");

    for (let node of nodes) {
        let fig = node.querySelector("figure")
        fig.style.position = "relative";
        let img = node.querySelector("figure>img")
        img.style.position = "relative";
        img.style.display = "block"

        let link = document.createElement("a");
        link.dataset.lightbox = "zeaze"
        link.style.display = "block";
        link.style.position = "absolute";
        link.style.width = "100%";
        link.style.height = "100%";
        link.style.left = "0px";
        link.style.top = "0px";
        link.style.contentVisibility = "hidden";
        // link.style.background = "yellow";
        link.href = img.src;
        link.innerHTML = "link"
        // console.log(img);
        img.parentElement.appendChild(link);

    }
}

const init_sprayloc_menu = function () {
    let toggler = document.querySelector(".navbar-toggler");
    let sidebar = document.querySelector("#sidebar-nav");
    let sprayloc_container = document.querySelector("#sprayloc-container");
    toggler.addEventListener("click", function () {
        sidebar.classList.toggle("hide");
    });

    sprayloc_container.addEventListener('click', function (e) {
        // console.log(e)
        // e.bubbles = true;
        sidebar.classList.add("hide");
    })
}

function doLogo() {
    let drops = document.querySelector(".inner-container");
    let drop_1 = document.getElementById("drop_1");
    // console.log(drops);
    let timeline_1 = new gsap.timeline({ paused: true });
    timeline_1.to("#drop_1", {
        duration: .20,
        transform: " rotate(20deg) translate3d(-10px,20px,0px) scale(0.6)",
        transformOrigin: "100% 75%"
    }).
        to("#drop_1", {
            duration: .33,
            ease: "back",
            delay: .05,
            transform: " rotate(0deg) translate3d(0px,0px, 0px)",
            // transformOrigin : "100% 75%"
        });
    let timeline_2 = new gsap.timeline({ paused: true });
    timeline_2.to("#drop_2", {
        duration: .20,
        transform: " rotate(-30deg) translate(-20px,-50px) scale(0.6)",
        transformOrigin: "100% 75%"
    }).
        to("#drop_2", {
            duration: .33,
            ease: "back",
            delay: .05,
            transform: " rotate(0deg) translate(0px,0) "
        });

    let timeline_3 = new gsap.timeline({ paused: true });
    timeline_3.to("#drop_3", {
        duration: .20,
        transform: "  rotate(-30deg) translate(30px,20px) scale(0.5)",
        transformOrigin: "100% 75%"
    })
        .to("#drop_3", {
            duration: .33,
            ease: "back",
            delay: .05,
            transform: "  rotate(0deg)  translate(0px,0)"
        });

    let mouse_over_logo = false;

    function onMouseEnter(e) {
        // e.preventDefault();
        mouse_over_logo = true;
        if (!timeline_1.isActive() && mouse_over_logo) {
            timeline_1.restart();
            timeline_2.restart();
            timeline_3.restart();
        }
        timeline_1.play();
        timeline_2.play();
        timeline_3.play();
    }

    drops.addEventListener("mouseenter", onMouseEnter);
    drops.addEventListener("touchstart", onMouseEnter);
    // drops.addEventListener("mouseout", onMouseOut)
}


function bs_dropdown_hover() {

    document.querySelectorAll('.nav-item').forEach(function (everyitem) {

        everyitem.addEventListener('mouseover', function (e) {

            let el_link = this.querySelector('a[data-toggle]');

            if (el_link != null) {
                let nextEl = el_link.nextElementSibling;
                el_link.classList.add('show');
                nextEl.classList.add('show');
            }

        });
        // everyitem.addEventListener('mouseleave', function (e) {
        //     let el_link = this.querySelector('a[data-toggle]');

        //     if (el_link != null) {
        //         let nextEl = el_link.nextElementSibling;
        //         el_link.classList.remove('show');
        //         nextEl.classList.remove('show');
        //     }


        // })
    });

    document.querySelectorAll('.dropdown-menu').forEach(function (everyitem) {


        everyitem.addEventListener('mouseleave', function (e) {

            e.target.classList.remove('show');
            // nextEl.classList.remove('show');



        })
    });


}

jQuery(document).ready(function () {
    // jQuery('[data-toggle="tooltip"]').tooltip();
    // jQuery('.dropdown-toggle').forEach(()=>{
    //     $(this).dropdown();
    // })

    // console.log("JQUERY init");
});
document.addEventListener("DOMContentLoaded", function () {


    // $('.dropdown-toggle').dropdown()
    PageTop();
    ////// lightbox options
    LightBox();
    init_sprayloc_menu();

    doLogo();

    bs_dropdown_hover();

})