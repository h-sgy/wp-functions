<?php

function add_css_js() {
	//CSSの読み込みはここから
	wp_enqueue_style('style', get_template_directory_uri().'/assets/css/style.css', array(), '1.0', 'all' );
    //JavaScriptの読み込みはここから
    wp_enqueue_script('main', get_template_directory_uri().'/assets/js/main.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'add_css_js');


// カスタム投稿
add_action( 'init', 'register_custom_post_types' );
function register_custom_post_types() {

    $labels = array(
      'name' => 'ニュース',
      'singular_name' => 'ニュース',
      'add_new' => 'ニュース追加',
      'add_new_item' => 'ニュースを追加',
      'edit_item' => 'ニュースを編集',
      'new_item' => '新しいニュース',
      'view_item' => 'ニュースを見る',
      'search_items' => 'ニュース検索',
      'not_found' => 'ニュースが見つかりません',
      'not_found_in_trash' => 'ゴミ箱にニュースはありません',
      'parent_item_colon' => 'ニュース:',
      'menu_name' => 'ニュース',
    );
  
    $args = array(
      'labels' => $labels,
      'hierarchical' => true,
      'description' => 'ニュース',
      'supports' => array('title', 'thumbnail', 'editor', 'custom-fields', 'revisions'),
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_position' => 4,
      'show_in_nav_menus' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'has_archive' => true,
      'query_var' => true,
      'can_export' => true,
      'rewrite' => array('with_front' => false),
      'capability_type' => 'post',
    );
  
    register_post_type( 'cameras', $args );
}

//ブログのカスタムタクソノミー
add_action( 'init', 'create_taxonomies' );
function create_taxonomies() {
  $labels = [
    'name' => 'ニュースカテゴリー'
  ];
  $args = [
    'hierarchical'          => true,
    'labels'                => $labels
  ];
  register_taxonomy( 'news_category', 'news', $args );

  $labels = [
    'name' => 'ニュースタグ'
  ];
  $args = [
    'labels'                => $labels
  ];
  register_taxonomy( 'news_tag', 'news', $args );

}


// カスタム投稿を数字ベースにする
add_filter( 'post_type_link', '_post_type_link', 1, 2 );
function _post_type_link( $link, $post ){
    if ( 'cameras' === $post->post_type ) {
        return home_url( '/cameras/' . $post->ID );
    } else {
        return $link;
    }
}
add_filter( 'rewrite_rules_array', '_rewrite_rules_array' );
function _rewrite_rules_array( $rules ) {
    $new_rules = array( 
        'cameras/([0-9]+)/?$' => 'index.php?post_type=cameras&p=$matches[1]',
    );
    
    return $new_rules + $rules;
}

//アイキャッチ有効化
add_theme_support( 'post-thumbnails', array( 'cameras' ) );
//タイトル有効化
add_theme_support( 'title-tag' );


add_filter( 'body_class', 'add_page_slug_class_name' );
function add_page_slug_class_name( $classes ) {
  if ( is_page() ) {
    $page = get_post( get_the_ID() );
    $classes[] = $page->post_name;
  }
  return $classes;
}


add_action('template_redirect', function () {
    ob_start(function ($buffer) {
        $buffer = str_replace(array(' type="text/javascript"', "type='text/javascript'"), '', $buffer);
        $buffer = str_replace(array(' type="text/css"', "type='text/css'"), '', $buffer);
        return $buffer;
    });
});


//管理画面の使わない項目を非表示
add_action( 'admin_menu', 'remove_menus' );
function remove_menus(){
    // remove_menu_page( 'index.php' ); //ダッシュボード
    remove_menu_page( 'edit.php' ); //投稿メニュー
    // remove_menu_page( 'upload.php' ); //メディア
    // remove_menu_page( 'edit.php?post_type=page' ); //ページ追加
    remove_menu_page( 'edit-comments.php' ); //コメントメニュー
    // remove_menu_page( 'themes.php' ); //外観メニュー
    // remove_menu_page( 'plugins.php' ); //プラグインメニュー
    // remove_menu_page( 'tools.php' ); //ツールメニュー
    // remove_menu_page( 'options-general.php' ); //設定メニュー
}