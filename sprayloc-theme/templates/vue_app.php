<?php
/*
Template Name: vue_app_template
*/



get_header();
$current_user = wp_get_current_user();
if (user_can($current_user, 'administrator')) {
    // user is an admin
    require_once(get_template_directory()."/inc/inventaire_panel.php");
}

// if (is_user_logged_in()) {
//     if (is_admin()) {

//     }
// }
?>

<div id="app-inventaire">

    <folders-bar :categories="new_categories" v-if="data_loaded"></folders-bar>
    <div id="search-bar">
        <div id="search-infos" v-html="infos_message"></div>
        <div id="search">
            <i class="fa fa-search"></i>
            <input type="search" name="search-input" v-model="string_filter" id="search-input" placeholder="Rechercher">
        </div>
    </div>
    <spinner v-if="!data_loaded"></spinner>
    <div v-else>

        <div v-if="filtered.length == 0" class="no-equipment">
            Aucun équipement dans cette catégorie.
            <br> <a href="?category=all">Voir tous les équipements</a>
        </div>
        <div class="cards-container">
            <sprayloc-card v-for="item in filtered" v-bind:data="item" v-bind:image="getImageThumbnail(item.image) ? getImageThumbnail(item.image) : getImageThumbnail(item.images[0])"
                @show-details="showDetails" v-bind:folder="getFolderName(item.folder)" :key="item.id"></sprayloc-card>
        </div>
        <detail-vue v-if="data_loaded" :item="getEquipmentByID(id_selected)" @hide-details="hideDetails" />
    </div >

</div >

    <script>
        const routes = [{
            path: "/vue-app"
}]
        const router = new VueRouter({
            mode: "history",
        routes,
        base: location.pathname
})

        const Spinner = Vue.component("spinner", {

            template: `
        <div id="spinner-container">

        </div>
        `,
        mounted() {
            let vm = this;
        document.addEventListener("DOMContentLoaded", function() {
            vm.draw_spinner();
        })
    },
        methods: {

            draw_spinner() {
            function Clock() {
                this.startTime = Date.now();
                this.currentTime = this.startTime;
                this.oldTime = this.startTime;

                this.deltaTime = 0;
                this.started = false;
            }

            Clock.prototype.start = function() {
            this.started = true;
            };

        Clock.prototype.update = function() {
            this.oldTime = this.currentTime;
        this.currentTime = Date.now();
        this.deltaTime = (this.currentTime - this.oldTime) / 1000;
            };

        let angle = Math.PI;
        let angle_rotation = 0;
        let container = document.getElementById("spinner-container");
        if (container) {
                const footer = document.getElementById("footer");
        footer_height = footer.getBoundingClientRect().height;

        container.style.height = "calc(90vh - " + (container.offsetTop + footer_height) + "px)";

        let clock = new Clock();

        let spinner = document.createElement("div");
        spinner.id = "spinner";
        container.appendChild(spinner);
        let canvas = document.createElement("canvas");
        spinner.appendChild(canvas);

        const ctx = canvas.getContext("2d");

        function draw(_angle, _rotation) {
            ctx.clearRect(0, 0, 80, 80);

        ctx.beginPath();
        ctx.ellipse(40, 40, 30, 30, 0, 0, Math.PI * 2, false);
        ctx.fillStyle = "#343a4033";
        ctx.fill();
        ctx.beginPath();
        ctx.ellipse(40, 40, 20, 20, _rotation, 0, _angle, false);
        // ctx.closePath();
        ctx.lineWidth = 7;
        ctx.strokeStyle = "white";
        ctx.stroke();
                }

        function animate() {
            clock.update();
        angle += clock.deltaTime * 2.0;
        angle_rotation += clock.deltaTime * 8.0;

        draw(((Math.cos(angle) + 1) / 2.0) * Math.PI * 2.0, angle_rotation);
        requestAnimationFrame(animate);
                }
        animate();
            }
        },
    }
})
        const FoldersBar = Vue.component("folders-bar", {
            props: ['categories'],
        data: function() {
        return {

        }
    },
        created() {
            let vm = this;
        document.body.addEventListener("click", function() {
            vm.hideAllSubfolders()

        })
    },
        template: `
        <div id="folders-bar">
            <div class="wrapper">
                <div  @click="setCategory('all', $event)" @mouseover="onMouseOver()" class="category" :class="currentCategory==='all'?'active':''">
                <a> Tout </a>
            </div>
            <div v-for="cat in categories" :key="cat.displayname" class="category" :class="currentCategory===cat.displayname?'active':''">
            <a @click="setCategory(cat.displayname, $event)"  @mouseover="onMouseOver(cat)" > {{ cat.displayname }} </a>
        <div :id="'subfolder-'+cat.displayname" class="subfolders-container">
        <div v-for="subcat in cat.subfolders" :key="subcat.displayname" class="subcategory" :class="currentCategory===subcat.displayname?'active':''">
        <a @click="setCategory(subcat.displayname, $event)" >{{ subcat.displayname }}</a>
                    </div >
                  </div >
                </div >
              </div >
            </div >
    `,
    methods: {
        setCategory: function(category_name, event) {

            event.preventDefault();
            event.stopPropagation();

            if (category_name === "all") {

                this.$router.push({
                    path: "/"
                })


            } else {

                this.$router.push({
                    path: "/",
                    query: {
                        category: category_name
                    }
                })

            }


        },
        hideAllSubfolders() {
            let subs = document.querySelectorAll('.subfolders-container');
            for (let sub of subs) {
                sub.classList.remove("show");
            }
        },
        onMouseOver(item) {
            this.hideAllSubfolders();
            if (item) {

                let subfolder_div = document.getElementById('subfolder-' + item.displayname);

                subfolder_div.classList.add("show")
            }
        },
    },
    computed: {
        currentCategory: function() {
            return this.$route.query.category;
        }
    }
});

