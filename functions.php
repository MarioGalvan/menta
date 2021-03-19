<?php
/**
 * zoli functions and definitions
 *
 * @package zoli
 */

/**

add_action( 'woocommerce_product_query', 'adev_hide_products_category_shop' );
   
function adev_hide_products_category_shop( $q ) {
  
    $tax_query = (array) $q->get( 'tax_query' );
  
    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( 'sale' ), // Category slug here
           'operator' => 'NOT IN'
    );
  
  
    $q->set( 'tax_query', $tax_query );
  
}

**/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/// admin /// 
if( is_admin() ) {
	require get_template_directory() . '/inc/admin/class-menu.php';
	/**
	 * Load include plugins using for this project
	 */
	require get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';
	require get_template_directory() . '/inc/tgm.php';
}

/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/classes/class-wp-bootstrap-navwalker.php';

/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/classes/class-offcanvas.php';


/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/customizer.php';


/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/functions.php';

/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/markup.php';


/**
 * Theme setup and custom theme supports.
 */
require get_template_directory() . '/inc/setup.php';

/**
 * Register widget area.
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/enqueue.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom pagination for this theme.
 */
require get_template_directory() . '/inc/pagination.php';

/**
 * Custom hooks.
 */
require get_template_directory() . '/inc/template-hooks.php';


/**
 * Custom hooks.
 */
require get_template_directory() . '/inc/post-format-functions.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

// load vendors plugin singleton
if( class_exists('WooCommerce')  ){

	/**
	 * If the core feature plugin is not installed and active then load default functions
	 */
	if( !class_exists("Wpopal_Core") ){ 
		require get_template_directory() . '/inc/vendor/woocommerce/template-functions.php';
	}
	
	require get_template_directory() . '/inc/vendor/woocommerce/product/class-single-layout.php';
	require get_template_directory() . '/inc/vendor/woocommerce/product/class-loop-layout.php';
	require get_template_directory() . '/inc/vendor/woocommerce-functions.php';
}

require get_template_directory() . '/inc/vendor/elementor-functions.php';
require get_template_directory() . '/inc/vendor/elementor-others.php';

add_filter('jetpack_just_in_time_msgs', '__return_false');


function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            width: auto !important;
            background-image: url("<?php echo get_stylesheet_directory_uri();?>/images/menta.png") !important;
            background-size: auto !important;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );


function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Menta Oficial';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );



//ocultar productos sale de página de shop

add_action( 'woocommerce_product_query', 'prefix_custom_pre_get_posts_query' );
 
function prefix_custom_pre_get_posts_query( $q ) {
	
	if( is_shop() ) { //No se mostrarán en estas páginas
	    $tax_query = (array) $q->get( 'tax_query' );
	
	    $tax_query[] = array(
	           'taxonomy' => 'product_cat',
	           'field'    => 'slug',
	           'terms'    => array( 'ropa-sale, sale, accesorios-nina, kit-viajero, monas, cepillos' ), // Categorías que no mostraremos
	           'operator' => 'NOT IN'
	    );
	
	
	    $q->set( 'tax_query', $tax_query );
	}
}

add_action( 'pre_get_posts', 'misha_hide_out_of_stock_in_search' );


/*Ocultar produtos agotados de las busquedas*/ 

add_action( 'pre_get_posts', 'misha_hide_out_of_stock_in_search' );

function misha_hide_out_of_stock_in_search( $query ) {

if( !is_admin() && $query->is_search() && $query->is_main_query() ) 
     {
        $query->set( 'meta_key', '_stock_status' );
        $query->set( 'meta_value', 'instock' );
    } 
}


/*Ocultar categoría SALE de las busquedas*/


function wpb_modify_search_query( $query ) {
    global $wp_the_query;
    if( $query === $wp_the_query && $query->is_search() ) {
        $tax_query = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => array( 'ropa-sale, sale, accesorios-nina, kit-viajero, monas, cepillos' ),
                'operator' => 'NOT IN',
            )
        );
        $query->set( 'tax_query', $tax_query );
    }
}
add_action( 'pre_get_posts', 'wpb_modify_search_query' );


function dias_pasados($fecha_inicial,$fecha_final)
{
$dias = (strtotime($fecha_inicial)-strtotime($fecha_final))/86400;
$dias = abs($dias); $dias = floor($dias);
return $dias;
}




/* CODIGO BUHOBOX */
$user = wp_get_current_user();
$user_id  = get_current_user_id();

function my_has_role($user, $role) {
  $roles = $user->roles; 
  return in_array($role, (array) $user->roles);
}

