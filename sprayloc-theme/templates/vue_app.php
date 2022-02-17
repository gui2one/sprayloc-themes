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

?>

<div id="app-inventaire">

    <folders-bar :categories="new_categories" v-if="data_loaded"></folders-bar>
    <sprayloc-pagination :numcards="filtered.length" :filtered="filtered" :maxitems="pagination_max" @page-change="onPageChange" @change-pagination-max="onChangePaginationMax"></sprayloc-pagination>
    <div id="search-bar">
        <div id="search-infos" v-html="infos_message"></div>
        <div id="search">
            <i class="fa fa-search"></i>
            <input type="search" name="search-input" v-model="string_filter" id="search-input" placeholder="Rechercher">
        </div>
    </div>
    <spinner v-if="!data_loaded"></spinner>
    <div v-else>

        <div v-if="paginated_items.length == 0" class="no-equipment">
            Aucun équipement dans cette catégorie.
            <br> <a href="?category=all">Voir tous les équipements</a>
        </div>
        <div class="cards-container" v-if="window_width > 500">
            <sprayloc-card   v-for="item in paginated_items" v-bind:data="item"
                v-bind:image="getImageThumbnail(item.image) ? getImageThumbnail(item.image) : getImageThumbnail(item.images[0])"
                @show-details="showDetails" v-bind:folder="getFolderName(item.folder)" :key="item.id"></sprayloc-card>
        </div>
        <div class="cards-container" v-else>
            <sprayloc-item-row v-for="item in getPaginatedItems(this.pagination_start,this.pagination_end)" v-bind:data="item"
                v-bind:image="getImageThumbnail(item.image) ? getImageThumbnail(item.image) : getImageThumbnail(item.images[0])"
                @show-details="showDetails" v-bind:folder="getFolderName(item.folder)" :key="item.id"></sprayloc-item-row>
        </div>
        <detail-vue v-if="data_loaded" :item="getEquipmentByID(id_selected)" :kits="kits" @hide-details="hideDetails"
            @show-details="showDetails" />
    </div>

</div>
<script src="<?php echo get_stylesheet_directory_uri();?>/templates/vue_app.js" ></script>


<?php

get_footer();
