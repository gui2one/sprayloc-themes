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
        document.addEventListener("DOMContentLoaded", function () {
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

            Clock.prototype.start = function () {
                this.started = true;
            };

            Clock.prototype.update = function () {
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
    data: function () {
        return {

        }
    },
    created() {
        let vm = this;
        document.body.addEventListener("click", function () {
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
        setCategory: function (category_name, event) {

            event.preventDefault();
            event.stopPropagation();

            if (category_name === "all") {

                this.$router.push({
                    path: "/",
                    // query: { ...this.$route.query }
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
        currentCategory: function () {
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
                    
                  </a>
                </div>   
              </div>

              <!-- Left and right controls -->
              <a class="carousel-control-prev" href="#image-slider" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </a>
              <a class="carousel-control-next" href="#image-slider" data-slide="next">
                <span class="carousel-control-next-icon"></span>
              </a>

        <!--Left and right controls -->
        <a class="carousel-control-prev" href="#image-slider" data-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#image-slider" data-slide="next">
        <span class="carousel-control-next-icon"></span>
        </a>

    </div>
    `,
    mounted: function () {

    },
    methods: {

    }
});

// const VueTinySlider = require(["wp-content/themes/sprayloc-theme/js/vue-tiny-slider.js"]);
const DetailVue = Vue.component("detail-vue", {
    props: ['item', 'kits'],
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
                <div class="description" v-html="item.external_remark"></div>
                <div class="kit" v-if="isInAKit(item)">
                <p> <a @click="showDetails(item)">Cet equipement fait partie d'un Kit</a>
                </p>
                </div>
                <div v-if="isAKit(item)" class="kit"><p>Ce kit comprend :</p>
                    <div v-for="kit_content in kitContent(item)" :key="kit_content.id">

                     <a @click="showDetails(kit_content)">{{kit_content.displayname}}</a>
                    </div> 
                </div>

            
        <div v-if="item.images"  class="pictures" >
            <image-slider v-if="item.images.length > 1" class="pictures" :images="item.images"/>
            
            <div class="single-image pictures" v-if="item.images.length == 1"  :style="{backgroundImage:'url('+item.images[0].url+')'}">
                <a class="lightbox-link" data-lightbox="slider-content" :href="item.images[0].url" ></a>
            
            </div >
        </div >
        <div v-else class="pictures">
            <span class="no-picture pictures">Pas de visuel pour cet équipement.</span>
        </div>
        

        </div >
    </div >
    `,

    created: function () {

    },
    mounted: function () {


    },
    watch: {
        '$route'(to, from) {
            let item_id = to.query.item_id
            // console.log(item_id)
            if (item_id === undefined) {
                this.hideDetails();

            } else {
                this.id_selected = item_id;
                this.$emit("show-details", item_id)
            }
        }
    },
    methods: {
        showDetails: function (item_id) {
            // console.log(item_id)
            let id;
            try {

                id = this.getParentEquipmentID(item_id)
            } catch (error) {
                // console.log(error);
                // console.log("ID ! !!!", item_id.id);
                id = parseInt(item_id.equipment.replace("/equipment/", ""))
            }
            this.id_selected = parseInt(id);
            this.$router.push({
                path: "/",
                query: {
                    ...this.$route.query, // keeping existing params
                    item_id: id
                }
            })
            this.$emit("show-details", id);
        },
        hideDetails: function () {
            this.$emit("hide-details");
        },
        isAKit: function (item) {


            return this.kits.find(element => {
                let equipID = parseInt(element.equipment.replace("/equipment/", ""))
                let parentID = parseInt(element.parent_equipment.replace("/equipment/", ""))
                let bool = item.id === parentID;

                return bool;
            }) !== undefined


        },
        isInAKit: function (item) {


            let found = this.kits.find((element) => {
                let equipID = parseInt(element.equipment.replace("/equipment/", ""))
                let parentID = parseInt(element.parent_equipment.replace("/equipment/", ""))

                return item.id === equipID;
            })


            return found
        },
        kitContent: function (item) {


            return this.kits.filter((element, index) => {
                let equipID = parseInt(element.equipment.replace("/equipment/", ""))
                let parentID = parseInt(element.parent_equipment.replace("/equipment/", ""))

                return parentID === item.id
            })
        },
        getParentEquipmentID: function (item) {
            let found = this.kits.find((element) => {
                let equipID = parseInt(element.equipment.replace("/equipment/", ""))


                return item.id === equipID;
            })

            let parentID = parseInt(found.parent_equipment.replace("/equipment/", ""))


            if (found) return parentID
            return undefined
        },
        gotoKit: function (item) {
            let found = this.kits.find((element) => {
                let equipID = parseInt(element.equipment.replace("/equipment/", ""))


                return item.id === equipID;
            })

            let parentID = parseInt(found.parent_equipment.replace("/equipment/", ""))


            this.$router.push({
                path: "/",
                query: {

                    item_id: parentID
                }
            })
        }

    },
    computed: {
        needOpen: function () {
            let test = this.$route.query.item_id !== undefined

            return test;
        },

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
                </div >
    <div class="card-footer" @click="showDetails(data.id)" > <a class="details-button" >+Détails</a></div >
            </div >
    `,
    methods: {
        showDetails: function (item_id) {

            this.$emit("show-details", item_id);
            this.$router.push({
                path: "/",
                query: {
                    ...this.$route.query,
                    item_id
                }
            })

        }

    },

});

const ItemRow = Vue.component("sprayloc-item-row", {

    props: ['data', 'image', 'folder'],
    template: `
    <div class="sprayloc-item-row" @click="showDetails(data.id)" >
                <div class="card-image" v-bind:style="{ 'background-image': 'url(' + image + ')' }"></div>
                <div class="content">
                    <div class="title"><a @click="showDetails(data.id)">{{data.name}}</a></div>
                    <div class="category"> Dans <strong> {{folder}}</strong></div>
                </div >
    <div class="card-footer" @click="showDetails(data.id)" > <a class="details-button" >+Détails</a></div >
            </div >
    `,
    methods: {
        showDetails: function (item_id) {

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

const Pagination = Vue.component("sprayloc-pagination", {
    props: ["numcards", "filtered", "maxitems"],
    data: function () {
        return {
            max_items: undefined
        }
    },
    template: `
    <div class="" id="sprayloc-pagination" v-if="filtered.length > 0">
        <div class="wrapper">
        <!--
        <span class="num-items" >{{numcards}}</span>
        <input type="number" cols="3" style="width: 3em;" @input="onInput" v-model="max_items" />
        -->
        
            <span class="pagination-navigation" @click="onPrevPage"> << </span>
            <span id="page-numbers"> 
                <span @click="onPageChange(index)" class="page-number pagination-navigation" v-for="index in pageSize" :key="index">
                   <span v-if="index != current_page" > {{index}}</span>
                   <span v-else class="active"><strong> {{index}} </strong></span>
                </span>
            
            </span>
            <span class="pagination-navigation" @click="onNextPage"> >> </span>
        </div>
    </div>
    `,
    created: function () {
        this.max_items = this.maxitems;
    },
    methods:
    {
        onPageChange: function (page_num) {
            console.log(page_num);
            this.$router.push({
                path: "/",
                query: {
                    ...this.$route.query,
                    page_num
                }
            })
            this.$emit("page-change", page_num);
            // this.current_page = parseInt(page_num);
        },
        onPrevPage: function () {
            console.log("previous page !!!");
        },
        onNextPage: function () {
            console.log("next page !!!");
        },
        onInput: function (event) {
            // console.log(event)

            this.$emit("change-pagination-max", parseInt(event.target.value));
        }
    },
    computed: {
        max_items: function () {
            // console.log(this.pagination_max);

            // this.$emit("hello");
            return this.pagination_max
        },
        pageSize: function () {
            return Math.ceil(this.filtered.length / this.maxitems);
        },
        current_page: function () {
            console.log("current_page ", this.$route.query.page_num);
            return parseInt(this.$route.query.page_num);
        }
    },
    watch: {

    },


});

var app = new Vue({
    router,
    el: "#app-inventaire",
    components: {
        FoldersBar,
        Card,
        ItemRow,
        Pagination,
        DetailVue
    },
    data: function () {

        return {
            window_width: 0,
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
            pagination_max: 20,
            pagination_start: 0,
            pagination_end: 20,
            pagination_current_page: 1,
            placeholder_url: "wp-content/themes/sprayloc-theme/assets/sprayloc_logo_placeholder.jpg",
        }
    },
    created: function () {
        console.log("-------------- app create -------------");
        this.getData();
        let vm = this;
        window.addEventListener('resize', this.onResize);
        let resize_event = new Event("resize");
        window.dispatchEvent(resize_event);



    },
    methods: {
        onPageChange: function (page_num) {

            let num_items = this.filtered.length;
            let max_per_page = this.pagination_max;
            // console.log("num_items :", num_items);
            // console.log("max_per_page :", max_per_page);
            // console.log("page_num :", page_num);

            let first_index = max_per_page * (page_num - 1);

            let last_index = first_index + max_per_page;
            if (last_index > num_items) {
                // console.log("too high !!");
                last_index -= last_index - num_items;
            }
            // console.log("first_index :", first_index);
            // console.log("last_index :", last_index);

            this.pagination_start = first_index;
            this.pagination_end = last_index;

            this.pagination_current_page = page_num
            // this.$router.push({
            //     path: "/",
            //     query: {
            //         ...this.$route.query,
            //         page_num
            //     }
            // })
        },
        onChangePaginationMax: function (value) {

            this.pagination_max = parseInt(value);

        },
        onResize: function () {
            this.window_width = window.innerWidth
            // console.log(this.window_width);
        },
        showDetails: function (item_id) {


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

        },
        hideDetails: function () {

            const detailVue = document.getElementById("details-window");
            const detailOverlay = document.getElementById("details-overlay");
            if (detailVue) {

                detailVue.classList.remove("opened")
                detailVue.style.pointerEvents = "none";
            }

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



            // START CATEGORIES

            for (folder of vm.folders) {
                if (folder.parent) {
                    let parent_id = parseInt(folder.parent.replace("/folders/", ""));
                    folder.parent = parent_id

                }
            }

            // build folders hierarchy
            let root_folder_id = vm.folders.filter(function (value) {
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


                    let good_folder = vm.folders.filter(function (item) {
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

            // fetch("api_test/get_files_v4.php")
            fetch("wp-content/themes/sprayloc-theme/inc/get_files_v4.php")
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {

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

            if (this.$route.query.page_num) {

                this.onPageChange(this.$route.query.page_num)
                console.log("Page Change : ", this.$route.query.page_num);
            }
        },
        getImageFile: function (file_id) {


            if (this.files.length > 0) {



                // parse file_id
                _file_id = parseInt(file_id.replace("/files/", ""))
                let filtered = this.files.filter(function (value) {
                    return value.id === _file_id;
                })[0]


                if (filtered)
                    return filtered.url
                else
                    return this.placeholder_url + "sdfsdf"

            }
        },
        LinkCheck: function (url) {
            var http = new XMLHttpRequest();
            http.open('HEAD', url, false);
            http.send();
            return http.status != 404;
        },
        getImageThumbnail: function (file_id) {
            if (file_id === null) {

                return this.placeholder_url
            }
            if (this.files) {

                // parse file_id

                _file_id = parseInt(file_id.replace("/files/", ""))


                let filtered = this.files.filter(function (value) {
                    return value.id === _file_id;
                })[0]

                if (filtered !== undefined) {
                    let src = filtered.displayname;


                    let full_name = src.substring(src.lastIndexOf("/") + 1);

                    let ext = full_name.substring(full_name.lastIndexOf("."));
                    let name = full_name.substring(0, full_name.lastIndexOf("."));


                    /*
                        Sanitize filenames !!!!
                        Do it in relation to the thumbnail creation php script
                    */
                    const regex = /[^a-zA-Z0-9]/g;
                    let thumb_name = name.replace(regex, "_");
                    // remove double undescores '__' as in php script
                    thumb_name = thumb_name.replace(/_+/g, '_');
                    thumb_name += "_thumbnail";
                    // console.log(thumb_name);
                    let link = "wp-content/themes/sprayloc-theme/inc/gallery/" + thumb_name + ext;

                    return link;
                }

                return this.placeholder_url;

            }
        },
        getFolderName: function (folder_id) {

            if (folder_id !== null && this.folders !== undefined) {


                let _folder_id = parseInt(folder_id.replace("/folders/", ""));

                let filtered = this.folders.filter(function (value) {

                    return parseInt(value.id) === parseInt(_folder_id);
                })

                if (filtered.length > 0) return filtered[0].displayname
                else return null
            } else {
                return null
            }
        },
        getFolderData: function (folder_id) {

            if (folder_id !== null && this.folders !== undefined) {


                let _folder_id = parseInt(folder_id.replace("/folders/", ""));

                let filtered = this.folders.filter(function (value) {

                    return parseInt(value.id) === parseInt(_folder_id);
                })

                if (filtered.length > 0) return filtered[0]
                else return null
            } else {
                return null
            }
        },
        getEquipmentByID: function (id) {
            if (this.equipment.length > 0 && id !== -1) {


                let ret = this.equipment.filter(function (value) {
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
        sanitizeName: function (name) {
            return name.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
        },
        getPaginatedItems: function (start, end) {

            let array = [];
            // console.log(end, this.filtered.length)
            if (end <= this.filtered.length) {
                for (let i = start; i < end; i++) {
                    array.push(this.filtered[i]);
                    // console.log("adding")
                }
            } else {
                array = this.filtered;
            }
            return array;
        }

    },
    computed: {
        filtered: function () {

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

                // console.log(filtered_equipments);

                // filter category
                if (this.$route.query.category !== undefined) {


                    if (this.$route.query.category === 'all') {
                        return filtered_equipments
                    }
                    const vm = this;

                    filtered_equipments = filtered_equipments.filter(function (item) {
                        let param_category = vm.$route.query.category;
                        let folder_name = vm.getFolderName(item.folder);
                        // check subcats
                        let cur_folder = vm.new_categories.filter(function (folder) {
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

                return filtered_equipments
            }
            console.log("problem with data ------------------")
            return undefined
        },
        filteredFolders: function () {
            if (this.equipment) {

            }
        },
        infos_message: function () {
            let cat = this.$route.query.category;
            if (cat) {

                return `Recherche dans: <strong> ${cat} </strong> <br> ${this.filtered.length} équipement${this.filtered.length > 1 ? 's' : ''}`
            } else {
                return ""
            }
        },
        paginated_items: function () {

            if (this.filtered) {


                let paginated = this.getPaginatedItems(this.pagination_start, this.pagination_end);

                return paginated;
                // console.log("pagination max : ", this.pagination_max)
                let num = this.pagination_max;
                if (this.filtered.length < this.pagination_max) num = this.filtered.length;
                for (let i = 0; i < num; i++) {
                    paginated.push(this.filtered[i]);
                }
                // console.log("paginated", paginated.length);

                if (paginated.length === 0) {
                    return this.filtered;
                }
                return paginated;
            }
            return [];
        }
    },
    watch: {
        '$route'(to, from) {
            console.log("found page_num in query");
            let page_num = to.query.page_num
            // console.log(item_id)
            this.onPageChange(page_num);
            if (page_num === undefined) {
                // this.pagination_start = 2;

            } else {
                // this.id_selected = item_id;
                // this.$emit("show-details", item_id)
            }
        }
    }

});