if(my_has_role($user, 'wholesale_customer')) {
	
	$data = get_user_meta ( $user_id);
	$fecha_ultima_compra = $data['fecha_ultima_compra'][0];
	$fechadehoy = date('d-m-Y');
	$dias = dias_pasados($fecha_ultima_compra,$fechadehoy);
	
	
	if($dias<1 || $dias>=15){
	add_action( 'woocommerce_check_cart_items', 'ValidacionesGenerales' );
//  	add_action( 'woocommerce_check_cart_items', 'wc_minimum_order_amount' );
	add_action( 'woocommerce_checkout_create_order', 'action_woocommerce_checkout_create_order', 10, 2 ); 
 	
	}else{
		echo'<script type="text/javascript">
    console.log("YA ESTA LIBRE DE VALIDACIONES");
    
    </script>'; 
	}
   
}



function ValidacionesGenerales() {
	
	if ( !function_exists( 'wc_add_notice' ) ) { 
    require_once '/includes/wc-notice-functions.php'; 
		} 

	
	global $woocommerce;
	
	//categorias listas
    //$categoriasropa    = array('zapatos','zapatos-sale','jean','blusas','camiseta','enterizo','pantalon','short',
	//					  'plataforma','sandalias','tenis','falda','pijamas','pijama-capri','pijama-pantalon','pijama-short',
	//					  'levantadores','bata','vestido'); 
	
	$categoriaaccesorios   = array('accesorios','accesorios-nina','panoleta-accesorios','anillos','aretes',
						  'cojines','vestido-de-bano','pantuflas','bolsos','cosmetiqueras','collares','diademas','lamparas','tapetes',
						  'maquillaje','panoleta','medias','tapa-ojos','variedades','termos'); 
	
	
	
	 //MENSAJES GENERALES ERRORES
	$message = 'Hola, por primera vez debes comprar en la categoria zapatos ó ropa 6 prendas diferentes variadas'; 
	$notice_type = 'success'; 
	$messageaccesorios = "debe tener un pedido con un mínimo de $200.000 en accesorios para realizar su pedido";
	
	
	
	//NUEVAS CATEGORIAS SEPARADAS
	$categoriafalda='falda';
	$categoriablusa='blusas';
	$categoriajean='jean';
	$categoriacamiseta='camiseta';
	$categoriaenterizo='enterizo';
	$categoriapantalon='pantalon';
	$categoriashort='short';
	$categoriavestido='vestido';	
	$categoriazapatos='zapatos';	
	$categoriazapatossale='zapatos-sale';
	$categoriazapatosplataforma='plataforma';
	$categoriazapatossandalia='sandalias';
	$categoriazapatostenis='tenis';
	$categoriapijamas='pijamas';
	$categoriapijamacapri='pijama-capri';
	$categoriapijamapantalon='pijama-pantalon';
	$categoriapijamashort='pijama-short';
	$categoriapijamalevantadores='levantadores';
	$categoriapijamabata='bata';

	
	//VARIABLES CONTADORAS POR CATEGORIA
	$countfalda=0;
	$countblusas=0;
	$countjean='jean';
	$countcamiseta='camiseta';
	$countenterizo='enterizo';
	$countpantalon='pantalon';
	$countshort='short';
	$countvestido='vestido';	
	$countzapatos='zapatos';	
	$countzapatossale='zapatos-sale';
	$countzapatosplataforma='plataforma';
	$countzapatossandalia='sandalias';
	$countzapatostenis='tenis';
	$countpijamas='pijamas';
	$countpijamacapri='pijama-capri';
	$countpijamapantalon='pijama-pantalon';
	$countpijamashort='pijama-short';
	$countpijamalevantadores='levantadores';
	$countpijamabata='bata';
	
	//VARIABLES EXISTENTES
	$existefalda=0;
	$existeblusa=0;
	$existejean='jean';
	$existecamiseta='camiseta';
	$existeenterizo='enterizo';
	$existepantalon='pantalon';
	$existeshort='short';
	$existevestido='vestido';	
	$existezapatos='zapatos';	
	$existezapatossale='zapatos-sale';
	$existezapatosplataforma='plataforma';
	$existezapatossandalia='sandalias';
	$existezapatostenis='tenis';
	$existepijamas='pijamas';
	$existepijamacapri='pijama-capri';
	$existepijamapantalon='pijama-pantalon';
	$existepijamashort='pijama-short';
	$existepijamalevantadores='levantadores';
	$existepijamabata='bata';
	
	
	//Validacion para categoria ropa
    foreach(WC()->cart->get_cart() as $cart_item ) {

		$item_quantity = $cart_item['quantity']; // Cart item quantity
        $product_id    = $cart_item['product_id']; // The product ID
		$product = $cart_item['data'];
		
			if( has_term($categoriafalda, 'product_cat', $product_id )) {
					$countfalda+=1;
					$existefalda=1;
			}else if(has_term($categoriablusa, 'product_cat', $product_id )){
				$countblusas+=$cart_item['quantity'];
				$existeblusa=1;
			}else if(has_term($categoriajean, 'product_cat', $product_id )){
				$countjean+=$cart_item['quantity'];
				$existejean=1;
			}else if(has_term($categoriacamiseta, 'product_cat', $product_id )){
				$countcamiseta+=$cart_item['quantity'];
				$existecamiseta=1;
			}else if(has_term($categoriaenterizo, 'product_cat', $product_id )){
				$countenterizo+=$cart_item['quantity'];
				$existeenterizo=1;
			}else if(has_term($categoriapantalon, 'product_cat', $product_id )){
				$countpantalon+=$cart_item['quantity'];
				$existepantalon=1;
			}else if(has_term($categoriashort, 'product_cat', $product_id )){
				$countshort+=$cart_item['quantity'];
				$existeshort=1;
			}else if(has_term($categoriavestido, 'product_cat', $product_id )){
				$countvestido+=$cart_item['quantity'];
				$existevestido=1;
			}else if(has_term($categoriazapatos, 'product_cat', $product_id )){
				$countzapatos+=$cart_item['quantity'];
				$existezapatos=1;
			}else if(has_term($categoriazapatossale, 'product_cat', $product_id )){
				$countzapatossale+=$cart_item['quantity'];
				$existezapatossale=1;
			}else if(has_term($categoriazapatosplataforma, 'product_cat', $product_id )){
				$countzapatosplataforma+=$cart_item['quantity'];
				$existezapatosplataforma=1;
			}else if(has_term($categoriazapatossandalia, 'product_cat', $product_id )){
				$countzapatossandalia+=$cart_item['quantity'];
				$existezapatossandalia=1;
			}else if(has_term($categoriazapatostenis, 'product_cat', $product_id )){
				$countzapatostenis+=$cart_item['quantity'];
				$existezapatostenis=1;
			}else if(has_term($categoriapijamas, 'product_cat', $product_id )){
				$countpijamas+=$cart_item['quantity'];
				$existepijamas=1;
			}else if(has_term($categoriapijamacapri, 'product_cat', $product_id )){
				$countpijamacapri+=$cart_item['quantity'];
				$existepijamacapri=1;
			}else if(has_term($categoriapijamapantalon, 'product_cat', $product_id )){
				$countpijamapantalon+=$cart_item['quantity'];
				$existepijamapantalon=1;
			}else if(has_term($categoriapijamashort, 'product_cat', $product_id )){
				$countpijamashort+=$cart_item['quantity'];
				$existepijamashort=1;
			}else if(has_term($categoriapijamalevantadores, 'product_cat', $product_id )){
				$countpijamalevantadores+=$cart_item['quantity'];
				$existepijamalevantadores=1;
			}else if(has_term($categoriapijamabata, 'product_cat', $product_id )){
				$countpijamabata+=$cart_item['quantity'];
				$existepijamabata=1;
			}
		
    }



	
	//validacion categoria accesorios
	 foreach(WC()->cart->get_cart() as $cart_item ) {

		$item_quantity = $cart_item['quantity']; // Cart item quantity
        $product_id    = $cart_item['product_id']; // The product ID
				
			//validar si en el carrito hay categoria accesorios
			 if( has_term($categoriaaccesorios, 'product_cat', $product_id )) {
				 $existeaccesorio=1;
			 }else{
				$existeaccesorio=0;
			 }
    }
	
	
	
	
	
	/*VALIDACIONES GENERALES*/
	
	var_dump($countblusas<6);
	var_dump($countblusas);

	if($existeblusa==1 && $countblusas<6){
		remove_action('woocommerce_proceed_to_checkout','woocommerce_button_proceed_to_checkout', 20);
			
	}


	



	
	
}




function action_woocommerce_checkout_create_order( $order, $data ) { 
 $user_id  = get_current_user_id();
 echo'<script type="text/javascript">
    console.log("eres rol mayorista Y CREASTE UNA ORDEN");
    
    </script>'; 
	 $fechaActual = date('d-m-Y');
	 update_user_meta( $user_id , 'fecha_ultima_compra',  $fechaActual );
}; 
         
















