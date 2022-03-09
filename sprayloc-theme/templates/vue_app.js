const createApp = function () {



    const routes = [{
        path: "/vue-app",
        meta : {
            // reload : true
        }
    }]

    const router = new VueRouter({
        mode: "history",
        routes,
        base: location.pathname
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
    

    <div class="dropdown" v-if="true" id="folders-dropdown" data-bs-auto-close="true">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false">
            Choisir une catégorie
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div v-for="cat in categories" :key="cat.displayname" class="category" :class="currentCategory===cat.displayname?'active':''" >
                <a @click="setCategory(cat.displayname, $event)"  @mouseover="onMouseOver(cat)" >{{ cat.displayname }} </a>
                <div :id="'subfolder-'+cat.displayname+'-dropdown'" >
                    <div v-for="subcat in cat.subfolders" :key="subcat.displayname" class="dropdown-subcategory" :class="currentCategory===subcat.displayname?'active':''">
                        <a @click="setCategory(subcat.displayname, $event)" >{{ subcat.displayname }}</a>
                    </div >
                </div >
            </div>
        </ul>
    </div>
                <div id="inline-categories">

                    <div v-for="cat in categories" :key="cat.displayname" class="category" :class="currentCategory===cat.displayname?'active':''">
                        <a @click="setCategory(cat.displayname, $event)"  @mouseover="onMouseOver(cat)" > {{ cat.displayname }} </a>
                        <div :id="'subfolder-'+cat.displayname" class="subfolders-container">
                            <div v-for="subcat in cat.subfolders" :key="subcat.displayname" class="subcategory" :class="currentCategory===subcat.displayname?'active':''">
                                <a @click="setCategory(subcat.displayname, $event)" >{{ subcat.displayname }}</a>
                            </div >
                        </div >
                    </div >

                    <div  @click="setCategory('all', $event)" @mouseover="onMouseOver()" class="category" :class="currentCategory==='all'?'active':''">
                        <a> Tout </a>
                    </div>
                </div>
    </div >
</div >
        `,
        methods: {
            setCategory: function (category_name, event) {

                event.preventDefault();
                //event.stopPropagation();

                this.$emit("change-category");
                if (category_name === "all") {

                    this.$router.push({
                        path: "/",
                        query: { page_num: 1 }
                    })


                } else {

                    this.$router.push({
                        path: "/",
                        query: {
                            category: category_name,
                            page_num: 1
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
                    // console.log("waht is that ?");
                    let subfolder_div = document.getElementById('subfolder-' + item.displayname);

                    if (subfolder_div) subfolder_div.classList.add("show")
                    // console.log(subfolder_div.classList)
                    // console.log(subfolder_div)
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
                let id;
                try {

                    id = this.getParentEquipmentID(item_id)
                } catch (error) {
                    console.log(error);
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
                        <div class="title" data-toggle="tooltip" data-placement="bottom" v-bind:title="data.name"><a @click="showDetails(data.id)">{{data.name}}</a></div>
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
            <div class="wrapper" v-if="filtered.length > maxitems">
            <!--
            <span class="num-items" >{{numcards}}</span>
            <input type="number" cols="3" style="width: 3em;" @input="onInput" v-model="max_items" />
            -->
            
                <span class="pagination-navigation" @click="onPrevPage"> << </span>
                <span class="page-numbers"> 
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

                this.$router.push({
                    path: "/",
                    query: {
                        ...this.$route.query,
                        page_num
                    }
                })
                this.$emit("page-change", page_num);


            },
            onPrevPage: function () {
                if (this.current_page > 1) {
                    this.$router.push({
                        path: "/",
                        query: {
                            ...this.$route.query,
                            page_num: this.current_page - 1
                        }
                    })
                    this.$emit("page-change", this.current_page - 1)
                }

            },
            onNextPage: function () {
                if (this.current_page < Math.ceil(this.filtered.length / this.maxitems)) {
                    this.$router.push({
                        path: "/",
                        query: {
                            ...this.$route.query,
                            page_num: this.current_page + 1
                        }
                    })
                    this.$emit("page-change", this.current_page + 1)
                }

            },
            onInput: function (event) {

                this.$emit("change-pagination-max", parseInt(event.target.value));
            }
        },
        computed: {
            max_items: function () {
                return this.pagination_max
            },
            pageSize: function () {
                return Math.ceil(this.filtered.length / this.maxitems);
            },
            current_page: function () {
                let page_num = this.$route.query.page_num !== undefined ? this.$route.query.page_num : 1; 
                return parseInt(page_num);
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
                data_loaded: undefined,
                pagination_max: 35,
                pagination_start: 0,
                pagination_end: 35,
                pagination_current_page: 1,
                placeholder_url: "wp-content/themes/sprayloc-theme/assets/sprayloc_logo_placeholder.jpg",
                delayed_func_interval : 0
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
        mounted: function () {

            this.delayed_func_interval = setInterval(this.initBloodySearchInput, 100);
            this.forceCameras();

        },
        beforeDestroy : function(){
            console.log("beforeRouterLeave --> ");
        },
        methods: {
            forceCameras : function(){
                this.$router.push({
                    path : "/",
                    query : {
                        category : "Caméras"
                    }
                })
            },
            initBloodySearchInput : function(){

                let search_input = document.querySelector("#search-input");
                console.log(search_input);
                if( search_input !== null) {
                    clearInterval(this.delayed_func_interval);
                    const set_string_filter = function (e) {

                        e.stopPropagation();
                        e.preventDefault();
                        console.log(" HAAAAAA ! : ", e.target.value);
                        if (e.target.value !== "") {

                            this.string_filter = e.target.value;
                            this.$router.push({
                                path: "/",
                                query: {
                                    string_filter: e.target.value
                                }
                            })
                        } else {
                            console.log(" Empty value: ", e.target.value);
                            this.string_filter = "";
                            this.$router.push({
                                path: "/",
                                query: {
                                    category: this.$route.query.category,
                                    string_filter: ""
                                }
                            })


                        }
                    }
                    search_input.addEventListener("input", set_string_filter.bind(this));
                    search_input.addEventListener("enter", set_string_filter.bind(this));
                    search_input.addEventListener("blur", set_string_filter.bind(this));
                }

            },
            onChangeCategory: function(){
                // console.log(this.string_filter);
                // this.string_filter = "";
                // console.log(this.string_filter);
            },
            onPageChange: function (page_num) {
                if (this.filtered) {


                    let num_items = this.filtered.length;
                    let max_per_page = this.pagination_max;


                    let first_index = max_per_page * (page_num - 1);

                    let last_index = first_index + max_per_page;
                    if (last_index > num_items) {

                        last_index -= last_index - num_items;
                    }


                    this.pagination_start = first_index;
                    this.pagination_end = last_index;

                    this.pagination_current_page = page_num

                }
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
                console.log("ha !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
                console.log(this.getEquipmentByID(item_id))
                // console.log(this.getImageThumbnail(item_id))

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


                //// sort folders by 'order' property
                vm.folders = vm.folders.sort((a, b) => {
                    return a.order - b.order;
                })
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


                // console.log("Got Data");
                // let anim = vm.$refs["loading-animation"];
                // console.log(anim);
                // console.log(vm)
                // if (anim) {

                //     console.log(anim.style.display);
                //     anim.style.display = "none";
                //     console.log(anim.style.display);
                // }
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

                console.log("Getting Data");
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

                if (this.filtered) {

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
                return undefined;
            }

        },
        computed: {
            filtered: function () {
                
                let vm = this;
                console.log("CHANGE !!!!!!");
                if (this.data_loaded) {

                    let filtered_equipments = this.equipment;
                    let pattern = this.string_filter != undefined ? this.string_filter : "";

                    let words = pattern.split(" ");
 

                    if( pattern != ""){

                        filtered_equipments = this.equipment.filter((value) => {
                            let found = false;
                            let num = 0
                            for (let word of words) {
    
                                let found_index = this.sanitizeName(value.name).toLowerCase()
                                .search(this.sanitizeName(word).toLowerCase());
    
                                if (found_index != -1) {
                                    found = true;
                                    num++;
                                    // break;
                                }else{
    
                                    // also check in categorie name
                                    let folder_name = this.getFolderName(value.folder);
                                    // console.log(folder_name);
                                    let found_index2 = this.sanitizeName(folder_name).toLowerCase()
                                        .search(this.sanitizeName(word).toLowerCase());
        
                                    if (found_index2 != -1) {
                                        found = true;
                                        num++;
                                        // break;
                                    }
                                    // getFolderName(value.folder);
                                }
    
                            }
                            return num === words.length;
                        })
                    }else{

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
            },
            app_loaded: function () {
                return this.data_loaded;
            }
        },
        watch: {
            '$route'(to, from) {

                console.log(to);

                if (this.$route.query.string_filter !== undefined ){
                    this.string_filter = this.$route.query.string_filter;
                    console.log("String Filter : ", this.string_filter);
                    let search_input = document.getElementById("search-input");
                    if( search_input ){
                        search_input.value = this.string_filter;
                    }
                }else{
                    this.string_filter = "";
                    let search_input = document.getElementById("search-input");
                    search_input.value = "";
                }
    
                // if(to.meta.reload==true) {
                //     window.location.reload();
                // }
                let page_num = to.query.page_num

                this.onPageChange(page_num);
    
            },
            data_loaded(to) {
                if (to === true) {
                    let anim = document.getElementById("loading-animation");

                    console.log(anim)
                    anim.style.display = "none";
                }
            }
        }

    });

}
document.addEventListener("DOMContentLoaded", function () {
    createApp();

})