const ImageSlider = Vue.component("image-slider", {
    props: ["images"],
    template: `
    <div id="image-slider" class="carousel slide" data-ride="carousel">

        <!--Indicators -->
        <ul class="carousel-indicators">
            <li v-for="(image, index) in images" data-target="#image-slider" :key="image.displayname" :data-slide-to="image.displayname" class="active"></li>
        </ul>

        <!--The slideshow-->
        <div class="carousel-inner">
            <div
                v-for="(image, index) in images" class="carousel-item" :class="index === 0 ? 'active' : ''" :style="{backgroundImage:'url('+image.url+')'}"
                    :data-custom="index" >
            <!-- <img :src="image.url" alt="image.displayname"> -->
            <a
                class="lightbox-link"
                :href="image.url"
                data-lightbox="slider-content" >
            Link
            </a>
            </div>   
        </div>

        <!--Left and right controls -->
        <a class="carousel-control-prev" href="#image-slider" data-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#image-slider" data-slide="next">
        <span class="carousel-control-next-icon"></span>
        </a>

    </div>
    `,
    mounted: function() {

    },
    methods: {

    }
});

// const VueTinySlider = require(["wp-content/themes/sprayloc-theme/js/vue-tiny-slider.js"]);
const DetailVue = Vue.component("detail-vue", {
    props: ['item'],
    components: {
        ImageSlider
    },
    template: `
    <div id = "details-container" >
        <div @click="hideDetails()"  id = "details-overlay" >  </div >
                  <div id="details-window" v-if="item">
                    <div class="header">
                        <div class="close-details-btn" @click="hideDetails">
                          <i class="fas fa-angle-left"></i> 
                        </div>
                    </div>
                    <div class="title">{{item.displayname}}</div>
                    <div class="description" v-html="item.external_remark">
                    
                    </div>
                    
                            <div v-if="item.images"  class="pictures" >
                              <image-slider v-if="item.images.length > 1" class="pictures" :images="item.images"/>
                              
                              <div class="single-image pictures" v-if="item.images.length == 1"  :style="{backgroundImage:'url('+item.images[0].url+')'}">
                              <a class="lightbox-link" data-lightbox="slider-content" :href="item.images[0].url" >link</a>
                                
                              </div >
                            </div >
    <div v-else class="pictures">
        <span class="no-picture pictures">Pas de visuel pour cet équipement.</span>
    </div>
                            
                  
                  </div >

                </div >
    `,

    created: function() {

    },
    mounted: function() {


    },
    methods: {
        hideDetails: function() {
            this.$emit("hide-details");
        },

    },
    computed: {
        needOpen: function() {
            let test = this.$route.query.item_id !== undefined

            return test;
        }
    }
});

const Card = Vue.component("sprayloc-card", {

    props: ['data', 'image', 'folder'],
    template: `
    <div class="sprayloc-card" @click="showDetails(data.id)" >
                <div class="card-image" v-bind:style="{ 'background-image': 'url(' + image + ')' }"></div>
                <div class="content">
                    <div class="title"><a @click="showDetails(data.id)">{{data.name}}</a></div>

                    <div class="category"> Dans <strong> {{folder}}</strong></div>
                    
                    <!-- 
                    <div class="external-remark block-ellipsis" v - html="data.external_remark" ></div >
    -->


                
                </div >
    <div class="card-footer" @click="showDetails(data.id)" > <a class="details-button" >+Détails</a></div >
            </div >
    `,
    methods: {
        showDetails: function(item_id) {
            console.log("ID !!! : ", item_id);
            this.$emit("show-details", item_id);
            this.$router.push({
                path: "/",
                query: {
                    ...this.$route.query,
                    item_id
                }
            })

        },

    },

});

var app = new Vue({
    router,
    el: "#app-inventaire",
    components: {
        FoldersBar,
        Card,
        DetailVue
    },
    data: function() {

        return {
            max_minutes_local_data: 10,
            string_filter: "",
            folders: undefined,
            files: [],
            equipment: [],
            kits: undefined,
            categories: [],
            new_categories: [],
            id_selected: -1,
            detail_vue_opened: false,
            data_loaded: false,
            placeholder_url: "wp-content/themes/sprayloc-theme/assets/sprayloc_logo_placeholder.jpg",
        }
    },
    created: function() {

        this.getData();
        let vm = this;


    },
    methods: {
        showDetails: function(item_id) {


            this.id_selected = parseInt(item_id)
            this.detail_vue_opened = true;

            setTimeout(() => {

                const detailVue = document.getElementById("details-window");
                const detailOverlay = document.getElementById("details-overlay");
                detailVue.classList.add("opened");
                detailOverlay.classList.add("opened");
                detailOverlay.style.pointerEvents = "unset";

                detailVue.style.pointerEvents = "unset";
                document.body.style.overflowY = "hidden";
                document.body.style.marginRight = "17px"; // scrollbar width


            }, 0);


            // document.location.assign("https://sprayloc-dev.com/vue-app?item_id="+item_id)
        },
        hideDetails: function() {

            const detailVue = document.getElementById("details-window");
            const detailOverlay = document.getElementById("details-overlay");
            detailVue.classList.remove("opened")
            detailVue.style.pointerEvents = "none";

            detailOverlay.classList.remove("opened")
            detailOverlay.style.pointerEvents = "none";

            document.body.style.overflowY = "scroll";
            document.body.style.marginRight = "0px";
            let query = {
                ...this.$route.query
            };
            if (query.item_id !== undefined) {
                delete query.item_id
            }
            this.$router.push({
                path: "/",
                query: {
                    ...query
                }
            })
            this.detail_vue_opened = false;
        },
        computeFinalData() {
            let vm = this;
            // console.log("Origin Equipment" , vm.equipment)
            // console.log("Origin Files" , vm.files)
            // console.log("Origin Folders" , vm.folders)
            // console.log("Origin Kits" , vm.kits)


            // START CATEGORIES

            for (folder of vm.folders) {
                if (folder.parent) {
                    let parent_id = parseInt(folder.parent.replace("/folders/", ""));
                    folder.parent = parent_id

                }
            }

            // build folders hierarchy
            let root_folder_id = vm.folders.filter(function(value) {
                return value.displayname.toLowerCase() == "équipement"
            })[0].id;



            // one pass to get level 1 folders
            for (let folder of vm.folders) {

                folder.subfolders = []
                if (folder.parent === root_folder_id) {
                    vm.new_categories.push(folder)
                }
            }

            // second pass to get sub folders
            for (let folder of vm.folders) {
                if (folder.parent !== root_folder_id && folder.parent !== null) {


                    let good_folder = vm.folders.filter(function(item) {
                        return item.id === folder.parent
                    })[0]

                    good_folder.subfolders.push(folder)

                }
            }


            // END CATEGORIES

            for (let item of vm.equipment) {

                vm.collectImages(item.id)
            }



            // shoDetails if query item_id not undefined
            if (vm.$route.query.item_id !== undefined) {
                vm.showDetails(vm.$route.query.item_id)
            }

            vm.data_loaded = true;
        },
        fetchData() {
            var vm = this;

            fetch("api_test/get_files_v4.php")
                .then(function(response) {
                    return response.json();
                })
                .then(function(result) {

                    let localData = {
                        time: Date.now(),
                        data: result
                    };

                    sessionStorage.sprayloc_data = JSON.stringify(localData);

                    console.log("LOADED DATA INTO SEESION STORAGE");

                    vm.folders = result.folders;
                    vm.files = result.files;
                    vm.equipment = result.equipment;
                    vm.kits = result.kits;

                    vm.data_loaded = true;

                    vm.computeFinalData();
                });
        },
        getData() {


            if (sessionStorage.sprayloc_data === undefined) {

                this.fetchData();
            } else if (sessionStorage.sprayloc_data !== undefined) {
                let local_data = JSON.parse(sessionStorage.sprayloc_data);

                let now = Date.now();
                let data_age = (now - local_data.time) / 1000;

                if (data_age > this.max_minutes_local_data * 60) { //10 minutes

                    this.fetchData();


                } else {


                    this.equipment = local_data.data.equipment
                    this.files = local_data.data.files
                    this.folders = local_data.data.folders
                    this.kits = local_data.data.kits


                    ///// IMPORTANT !!
                    this.data_loaded = true;
                    ////       

                    this.computeFinalData();

                }
            }

            for (let item of this.equipment) {

                this.collectImages(item.id)
            }
        },
        getImageFile: function(file_id) {
            // if (file_id === null) {
            //     return this.placeholder_url
            // }

            if (this.files.length > 0) {

                // console.log("file_id : " , file_id);

                // parse file_id
                _file_id = parseInt(file_id.replace("/files/", ""))
                let filtered = this.files.filter(function(value) {
                    return value.id === _file_id;
                })[0]


                if (filtered)
                    return filtered.url
                else
                    return this.placeholder_url + "sdfsdf"

            }
        },
        LinkCheck: function(url) {
            var http = new XMLHttpRequest();
            http.open('HEAD', url, false);
            http.send();
            return http.status != 404;
        },
        getImageThumbnail: function(file_id) {
            if (file_id === null) {

                // return this.placeholder_url
                return this.placeholder_url
            }
            if (this.files.length > 0) {

                // parse file_id
                console.log("ID !!!!!! : ", file_id);
                _file_id = parseInt(file_id.replace("/files/", ""))
                let filtered = this.files.filter(function(value) {
                    return value.id === _file_id;
                })[0]

                if (filtered) {
                    let src = filtered.displayname;


                    let full_name = src.substring(src.lastIndexOf("/") + 1);
                    console.log("name ----------------------", src);
                    let ext = full_name.substring(full_name.lastIndexOf("."));
                    let name = full_name.substring(0, full_name.lastIndexOf("."));


                    /*
                        TODO :
                        Sanitize filenames !!!!
                        Do it in relation to the thumbnail creation php script
                    */
                    let thumb_name = name.replace("(", "_").replace(")", "_") + "_thumbnail";
                    let link = "api_test/gallery/" + encodeURIComponent(thumb_name) + ext


                    // console.log("link :", link)
                    return link;

                    let file_exists = this.LinkCheck(link);
                    if (file_exists === true) {
                        // console.log("File found ? ", link)

                        return link;

                    } else {
                        console.log("missing ? ", link)
                        return filtered.url
                    }


                }

                // return this.placeholder_url
                return ""

            }
        },
        getFolderName: function(folder_id) {

            if (folder_id !== null && this.folders !== undefined) {


                let _folder_id = parseInt(folder_id.replace("/folders/", ""));

                let filtered = this.folders.filter(function(value) {

                    return parseInt(value.id) === parseInt(_folder_id);
                })

                if (filtered.length > 0) return filtered[0].displayname
                else return null
            } else {
                return null
            }
        },
        getFolderData: function(folder_id) {

            if (folder_id !== null && this.folders !== undefined) {


                let _folder_id = parseInt(folder_id.replace("/folders/", ""));

                let filtered = this.folders.filter(function(value) {

                    return parseInt(value.id) === parseInt(_folder_id);
                })

                if (filtered.length > 0) return filtered[0]
                else return null
            } else {
                return null
            }
        },
        getEquipmentByID: function(id) {
            if (this.equipment.length > 0 && id !== -1) {


                let ret = this.equipment.filter(function(value) {
                    return value.id === id
                })[0]

                if (ret !== undefined) return ret;
                else return this.equipment[0]
            }
        },
        collectImages(equipment_id) {
            let images = [];
            let equip = this.getEquipmentByID(equipment_id)

            if (this.files.length > 0) {
                let item_files = this.files.filter(value => {
                    return value.item === parseInt(equipment_id);
                })

                if (item_files.length > 0) {



                    equip.images = item_files;

                }

            }

            if (equip.image) {

                let default_image_url = this.getImageFile(equip.image);

            }


        },
        sanitizeName: function(name) {
            return name.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
        }

    },
    computed: {
        filtered: function() {

            if (this.data_loaded) {

                let pattern = this.string_filter;
                let words = pattern.split(" ");
                let filtered_equipments = this.equipment.filter((value) => {
                    let found = false;
                    let num = 0
                    for (let word of words) {

                        let found_index = this.sanitizeName(value.name).toLowerCase().search(this
                            .sanitizeName(word).toLowerCase())

                        if (found_index != -1) {
                            found = true;
                            num++;
                            // break;
                        }
                    }
                    return num === words.length;
                })

                // filter category
                if (this.$route.query.category !== undefined) {


                    if (this.$route.query.category === 'all') {
                        return filtered_equipments
                    }
                    const vm = this;

                    filtered_equipments = filtered_equipments.filter(function(item) {
                        let param_category = vm.$route.query.category;
                        let folder_name = vm.getFolderName(item.folder);
                        // check subcats
                        let cur_folder = vm.new_categories.filter(function(folder) {
                            return folder.displayname === param_category
                        })[0]

                        if (cur_folder) {
                            if (cur_folder.subfolders.length > 0) {

                                for (let sub of cur_folder.subfolders) {
                                    if (folder_name === sub.displayname) return true
                                }
                            }
                        }

                        return folder_name === param_category
                    })
                }

                console.log("filtered_equipments ------------------")
                console.log(filtered_equipments)
                return filtered_equipments
            }
            console.log("problem with data ------------------")
            return undefined
        },
        filteredFolders: function() {
            if (this.equipment) {

            }
        },
        infos_message: function() {
            let cat = this.$route.query.category;
            if (cat) {

                return `Recherche dans: <strong> ${cat} </strong> <br> ${this.filtered.length} équipement${this.filtered.length>1 ? 's':''}`
            } else {
                return ""
            }
        }
    }
});
</script>

<?php

get_footer();
