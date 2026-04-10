<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Segment;
// use App\Helpers\SpecialFunc;

class MainController extends Controller
{

	public function __construct() {}

	public function findTerms()
	{
		return view('content.terms');
	}

	public function showConstruction()
	{
		$participant = NULL;
		$page = \Solunes\Master\App\Page::find(3);
		return view('content.construction', ['page' => $page]);
	}

	public function showIndex()
	{
		//return redirect('admin/redirect');
		$locale = \App::getLocale();
		$page = \Solunes\Master\App\Page::where('customized_name', 'home')->first();
		return redirect($page->translate($locale)->slug);
	}

	public function showLogin()
	{
		$participant = NULL;
		return view('content.login', ['item' => $participant]);
	}

	public function getSendSms()
	{
		$number = '+59170554450';
		$number = '+59169805530';
		$message = 'SMS de Prueba ácentos señor y & con % asd.';
		$return = \Func::send_sms($number, $message, 'ClubBolivar', true);
		echo 'Mensaje enviado: ' . json_encode($return);
	}

	public function testMail($number_sale)
	{
		$sale = \Solunes\Sales\App\Sale::find($number_sale);
		$link = \CustomFunc::sendSuccessMail($sale);
		$response_1 = $link;
		\Mail::send('emails.notifications.daily-report', [], function ($message) use ($response_1) {
			//Test
			$send_array = ['boliamau.32@gmail.com'];
			//$send_array = ['edumejia30@gmail.com'];
			// Samsung
			//$send_array = ['informacion@anisa-srl.com'];
			$message->to($send_array)->subject('Compra recibida en LaGanga.com.bo - ' . date('Y-m-d'));
			$message->replyTo(['informacion@laganga.com.bo']);
			$message->attach($response_1);
		});

		$vars = ['@venta@' => $sale->name, '@total@' => $sale->amount, '@url@' => url('account/my-history/1354351278')];
		\FuncNode::make_email('successful_payment', ['boliamau.32@gmail.com'], $vars);

		return $response_1;
	}

	public function showRegister()
	{
		$participant = NULL;
		return view('content.register', ['item' => $participant]);
	}

	public function showCard()
	{
		$school_registration = \App\SchoolRegistration::first();
		$array = ['item' => $school_registration->customer_dependant, 'school' => $school_registration->school];
		//return view('pdf.card-pdf', $array);
		$pdf = \PDF::loadView('pdf.card-pdf', $array);
		return $pdf->setOption('page-width', 55)->setOption('page-height', 86)->setOrientation('landscape')->stream('1.pdf');
	}

	public function showPage($slug)
	{
		if ($page_translation = \Solunes\Master\App\PageTranslation::findBySlug($slug)) {
			$page = $page_translation->page;
			if ($page->type != 'blank' && $page->type != 'external' && $page_translation->locale != \App::getLocale()) {
				return redirect('change-locale/' . $page_translation->locale . '/' . $page_translation->slug);
			}
			$array = ['page' => $page, 'i' => NULL, 'dt' => false];
			if ($page->type == 'blank' || $page->type == 'external') {
				return abort(404);
			}
			$slug = $page_translation->slug;
			if ($page->type == 'customized') {
				$array = \CustomFunc::get_custom_node_array($array, $page, false);
				return view('content.' . $page->customized_name, $array);
			} else {
				$array = \CustomFunc::get_node_array($array, $page, false);
				return view('content.page', $array);
			}
		} else {
			return abort(404);
		}
	}

	public function openForm()
	{
		$cities = \App\City::lists('name', 'id');
		return view('content.formulario', ['cities' => $cities]);
	}

	//
	public function findProduct($slug)
	{
		$subitem = \Solunes\Business\App\ProductBridgeTranslation::findBySlug($slug);
		if (!$subitem) {
			return redirect('inicio#alert')->with(['message_error' => 'No se encontró el producto.']);
		}
		$item = $subitem->product_bridge;
		$subitem = \Solunes\Product\App\Product::find($item->product_id);
		if (!$subitem) {
			return redirect('inicio#alert')->with(['message_error' => 'No se encontró el producto solicitado.']);
		}
		$product_gallery = \Solunes\Product\App\ProductImage::where('parent_id', $subitem->id)->get();
		$product = \Solunes\Business\App\ProductBridge::where('category_id', $item->category_id)->where('id', '!=', $item->id)->limit(8)->orderBy('id', 'DESC')->get();
		$page = \Solunes\Master\App\Page::find(3);
		return view('content.product', ['page' => $page, 'item' => $item, 'product_gallery' => $product_gallery, 'product' => $product]);
	}

	public function findInfo($slug)
	{
		$item = \App\InformationTranslation::findBySlug($slug);
		if (!$item) {
			return redirect('inicio#alert')->with(['message_error' => 'No se encontró la página.']);
		}
		$item = $item->information;
		$information = \App\Information::limit(5)->orderBy('id', 'DESC')->get();
		$page = \Solunes\Master\App\Page::find(3);
		return view('content.info', ['item' => $item, 'page' => $page, 'information' => $information]);
	}

	public function findCategory($id)
	{
		$item = \Solunes\Business\App\Category::where('id', $id)->with(['product_bridges', 'products'])->first();
		if (!$item) {
			return redirect('inicio#alert')->with(['message_error' => 'No se encontró la página.']);
		}
		$page = \Solunes\Master\App\Page::find(3);
		//
		$variables = [];
		$variable_groups = \Solunes\Business\App\Variation::get();
		foreach ($variable_groups as $variable_group) {
			$variables[$variable_group->id]['id'] = $variable_group->id;
			$variables[$variable_group->id]['name'] = $variable_group->label;
			$variable_id = $variable_group->id;

			$variables[$variable_group->id]['items'] = $variable_group->variation_options()->get()->lists('name', 'id')->toArray();
		}
		$variables = $variables;
		//
		$subarray['variations'] = \Solunes\Business\App\Variation::get();
		return view('content.category', ['page' => $page, 'item' => $item, 'variables' => $variables]);
	}

	public function findXml()
	{
		$array['sites'] = \DB::table('sites')->get();
		$array['products'] = \Solunes\Product\App\Product::where('active', 1)->get();

		return response()->view('xml.xml', $array)->header('Content-Type', 'text/xml');
	}

	public function findProducts()
	{
		$product_bridges = \Solunes\Product\App\Product::where('quantity', '>', 0)->where('active', 1)->orderBy('created_at', 'DESC')->paginate(24);
		//
		$variables = [];
		$variable_groups = \Solunes\Business\App\Variation::get();
		foreach ($variable_groups as $variable_group) {
			$variables[$variable_group->id]['id'] = $variable_group->id;
			$variables[$variable_group->id]['name'] = $variable_group->label;
			$variable_id = $variable_group->id;

			$variables[$variable_group->id]['items'] = $variable_group->variation_options()->get()->lists('name', 'id')->toArray();
		}
		$variables = $variables;
		//
		$subarray['variations'] = \Solunes\Business\App\Variation::get();
		return view('content.products', ['product_bridges' => $product_bridges, 'variables' => $variables]);
	}

	public function findProductsWithOutPrice()
	{
		$product_bridges = \Solunes\Product\App\Product::where('active', 1)
			->where('price', null)->orwhere('price', 0)->orderBy('created_at', 'DESC')->paginate(24);
		//
		$variables = [];
		$variable_groups = \Solunes\Business\App\Variation::get();
		foreach ($variable_groups as $variable_group) {
			$variables[$variable_group->id]['id'] = $variable_group->id;
			$variables[$variable_group->id]['name'] = $variable_group->label;
			$variable_id = $variable_group->id;

			$variables[$variable_group->id]['items'] = $variable_group->variation_options()->get()->lists('name', 'id')->toArray();
		}
		$variables = $variables;
		//
		$subarray['variations'] = \Solunes\Business\App\Variation::get();
		return view('content.products', ['product_bridges' => $product_bridges, 'variables' => $variables]);
	}

	public function findProductByBrand($id)
	{
		//$item = \Solunes\Business\App\Category::where('id',$id)->with('product_bridges')->first();
		$item = \Solunes\Business\App\Brand::with('product_bridges')->has('product_bridges')->find($id);
		if (!$item) {
			return redirect('inicio#alert')->with(['message_error' => 'No se encontró la página.']);
		}
		$brands = \Solunes\Business\App\Brand::has('product_bridges')->get();
		$page = \Solunes\Master\App\Page::find(3);
		return view('content.brand', ['page' => $page, 'item' => $item, 'brands' => $brands]);
	}

	public function findProductByGroup($id)
	{
		//$item = \Solunes\Business\App\Category::where('id',$id)->with('product_bridges')->first();
		$item = \App\Group::find($id);
		if (!$item) {
			return redirect('inicio#alert')->with(['message_error' => 'No se encontró la página.']);
		}
		$brands = \App\Group::where('category_id', $item->category_id)->where('active', 1)->orderBy('order', 'ASC')->get();
		$page = \Solunes\Master\App\Page::find(3);
		return view('content.category', ['page' => $page, 'item' => $item, 'brands' => $brands]);
	}

	/*public function findSearchProduct() {
        //if(request()->has('search')){
	        //$term = request()->input('search');
			$term = mb_strtolower( request()->input('search'), 'UTF-8');
		    $term = trim( $term );
		    $term = str_replace(' ',  '', $term );
		    $term = str_replace('á', 'a', $term);
		    $term = str_replace('é', 'e', $term);
		    $term = str_replace('í', 'i', $term);
		    $term = str_replace('ó', 'o', $term);
			$term = str_replace('ú', 'u', $term);
			$term = preg_replace('/[^a-z0-9]/i', '', $term);			
	        $brands_ids = \Solunes\Business\App\Brand::whereRaw(
				"TRIM(
					REPLACE(
						REGEXP_REPLACE(
							REPLACE(
								REPLACE( 
									REPLACE(
										REPLACE(
											REPLACE(
												LOWER( name ), 'á', 'a'
											),
											'é', 'e'
										),
										'í', 'i'
									),
									'ó', 'o'
								),
								'ú', 'u'
							),
							'[^a-z0-9]', ''
						),
						' ', ''
					)
				) LIKE ? ", 
				['%'. $term. '%']
			)->lists('id')->toArray();
			//
			// $product_bridge_items = \Solunes\Business\App\ProductBridgeTranslation::where('name', 'LIKE', '%'.$term.'%')->lists('product_bridge_id')->toArray();
			$product_bridge_items = \Solunes\Business\App\ProductBridgeTranslation::whereRaw(
				"TRIM(
					REPLACE(
						REGEXP_REPLACE(
							REPLACE(
								REPLACE( 
									REPLACE(
										REPLACE(
											REPLACE(
												LOWER( name ), 'á', 'a'
											),
											'é', 'e'
										),
										'í', 'i'
									),
									'ó', 'o'
								),
								'ú', 'u'
							),
							'[^a-z0-9]', ''
						),
						' ', ''
					)
				) LIKE ? ", 
				['%'. $term. '%']
			)->lists('product_bridge_id')->toArray();
	        $product_bridges = \Solunes\Business\App\ProductBridge::where('active',1)->where(function($query) use ($term, $product_bridge_items, $brands_ids) {
	        	$query->whereIn('id', $product_bridge_items);
	        	$query->orWhereIn('brand_id', $brands_ids);
	        })->get();
        //}
		$page = \Solunes\Master\App\Page::find(3);
	    return view('content.search-product', ['page'=>$page, 'product_bridges'=>$product_bridges]);
	}*/

	public function findSearchProduct()
	{
		//if(request()->has('search')){
		//$term = request()->input('search');
		$term = mb_strtolower(request()->input('search'), 'UTF-8');
		$term = str_replace('á', 'a', $term);
		$term = str_replace('é', 'e', $term);
		$term = str_replace('í', 'i', $term);
		$term = str_replace('ó', 'o', $term);
		$term = str_replace('ú', 'u', $term);
		$term = str_replace('à', 'a', $term);
		$term = str_replace('è', 'e', $term);
		$term = str_replace('ì', 'i', $term);
		$term = str_replace('ò', 'o', $term);
		$term = str_replace('ù', 'u', $term);
		//
		$term =	preg_replace('/(os$)/i', 'o', $term);
		$term =	preg_replace('/(as$)/i', 'a', $term);
		$term =	preg_replace('/(.*)(es$)/i', '$1', $term);
		//
		$term = preg_replace('/[^a-z0-9ñ]/i', '', $term);
		$term = trim($term);
		$brands_ids = \Solunes\Business\App\Brand::whereRaw(
			"TRIM(
					REPLACE(
						REPLACE(
						  REPLACE( 
							REPLACE(
							  REPLACE(
								REPLACE(
								  LOWER( name ), 'á', 'a'
								),
								'é', 'e'
							  ),
							  'í', 'i'
							),
							'ó', 'o'
						  ),
						  'ú', 'u'
						),
					  ' ', ''
					)
				) LIKE ? ",
			['%' . $term . '%']
		)->lists('id')->toArray();
		$category_ids = \Solunes\Business\App\CategoryTranslation::whereRaw(
			"TRIM(
					REPLACE(
						REPLACE(
						  REPLACE( 
							REPLACE(
							  REPLACE(
								REPLACE(
								  LOWER( name ), 'á', 'a'
								),
								'é', 'e'
							  ),
							  'í', 'i'
							),
							'ó', 'o'
						  ),
						  'ú', 'u'
						),
					  ' ', ''
					)
				) LIKE ? ",
			['%' . $term . '%']
		)->lists('category_id')->toArray();
		//
		// $product_bridge_items = \Solunes\Business\App\ProductBridgeTranslation::where('name', 'LIKE', '%'.$term.'%')->lists('product_bridge_id')->toArray();
		$product_bridge_items = \Solunes\Business\App\ProductBridgeTranslation::whereRaw(
			"TRIM(
					REPLACE(
						REPLACE(
						  REPLACE( 
							REPLACE(
							  REPLACE(
								REPLACE(
								  LOWER( name ), 'á', 'a'
								),
								'é', 'e'
							  ),
							  'í', 'i'
							),
							'ó', 'o'
						  ),
						  'ú', 'u'
						),
					  ' ', ''
					)
				) LIKE ? ",
			['%' . $term . '%']
		)->lists('product_bridge_id')->toArray();
		$product_bridges = \Solunes\Business\App\ProductBridge::has('product')->where('quantity', '>', 0)->where('active', 1)->where(function ($query) use ($term, $product_bridge_items, $brands_ids, $category_ids) {
			$query->whereIn('id', $product_bridge_items);
			$query->orWhereIn('brand_id', $brands_ids);
			$query->orWhereIn('category_id', $category_ids);
		})->get();

		$save_search = new \App\SaveSearch;
		if (auth()->check()) {
			$user = auth()->user();
			$customer = $user->customer;
			$save_search->customer_id = $customer->id;
		}
		$save_search->busqueda = request()->input('search');
		$save_search->save();
		//}
		$page = \Solunes\Master\App\Page::find(3);
		$search = request()->input('search') ? request()->input('search') : '';
		return view('content.search-product', [
			'page' 			  => $page,
			'product_bridges' => $product_bridges,
			'search'          => $search
		]);
	}

	public function findProductOffers()
	{
		$categories = \Solunes\Business\App\PricingRule::where('active', 1)->orderBy('discount_percentage', 'DESC')->lists('category_id')->toArray();
		$category_string = '';
		foreach ($categories as $key => $category) {
			if ($key > 0) {
				$category_string .= ", ";
			}
			$category_string .= "'" . $category . "'";
		}
		\Log::info($category_string);
		$products   = \Solunes\Business\App\PricingRule::whereNotNull('product_bridge_id')->where('active', 1)->lists('product_bridge_id')->toArray();
		if (count($categories) == 0) {
			$product_bridges = \Solunes\Product\App\Product::orWhereHas('product_bridge', function (\Illuminate\Database\Eloquent\Builder $query) use ($products) {
				$query->whereIn('product_bridges.id', $products);
			})->orWhereIn('category_id', $categories)->where('quantity', '>', 0)->where('active', 1)->orWhere('discount_price', '>', 0)->paginate(24);
		} else {
			$product_bridges = \Solunes\Product\App\Product::orWhereHas('product_bridge', function (\Illuminate\Database\Eloquent\Builder $query) use ($products) {
				$query->whereIn('product_bridges.id', $products);
			})->orWhereIn('category_id', $categories)->where('quantity', '>', 0)->where('active', 1)->orWhere('discount_price', '>', 0)->orderByRaw("FIELD(category_id, " . $category_string . ")")->paginate(24);
		}
		//var_dump($product_bridges);
		/*foreach($product_bridges as $product_bridge){
			foreach($product_bridge as $product){
				$percentage = $product->real_price/$product->full_price;
			}
		}*/
		$page = \Solunes\Master\App\Page::find(3);
		return view('content.product-offers', ['page' => $page, 'product_bridges' => $product_bridges]);
	}

	public function findTestQueries()
	{
		$user = \App\User::find(3);
		$ride = \App\Ride::find(1);
		$sale = \SpecialFunc::make_ride_sale(
			$user->id,
			$user->customer->id,
			$ride->id,
			'Test',
			3,
			'Test',
			'12345678'
		);
		\Func::vardump($sale);
		die();
		$result = \SpecialFunc::make_ride_sale(1, 1, 1, 'Viaje desde - hasta etc', 2, 'Gutierrez', '13179766');
		\Func::vardump($result);
		die();
		\SpecialFunc::send_sms_twilo('77112939', 'Su codigo de confirmacion es 12345....');
	}


	public function getPointsRide($uuid)
	{
		$ride = \App\Ride::where('uuid', $uuid)->first();
		$item = \App\Request::where('id', $ride->parent_id)->with(['request_waypoints' => function ($query) {
			$query->orderBy('order', 'asc');
		}])->first();
		$user = \App\User::where('id', $item->user_id)->first();
		return view('content.request-tracking.tracking', [
			'items' => $item,
			'token' => $user->token_jwt
		]);
	}

	public function registerDriver()
	{
		// $user = auth()->user();
		// $site = \Solunes\
		$cities = []; // Se carga dinámicamente por JS según el Departamento seleccionado
		$regions = \Solunes\Business\App\Region::get();
		$organizations = \App\Organization::where('type', 'company')->get();

		$vehiclesModels = \App\VehicleModel::where('active', true)->get();
		$vehiclesBrands = \App\VehicleBrand::where('active', true)->get();
		$banks = \App\Bank::get();
		$type_vehicle = [["id" => "auto", "name" => "Automóvil"], ["id" => "moto", "name" => "Motocicleta"], ["id" => "torito", "name" => "Torito"]];

		return view('content.register.step1', [
			'site' => [],
			'cities' => $cities,
			'regions' => $regions,
			'organizations' => $organizations,
			'vehiclesModels' => $vehiclesModels,
			'vehiclesBrands' => $vehiclesBrands,
			'typeVehicle' => $type_vehicle,
			'banks' => $banks,
		]);
	}

	public function updateRegisterDriver($id)
	{
		$driver = \App\Driver::where('id', $id)->with('driver_vehicles')->first();

		$cities = \Solunes\Business\App\City::get();
		$regions = \Solunes\Business\App\Region::get();
		$organizations = \App\Organization::get();

		$vehiclesModels = \App\VehicleModel::where('active', true)->get();
		$vehiclesBrands = \App\VehicleBrand::where('active', true)->get();

		return view('content.register.step1-update', [
			'site' => [],
			'cities' => $cities,
			'regions' => $regions,
			'organizations' => $organizations,
			'vehiclesModels' => $vehiclesModels,
			'vehiclesBrands' => $vehiclesBrands,
			'driver' => $driver
		]);
	}

	public function registerVehicle($driverId)
	{
		$driver = \App\Driver::where('id', $driverId)->first();
		// $user = auth()->user();
		$cities = \Solunes\Business\App\City::get();
		$organizations = \App\Organization::get();
		$vehiclesModels = \App\VehicleModel::where('active', true)->get();
		$vehiclesBrands = \App\VehicleBrand::where('active', true)->get();
		return view('content.register.step2', [
			// 'site'=>$user->site,
			'cities' => $cities,
			'organizations' => $organizations,
			'vehiclesModels' => $vehiclesModels,
			'vehiclesBrands' => $vehiclesBrands,
			'vehiclesBrands' => $vehiclesBrands,
			'driver' => $driver,
		]);
	}

	public function registerSuccess()
	{
		return view('content.register.step3');
	}



	public function verifyUser($userId)
	{
		try {
			$user = \App\User::where('id', $userId)->first();
			if (!$user) {
				return redirect('/customer-admin/model-list/driver')->with('message_error', 'El usuario no existe');
			} else {
				$verify = $user->is_verify;
				if (!$verify) {

					$driver = \App\Driver::where('user_id', $userId)->first();
					$driverVehicles = \App\DriverVehicle::where('parent_id', $driver->id)->get();
					if (count($driver->driver_vehicles) == 0) {
						return redirect('/customer-admin/model-list/driver')->with('message_error', 'No puede habilitar a este conductor porque no exite un vehículo.');
					}

					// AQUI VA LA CONFIRMACION 
					if ($verify == false) {
						if ($driver->email != '' || $driver->email != null) {
							// \SpecialFunc::send_email("Central de Taxis", [$driver->email], 'Registro de Conductor exitoso', '¡Te damos la bienvenida a nuestra innovadora aplicación! Nos complace enormemente que formes parte de nuestro equipo.');
						}
					}

					\App\User::where('id', $userId)->update(['is_verify' => !$verify]);
					return redirect('/customer-admin/model-list/driver')->with('message_success', 'La cuenta del Conductor fué actualizada correctamente.');
				} else {
					\App\User::where('id', $userId)->update(['is_verify' => !$verify]);
					return redirect('/customer-admin/model-list/driver')->with('message_success', 'La cuenta del Conductor fué actualizada correctamente.');
				}
			}
		} catch (\Exception $e) {
			dd($e);
			return redirect('/customer-admin/model-list/driver')->with('message_error', 'Error en el servidor.');
		}
	}

	public function postRegisterDriver(Request $request)
	{
		\Log::info('========== INICIO REGISTRO CONDUCTOR ==========');
		\Log::info('Datos recibidos:', $request->except(['password', 'image', 'license_front_image', 'license_back_image', 'ci_front_image', 'ci_back_image', 'vehicle_image', 'side_image']));
		\Log::info('Archivos recibidos:', array_keys($request->allFiles()));

		$validator = \Validator::make($request->all(), [
			'first_name' => 'required|string|min:2',
			'last_name' => 'required|string|min:2',
			'email' => 'required|email',
			'cellphone' => 'required|string|min:7',
			'city_id' => 'required',
			'region_id' => 'required',
			'type_vehicle' => 'required',
			'license_number' => 'sometimes',
			'license_expiration_date' => 'sometimes',
			'number_of_passengers' => 'sometimes',
			'password' => 'sometimes|nullable',
			'image' => 'required|file|mimes:jpeg,png,jpg,webp,gif,bmp,heic,heif,svg',
			'license_front_image' => 'required|file',
			'license_back_image' => 'required|file',
			'ci_front_image' => 'required|file',
			'ci_back_image' => 'required|file',
			'number_plate' => 'required|string|min:2',
			'vehicle_image' => 'required|file',
			'side_image' => 'required|file',
			'vehicle_brand_id' => 'required',
			'ci_number' => 'sometimes',
			'rubro_id' => 'sometimes',

			// 'organization_id' => 'required',
			//'gender' => 'required',
			// 'city_id_vehicle' => 'required',

			// 'vehicle_model_id' => 'required',
			// 'color' => 'required',

			// 'rua' => 'required',



			// 'number_of_bank' => 'required',
			// 'bank_id' => 'required',
			// 'name_titular' => 'required',
			// 'ci_number_titular' => 'required',
			// 'ci_front_image_titular' => 'required',
			// 'ci_back_image_titular' => 'required',

		], [
			'first_name.required' => "El nombre es requerido.",
			'first_name.min' => "El nombre debe tener al menos 2 caracteres.",
			'last_name.required' => "El apellido es requerido.",
			'last_name.min' => "El apellido debe tener al menos 2 caracteres.",
			'email.required' => "El correo electrónico es requerido.",
			'email.email' => "Ingrese un correo electrónico válido.",
			'cellphone.required' => "Por favor ingrese su numero de celular.",
			'cellphone.min' => "El celular debe tener al menos 7 dígitos.",
			'city_id.required' => "Por favor seleccione el municipio.",
			'region_id.required' => "Por favor seleccione el departamento.",
			'type_vehicle.required' => "Seleccione el tipo de vehículo.",
			'image.required' => 'La foto de perfil es requerida.',
			'image.mimes' => 'El formato de la imagen debe ser JPEG, PNG o JPG.',
			'license_front_image.required' => 'La foto del Brevete/Licencia (frente) es requerida.',
			'license_back_image.required' => 'La foto del Brevete/Licencia (reverso) es requerida.',
			'ci_front_image.required' => 'La foto del CI (anverso) es requerida.',
			'ci_back_image.required' => 'La foto del CI (reverso) es requerida.',
			'number_plate.required' => "Por favor registre el número de placa.",
			'number_plate.min' => "La placa debe tener al menos 2 caracteres.",
			'vehicle_image.required' => "La foto delantera del vehículo es requerida.",
			'side_image.required' => "La foto de costado del vehículo es requerida.",
			'vehicle_brand_id.required' => "Seleccione la Marca de su vehículo.",
			// 'vehicle_model_id.required' => "Seleccione el Modelo de su vehículo.",
			// 'color.required' => "Debe seleccionar un color.",

			// 'rua.required'=>"Debe introducir su RUAT.",

			// 'gender.required' => "Debe introducir su género.",
			'ci_number.required' => "Debe introducir este campo.",

			// 'number_of_bank.required'=>"Debe introducir este campo.",
			// 'bank_id.required'=>"Debe introducir este campo.",
			// 'name_titular.required'=>"Debe introducir este campo.",
			// 'ci_number_titular.required'=>"Debe introducir este campo.",
			// 'ci_front_image_titular.required'=>"Debe introducir este campo.",
			// 'ci_back_image_titular.required'=>"Debe introducir este campo.",

		]);

		if ($validator->fails()) {
			$errors = $validator->errors();
			\Log::warning('VALIDACION FALLIDA:', $errors->toArray());
			return redirect('/customer-admin/register-driver')->withErrors($validator)
				->with('message_error', 'Debe completar los campos requeridos.')->withInput();
		}

		\Log::info('Validacion OK - procesando registro...');

		try {
			$userExist = \App\User::where('email',  $request->input('email'))->first();
			\Log::info('Usuario existente: ' . ($userExist ? 'SI (ID:'.$userExist->id.')' : 'NO'));

			if ($userExist) {

				$driverExist = \App\Driver::where('user_id', $userExist->id)->first();

				if ($driverExist) {

					$vehicleExist =  \App\DriverVehicle::where('parent_id', $driverExist->id)->first();

					if (count($vehicleExist) == 0) {
						$newDriverVehicle = $this->driverVehicle($request, $driverExist->id);
						$userExist->role_user()->attach(5);
						$userExist->role_user()->detach(2);
						return redirect('/customer-admin/register-driver/step3')->with('message_success', 'El conductor se registro correctamente.');
					} else {
						return redirect('/customer-admin/register-driver')->with('message_error', 'El conductor con ese correo ya se encuentra registrado.')->withInput();
					}
				} else {
					$newDriver = $this->createDriver($request, $userExist);
					$newDriverVehicle = $this->driverVehicle($request, $newDriver->id);
					$userExist->role_user()->detach(2);
					$userExist->role_user()->attach(5);
					return redirect('/customer-admin/register-driver/step3')->with('message_success', 'El conductor se registro correctamente.');
				}
			} else {

				// Auto-generar contraseña si no fue enviada desde el formulario
				$autoPassword = $request->input('password') ?: ($request->input('cellphone') . rand(1000, 9999));

				// * Register User y Customer
				$customer = \Customer::generateCustomer(null, $request->input('email'), [
					'first_name' => $request->input('first_name'),
					'last_name' => $request->input('last_name')
				], $autoPassword);
				\Log::info('Driver auto-password generated for: ' . $request->input('email'));

				$user = $customer->fresh()->user;
				if (!$user) {
					\Log::error('generateCustomer no creó el User para email: ' . $request->input('email'));
					return redirect('/customer-admin/register-driver')->with('message_error', 'Error al crear la cuenta de usuario. Intente nuevamente.')->withInput();
				}
				$user->name = $request->input('first_name') . ' ' . $request->input('last_name');

				$user->first_name = $request->input('first_name');
				$user->last_name = $request->input('last_name');
				$user->email =  $request->input('email');
				$user->cellphone =  $request->input('cellphone');
				$user->city_id =  $request->input('city_id');
				$user->is_verify = 0;
				$user->gender = isset($request->gender) ? $request->gender : 'male';
				$user->image = \Asset::upload_image($request->file('image'), 'user-image', null);
				$user->save();
				\Log::info('Usuario guardado OK - ID: ' . $user->id);
				$user->role_user()->detach(2);
				$user->role_user()->attach(5);

				$newDriver = $this->createDriver($request, $user);
				\Log::info('Driver creado OK - ID: ' . $newDriver->id);

				$this->driverVehicle($request, $newDriver->id);
				\Log::info('Vehiculo registrado OK para driver: ' . $newDriver->id);
				\Log::info('========== REGISTRO COMPLETADO EXITOSAMENTE ==========');
				return redirect('/customer-admin/register-driver/step3/');
			}
		} catch (\Throwable $th) {
			\Log::error('ERROR EN REGISTRO: ' . $th->getMessage());
			\Log::error($th->getTraceAsString());
			return redirect('/customer-admin/register-driver')->with('message_error', 'Ocurrió un error en el servidor. ' . $th->getMessage());
		}
	}

	public function updateDriver(Request $request)
	{

		$validator = \Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required',
			'cellphone' => 'required',
			'city_id' => 'required',
			'password' => 'required',
			'organization_id' => 'required',
			// 'gender' => 'required',
			'image' => 'required|image|mimes:jpeg,png,jpg',
			'license_front_image' => 'required',
			'license_back_image' => 'required',

			'license_number' => 'sometimes',
			'license_expiration_date' => 'sometimes',
			'number_of_passengers' => 'sometimes',

			'ci_front_image' => 'sometimes',
			'ci_back_image' => 'sometimes',

			//'number_plate' => 'required',
			'vehicle_image' => 'sometimes',
			'vehicle_brand_id' => 'required',

			'side_image' => 'sometimes',
			// 'vehicle_model_id' => 'required',
			'color' => 'sometimes',
			'ci_number' => 'sometimes',
			'rubro' => 'sometimes',
			// 'ci_exp' => 'required',
			// 'rua' => 'required',

		], [
			'first_name.required' => "El nombre es requerido.",
			'password.required' => "La contraseña es requerida.",
			'last_name.required' => "El apellido es requerido.",
			'organization_id.required' => "La empresa es requerida, si no tiene una empresa debe asignarse como libre",
			'email.required' => "El correo electrónico es requerido.",
			'cellphone.required' => "Por favor ingrese su numero de celular.",
			'city_id.required' => "Por favor seleccione la ciudad.",
			'license_number.required' => "Por favor registre el número de licencia.",
			'license_expiration_date.required' => "Seleccione la fecha de caducidad de la licencia.",

			'image.required' => 'La imagen es requerida.',
			'image.image' => 'El archivo debe ser una imagen.',
			'image.mimes' => 'El formato de la imagen debe ser JPEG, PNG o JPG.',

			'license_front_image.required' => 'La imagen de la licencia es requerida.',
			'license_back_image.required' => 'La imagen de la licencia es requerida.',
			'ci_front_image.required' => 'La imagen de su CI es requerida.',
			'ci_back_image.required' => 'La imagen de su CI es requerida.',

			'city_id_vehicle.required' => "Por favor seleccione la ciudad.",
			//'number_plate.required' => "Por favor registre el número de placa.",
			'vehicle_image.required' => "Debe ingresar la imagen de su vehículo.",
			'side_image.required' => "Debe ingresar la imagen de su vehículo.",
			'vehicle_brand_id.required' => "Seleccione la Marca de su vehículo.",
			// 'vehicle_model_id.required' => "Seleccione el Modelo de su vehículo.",
			'color.required' => "Debe seleccionar un color.",
			// 'rua.required'=>"Debe introducir su RUAT.",
			// 'gender.required' => "Debe introducir su género.",
			// 'ci_number.required' => "Debe introducir este campo.",
			// 'ci_exp.required'=>"Debe introducir este campo.",

		]);

		if ($validator->fails()) {
			$errors = $validator->errors();
			\Log::info("ERRORESSSS");
			\Log::info($errors);
			return redirect('/customer-admin/register-driver/step1-update/' . $request->input('driver_id') . '')->withErrors($validator)
				->with('message_error', 'Debe completar los campos requeridos.');
		}

		try {
			$userExist = \App\User::where('email',  $request->input('email'))->first();
			if ($userExist) {
				$driverExist = \App\Driver::where('id',  $request->input('driver_id'))->first();
				if ($driverExist) {
					$this->updateDriverModel($request, $driverExist->id);

					if (count($driverExist->driver_vehicles) == 0) {
						$driverVehicle = $this->driverVehicle($request, $driverExist->id);
						if ($driverVehicle) return redirect('/customer-admin/register-driver/step3/');
						else {
							\Log::info($driverVehicle);
							return redirect('/customer-admin/register-driver/step3')->with('message_error', 'No se pudo crear el vehículo contactese con soporte.');
						}
					} else {
						return redirect('/customer-admin/register-driver/step3/')->with('message_error', 'Usted ya cuenta con una cuenta y un vehículo registrado.');
					}
				}
			} else {
				$this->postRegisterDriver($request);
			}
		} catch (\Throwable $th) {
			\Log::info("EXCEPTION");
			\Log::info($th);
			return redirect('/customer-admin/register-driver')->with('message_error', 'Ocurrió un error en el servidor. ' . $th->getMessage());
		}
	}

	public function createDriver(Request $request, $user)
	{
		try {
			// * Register Driver
			$newDriver = new \App\Driver;
			$newDriver->city_id = $request->input('city_id');
			$newDriver->organization_id = $request->input('organization_id') != null || $request->input('organization_id') != ''   ? $request->input('organization_id') : 4;
			$newDriver->first_name = $request->input('first_name');
			$newDriver->last_name = $request->input('last_name');
			$newDriver->user_id = $user->id;
			$newDriver->email = $request->input('email');
			$newDriver->cellphone = $request->input('cellphone');
			$newDriver->movil_number = 0;
			$newDriver->municipal_registry_operator = '';
			$newDriver->municipal_registry_operator_file = '';
			$newDriver->license_number = $request->input('license_number');
			$newDriver->license_expiration_date = isset($request->license_expiration_date) ? $request->license_expiration_date : date('Y-m-d', strtotime('+10 years'));
			$newDriver->active_trips = isset($request->is_active_for_career) ? true : true;
			$newDriver->baby_chair = $request->input('baby_chair') !== null  ? true : false;
			$newDriver->car_with_grill = $request->input('car_with_grill') !== null  ? true : false;
			$newDriver->travel_with_pets = $request->input('travel_with_pets') !== null  ? true : false;
			$newDriver->active_delivery = $request->input('active_delivery') !== null  ? true : false;
			$newDriver->fragile_content = $request->input('fragile_content') !== null  ? true : false;
			$newDriver->car_with_ac = $request->input('car_with_ac') !== null  ? true : false;
			$newDriver->car_electric = $request->input('car_electric') !== null  ? true : false;
			$newDriver->number_of_passengers = isset($request->number_of_passengers) ? $request->number_of_passengers : 5;
			$newDriver->gender = isset($request->gender) ? $request->gender : "male";
			$newDriver->ci_number = $request->input('ci_number');
			$newDriver->ci_exp = $request->input('ci_exp') || null;
			$newDriver->rubro = $request->input('rubro') || null;
			$newDriver->region_id = $request->input('region_id');

			// dd([$request->input('number_of_bank'), $request->input('name_titular'),  $request->input('ci_number_titular')]);

			$newDriver->bank_account_number = $request->input('number_of_bank');
			$newDriver->bank_id = $request->input('bank_id');
			$newDriver->name_titular = $request->input('name_titular');
			$newDriver->ci_number_titular = $request->input('ci_number_titular');

			$newDriver->active_send_money = $request->input('active_send_money') !== null  ? true : false;
			$newDriver->is_active_for_career = $request->input('is_active_for_career') !== null  ? true : true;
			$newDriver->tic = $request->input('tic') !== null  ? $request->input('tic') : '';

			// Conductor nuevo queda INACTIVO hasta que el admin lo apruebe
			$newDriver->active = 0;

			// * IMAGENES - Procesadas directamente
			$imageFields = [
				'image' => 'driver-image',
				'license_front_image' => 'driver-license_front_image',
				'license_back_image' => 'driver-license_back_image',
				'ci_front_image' => 'driver-ci_front_image',
				'ci_back_image' => 'driver-ci_back_image',
				'ci_front_image_titular' => 'driver-ci_front_image_titular',
				'ci_back_image_titular' => 'driver-ci_back_image_titular',
				'tic_file' => 'driver-tic_file',
			];

			foreach ($imageFields as $field => $folder) {
				if ($request->hasFile($field)) {
					$file = $request->file($field);
					\Log::info("Procesando {$field}: original={$file->getClientOriginalName()}, mime={$file->getMimeType()}, size={$file->getSize()}");
					try {
						$result = \Asset::upload_image($file, $folder, null);
						if ($result) {
							$newDriver->$field = $result;
							\Log::info("{$field} subida OK via Asset: {$result}");
						} else {
							// Fallback: subir a S3 vía Storage::put
							$ext = $file->getClientOriginalExtension() ?: 'jpg';
							$filename = $folder . '_' . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20) . '.' . $ext;
							$sizes = ['normal', 'mini'];
							foreach ($sizes as $sizeCode) {
								\Storage::put($folder . '/' . $sizeCode . '/' . $filename, file_get_contents($file->getRealPath()));
							}
							$newDriver->$field = $filename;
							\Log::info("{$field} subida por fallback S3: {$filename}");
						}
					} catch (\Throwable $imgErr) {
						\Log::error("{$field} error Asset: " . $imgErr->getMessage());
						// Fallback: subir a S3 vía Storage::put
						try {
							$ext = $file->getClientOriginalExtension() ?: 'jpg';
							$filename = $folder . '_' . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20) . '.' . $ext;
							$sizes = ['normal', 'mini'];
							foreach ($sizes as $sizeCode) {
								\Storage::put($folder . '/' . $sizeCode . '/' . $filename, file_get_contents($file->getRealPath()));
							}
							$newDriver->$field = $filename;
							\Log::info("{$field} subida por fallback S3 tras error: {$filename}");
						} catch (\Throwable $s3Err) {
							\Log::error("{$field} fallback S3 también falló: " . $s3Err->getMessage());
						}
					}
				}
			}
			
			$newDriver->save();
			\Log::info("Driver creado y guardado con imágenes - ID: {$newDriver->id}");
			return $newDriver;
		} catch (\Throwable $th) {
			\Log::error('createDriver error: ' . $th->getMessage());
			throw $th;
		}
	}



	public function updateDriverModel(Request $request, $driverID)
	{

		try {
			$newDriver = \App\Driver::where('id', $driverID)->first();
			$user = \App\User::where('id', $newDriver->user_id)->first();
			if (!$newDriver) return redirect('/customer-admin/register-driver')->with('message_error', 'Este driver no existe registrado.');
			// * Register Driver
			$newDriver->city_id = $request->input('city_id_vehicle');
			$newDriver->organization_id = $request->input('organization_id') != null || $request->input('organization_id') != ''   ? $request->input('organization_id') : 4;
			$newDriver->first_name = $request->input('first_name');
			$newDriver->last_name = $request->input('last_name');
			$newDriver->user_id = $user->id;
			$newDriver->email = $request->input('email');
			$newDriver->cellphone = $request->input('cellphone');
			$newDriver->movil_number = 0;
			$newDriver->municipal_registry_operator = '';
			$newDriver->municipal_registry_operator_file = '';
			$newDriver->license_number = $request->input('license_number');
			$newDriver->license_expiration_date = $request->input('license_expiration_date');
			$newDriver->active_trips = $request->input('is_active_for_career') !== null  ? true : false;
			$newDriver->baby_chair = $request->input('baby_chair') !== null  ? true : false;
			$newDriver->car_with_grill = $request->input('car_with_grill') !== null  ? true : false;
			$newDriver->travel_with_pets = $request->input('travel_with_pets') !== null  ? true : false;
			$newDriver->active_delivery = $request->input('active_delivery') !== null  ? true : false;
			$newDriver->fragile_content = $request->input('fragile_content') !== null  ? true : false;
			$newDriver->car_with_ac = $request->input('car_with_ac') !== null  ? true : false;
			$newDriver->car_electric = $request->input('car_electric') !== null  ? true : false;
			$newDriver->number_of_passengers = $request->input('number_of_passengers');
			$newDriver->gender = $request->input('gender');
			$newDriver->ci_number = $request->input('ci_number');
			$newDriver->ci_exp = $request->input('ci_exp');
			$newDriver->active_send_money = $request->input('active_send_money') !== null  ? true : false;
			$newDriver->is_active_for_career = $request->input('is_active_for_career') !== null  ? true : false;
			$newDriver->tic = $request->input('tic') !== null  ? $request->input('tic') : '';

			// * IMAGENES 
			// dd($request->file('image'));
			$newDriver->image = \Asset::upload_image($request->file('image'), 'driver-image', null);
			$newDriver->license_front_image = \Asset::upload_image($request->file('license_front_image'), 'driver-license_front_image', null);
			$newDriver->license_back_image = \Asset::upload_image($request->file('license_back_image'), 'driver-license_back_image', null);
			$newDriver->ci_back_image = \Asset::upload_image($request->file('ci_back_image'), 'driver-ci_back_image', null);
			$newDriver->ci_front_image =  \Asset::upload_image($request->file('ci_front_image'), 'driver-ci_front_image', null);

			$newDriver->tic_file =  $request->file('tic_file') !== null ? \Asset::upload_file($request->file('tic_file'), 'driver-tic_file', null) : null;
			$newDriver->save();
			return $newDriver;
		} catch (\Throwable $th) {
			\Log::error('updateDriverModel error: ' . $th->getMessage());
			throw $th;
		}
	}


	public function postRegisterVehicle(Request $request)
	{
		try {
			$driver = \App\Driver::where('id', $request->input('driverId'))->first();
			if (!$driver) return redirect('/customer-admin/register-driver')
				->with('message_error', 'El conductor con ese ID: no existe. Debe registrar los datos de Conductor.');

			$validator = \Validator::make($request->all(), [
				// 'city_id' => 'required',
				'number_plate' => 'required',
				'vehicle_image' => 'sometimes',
				// 'side_image' => 'required',
				'vehicle_brand_id' => 'required',
				// 'vehicle_model_id' => 'required',
				// 'color' => 'required',
				// 'rua' => 'required',
			], [
				// 'city_id.required' => "Por favor seleccione la ciudad.",
				'number_plate.required' => "Por favor registre el número de placa.",
				'vehicle_image.required' => "Debe ingresar la imagen de su vehículo.",
				'side_image.required' => "Debe ingresar la imagen de su vehículo.",
				'vehicle_brand_id.required' => "Seleccione la Marca de su vehículo.",
				'vehicle_model_id.required' => "Seleccione el Modelo de su vehículo.",
				'color.required' => "Debe seleccionar un color.",
				'rua.required' => "Debe introducir su RUAT.",
			]);


			if ($validator->fails()) {
				$errors = $validator->errors();
				\Log::info($errors);
				return redirect('/customer-admin/register-driver')->withErrors($validator)
					->with('message_error', 'Debe completar los campos requeridos. del auto/moto');
			}

			$this->driverVehicle($request, $driver->id);
			return redirect('/customer-admin/register-driver/step3/');
		} catch (\Throwable $th) {
			return redirect('/customer-admin/register-driver')->with('message_error', 'Ocurrió un error en el servidor. ' . $th->getMessage());
		}
	}

	public function driverVehicle(Request $request, $driverId)
	{
		try {
			$vehicle_brand = \App\VehicleBrand::find($request->input('vehicle_brand_id'));

			$type_vehicle = $vehicle_brand ? $vehicle_brand->type_vehicle : "moto";
			\Log::info("tipo_vehicle__" . $type_vehicle);
			$newVehicle = new \App\DriverVehicle;
			$newVehicle->city_id = $request->input('city_id');
			$newVehicle->parent_id = $driverId != null || $driverId != '' ? $driverId : $request->input('driver_id');
			$newVehicle->number_plate = $request->input('number_plate');
			$newVehicle->vehicle_brand_id = $request->input('vehicle_brand_id');
			$vehicleModelId = $request->input('vehicle_model_id');
			$newVehicle->vehicle_model_id = (!empty($vehicleModelId)) ? $vehicleModelId : null;
			$newVehicle->color = $request->input('color') !== null ? $request->input('color') : '#ffffff';
			$newVehicle->model_year = $request->input('model_year') !== null ? $request->input('model_year') : '';
			$validTypes = ['vagoneta', 'multiuso', 'convertible', 'descapotable'];
			$typeInput = $request->input('type');
			$newVehicle->type = in_array($typeInput, $validTypes) ? $typeInput : 'vagoneta';
			$newVehicle->tmov = $request->input('tmov') !== null ? $request->input('tmov') : '';
			$newVehicle->chassis_number = $request->input('chassis_number') !== null ? $request->input('chassis_number') : '';
			$newVehicle->rua = $request->input('rua') !== null ? $request->input('rua') : 0;
			$newVehicle->active = true;

			// Imágenes del vehículo - procesadas directamente
			$vehicleImageFields = [
				'vehicle_image' => 'driver-vehicle-vehicle_image',
				'side_image' => 'driver-vehicle-side_image',
				'rua_image' => 'driver-vehicle-rua_image',
			];

			foreach ($vehicleImageFields as $field => $folder) {
				if ($request->hasFile($field)) {
					$file = $request->file($field);
					\Log::info("Procesando vehiculo {$field}: original={$file->getClientOriginalName()}, mime={$file->getMimeType()}, size={$file->getSize()}");
					try {
						$result = \Asset::upload_image($file, $folder, null);
						if ($result) {
							$newVehicle->$field = $result;
							\Log::info("Vehiculo {$field} subida OK: {$result}");
						} else {
							$ext = $file->getClientOriginalExtension() ?: 'jpg';
							$filename = $folder . '_' . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20) . '.' . $ext;
							$sizes = ['normal', 'mini'];
							foreach ($sizes as $sizeCode) {
								\Storage::put($folder . '/' . $sizeCode . '/' . $filename, file_get_contents($file->getRealPath()));
							}
							$newVehicle->$field = $filename;
							\Log::info("Vehiculo {$field} subida por fallback S3: {$filename}");
						}
					} catch (\Throwable $imgErr) {
						\Log::error("Vehiculo {$field} error: " . $imgErr->getMessage());
						try {
							$ext = $file->getClientOriginalExtension() ?: 'jpg';
							$filename = $folder . '_' . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20) . '.' . $ext;
							\Storage::put($folder . '/normal/' . $filename, file_get_contents($file->getRealPath()));
							$newVehicle->$field = $filename;
							\Log::info("Vehiculo {$field} subida por fallback S3 tras error: {$filename}");
						} catch (\Throwable $s3Err) {
							\Log::error("Vehiculo {$field} fallback S3 falló: " . $s3Err->getMessage());
						}
					}
				}
			}

			$newVehicle->save();
			\Log::info("Vehiculo guardado OK - ID: {$newVehicle->id}, parent_id: {$driverId}");

			return true;
		} catch (\Throwable $th) {
			\Log::error("driverVehicle error: " . $th->getMessage());
			echo $th;
			return false;
		}
	}

	public function modelByBrand($brandId)
	{
		try {
			\Log::info('modelByBrand - Searching for brand ID: ' . $brandId);
			
			$models = \App\VehicleModel::where('vehicle_brand_id', $brandId)->get();
			
			\Log::info('modelByBrand - Found ' . count($models) . ' models for brand ID: ' . $brandId);
			
			// Si no encuentra, loguear información
			if (count($models) == 0) {
				$all_brands = \App\VehicleModel::distinct()->pluck('vehicle_brand_id');
				\Log::warning('modelByBrand - No models found. Available brand IDs: ' . json_encode($all_brands));
			}
			
			return ['status' => true, 'message' => 'Modelos obtenidos con éxito', 'data' => $models];
		} catch (\Throwable $th) {
			\Log::error('modelByBrand error: ' . $th->getMessage());
			return ['status' => false, 'message' => 'Ocurrio un error en el server: ' . $th->getMessage()];
		}
	}

	public function brandsByType($type)
	{
		try {
			\Log::info('brandsByType - Searching for type: ' . $type);
			
			// Buscar por el campo vehicle_type (no type_vehicle)
			$brands = \App\VehicleBrand::where('vehicle_type', $type)->where('active', 1)->get();
			
			\Log::info('brandsByType - Found ' . count($brands) . ' brands for type: ' . $type);
			
			// Si no encuentra, loguear todos los tipos disponibles
			if (count($brands) == 0) {
				$all_types = \App\VehicleBrand::distinct()->pluck('vehicle_type');
				\Log::warning('brandsByType - No brands found. Available types: ' . json_encode($all_types));
			}
			
			return ['status' => true, 'message' => 'Marcas obtenidas con éxito', 'data' => $brands];
		} catch (\Throwable $th) {
			\Log::error('brandsByType error: ' . $th->getMessage());
			return ['status' => false, 'message' => 'Ocurrio un error en el server: ' . $th->getMessage()];
		}
	}

	public function deleteDriver($driverId)
	{
		try {
			$driver = \App\Driver::where('id', $driverId)->first();

			\App\DriverActivation::where('parent_id', $driverId)->delete();
			\App\DriverRating::where('parent_id', $driverId)->delete();
			\App\DriverRequest::where('driver_id', $driverId)->delete();
			\App\DriverVehicle::where('parent_id', $driverId)->delete();
			\App\Ride::where('driver_id', $driverId)->delete();
			\App\DriverPaymentMethod::where('parent_id', $driverId)->delete();
			\App\DriverDeviceCode::where('parent_id', $driverId)->delete();
			\App\Driver::where('id', $driverId)->delete();
			\Solunes\Customer\App\Customer::where('user_id', $driver->user_id)->delete();
			\App\User::where('id', $driver->user_id)->delete();

			return redirect('/customer-admin/model-list/driver')->with('message_success', 'El Conductor se eliminó correctamente');
		} catch (\Throwable $th) {
			return redirect('/customer-admin/register-driver')->with('message_error', 'Ocurrió un error en el servidor. ' . $th->getMessage());
		}
	}


	public function driverDevices()
	{
		$driverCodes = \App\DriverDeviceCode::with('driver')->get();
		return view('content.register.devices-code', ['drivercodes' => $driverCodes]);
	}

	public function deleteDeviceCodes($id)
	{
		try {
			\App\DriverDeviceCode::where('id', $id)->delete();
			return redirect('/customer-admin/device-code')->with('message_success', 'Dispositivo Eliminado');
		} catch (\Throwable $th) {
			return redirect('/customer-admin/device-code')->with('message_error', 'Ocurrió un error en el servidor. ' . $th->getMessage());
		}
	}

	public function pageCreatePassenger()
	{

		return view('content.register.user-register');
	}

	public function pageUpdatePassenger($id)
	{
		$user = \App\User::where('id', $id)->first();
		return view('content.register.user-update', ['user' => $user]);
	}

	public function changeField($id)
	{
		try {
			$field = request('field');
			$driver = \App\Driver::where('id', $id)->first();
			if (!$driver) return redirect('/customer-admin/model-list/driver')->with('message_error', 'El conductor con ese id no existe ');
			$fieldChange = !$driver[$field];
			// dd($fieldChange);
			\App\Driver::where('id', $id)->update([$field => $fieldChange]);
			return redirect('/customer-admin/model-list/driver')->with('message_success', 'Check actualizado');
		} catch (\Throwable $th) {
			return redirect('/customer-admin/model-list/driver')->with('message_error', 'Ocurrió un error en el servidor. ' . $th->getMessage());
		}
	}

	public function sendEmailLibelula($id)
	{
		try {
			$field = request('field');
			$driver = \App\Driver::where('id', $id)->first();
			if ($driver->ci_number == null) return redirect('/customer-admin/model-list/driver')->with('message_error', 'Para enviar el correo a Libélula se necesita el Nro de carnet de identidad del conductor.');
			if ($driver->bank_id == null) return redirect('/customer-admin/model-list/driver')->with('message_error', 'Para enviar el correo a Libélula se necesita la entidad bancaria del conductor.');
			if ($driver->bank_account_number == null) return redirect('/customer-admin/model-list/driver')->with('message_error', 'Para enviar el correo a Libélula se necesita la cuenta bancaria del conductor.');


			\SpecialFun::send_email('Solicitud para habilitacion de appkey', [
				'carlosmarcelotorresvargas@gmail.com',
				'Solicitud para habilitacion de appkey',
				'Nombre: ' . $driver->first_name . ' ' . $driver->last_name . ' Documento de identidad: ' . $driver->ci_number . ' ' . $driver->ci_exp . ' Banco: ' . $dirver->bank->name . ' Nro de cuenta: ' . $driver->bank_account_number . ''
			]);

			\App\Driver::where('id', $id)->update(['send_email_libelula' => true]);
			return redirect('/customer-admin/model-list/driver')->with('message_success', 'Se envió un correo electrónico a Libélula');
		} catch (\Throwable $th) {
			return redirect('/customer-admin/model-list/driver')->with('message_error', 'Ocurrió un error en el servidor. ' . $th->getMessage());
		}
	}

	public function getCities()
	{
		try {
			$cities = \Solunes\Business\App\City::get();
			return ['status' => true, 'message' => 'correcto', 'data' => $cities];
		} catch (\Throwable $th) {
			return ['status' => false, 'message' => 'Ocurrió un error en el servidor. ' . $th->getMessage(), 'data' => []];
		}
	}
	public function getRoles()
	{
		try {
			$roles = \Solunes\Master\App\Role::get();
			$array = [];
			for ($i = 0; $i < count($roles); $i++) {
				$array[] = [
					'id' => $roles[$i]->id,
					'name' => $roles[$i]->display_name
				];
			}

			return ['status' => true, 'message' => 'correcto', 'data' => $array];
		} catch (\Throwable $th) {
			return ['status' => false, 'message' => 'Ocurrió un error en el servidor. ' . $th->getMessage(), 'data' => []];
		}
	}


	public function imageSave(Request $request)
	{
		try {
			$node = \Solunes\Master\App\Node::where('name', $request->input('node'))->first();
			if (!$node) return ['status' => false, 'message' => 'El modelo no existe: ' . $request->input('node')];
			$image = $request->file('imagen');
			$newImage = \Asset::upload_image($image, $request->input('folder'), null);

			$model = $node->model::where('id', $request->input('itemId'))->first();
			$model[$request->input('attribute')] = $newImage;
			$model->save();

			return ['status' => true, 'message' => 'Imagen actualizada.', 'data' => $request->input('itemId')];
		} catch (\Throwable $th) {
			return ['status' => false, 'message' => 'Ocurrió un error en el servidor. ' . $th->getMessage()];
		}
	}

	public function organizationByCity($id)
	{
		try {
			$organizations = \App\Organization::where('city_id', $id)->where('type', 'company')->get();
			\Log::info("organizationByCity - Found " . count($organizations) . " organizations for city: {$id}");
			return ['status' => true, 'message' => 'Obtenido con éxito..', 'data' => $organizations];
		} catch (\Throwable $th) {
			\Log::error('organizationByCity error: ' . $th->getMessage());
			return ['status' => false, 'message' => 'Ocurrió un error en el servidor. ' . $th->getMessage()];
		}
	}

	public function citiesByRegion($id)
	{
		try {
			// Obtener ciudades usando el modelo Solunes
			// Nota: 'name' es un atributo traducido (Translatable), no exista en tabla cities
			$cities = \Solunes\Business\App\City::where('region_id', $id)->where('active', 1)->get();
			
			\Log::info("citiesByRegion - Found " . count($cities) . " cities for region: {$id}");
			
			return ['status' => true, 'message' => 'Ciudades obtenidas con éxito.', 'data' => $cities];
		} catch (\Throwable $th) {
			\Log::error('citiesByRegion error: ' . $th->getMessage());
			return ['status' => false, 'message' => 'Ocurrió un error en el servidor. ' . $th->getMessage()];
		}
	}
	public function assingMeDriver($id)
	{
		$user = auth()->user();
		$driver = \App\Driver::where('id', $id)->first();
		if (!$driver) return redirect('/customer-admin/model-list/driver')->with('message_error', 'El conductor con ese id no existe ');
		if (\App\Driver::where('user_belongs_to_id', $user->id)->count() >= 1000) {
			return redirect('/customer-admin/model-list/driver')->with('message_error', 'Este Usuario ya tiene asignado 1000 Conductores.');
		}
		$fieldChange = $driver['user_belongs_to_id'];
		if ($fieldChange > 0) {
			\App\Driver::where('id', $id)->update(['user_belongs_to_id' => null]);
		} else {
			\App\Driver::where('id', $id)->update(['user_belongs_to_id' => $user->id]);
		}

		return redirect('/customer-admin/model-list/driver')->with('message_success', 'Conductor actualizado.');
	}

	public function driverDetail($driverId)
	{
		// Redirigir al listado general para evitar el error de $menu_main no definido
		return redirect('/customer-admin/model/driver/view/' . $driverId);
	}

	public function approveDriver($driverId)
	{
		try {
			$driver = \App\Driver::findOrFail($driverId);
			$user = \App\User::find($driver->user_id);

			if (!$user) {
				return redirect()->back()->with('message_error', 'No se encontró el usuario asociado al conductor.');
			}

			// Verificar que tenga al menos un vehículo
			$vehicleCount = \App\DriverVehicle::where('parent_id', $driver->id)->count();
			if ($vehicleCount == 0) {
				return redirect()->back()->with('message_error', 'No se puede aprobar: el conductor debe tener al menos un vehículo.');
			}

			// Aprobar: marcar usuario como verificado y conductor como activo
			$user->is_verify = 1;
			$user->save();

			$driver->active = 1;
			$driver->approved_at = \Carbon\Carbon::now();
			$driver->free_trial_until = date('Y-m-d', strtotime('+30 days'));
			$driver->save();

			\Log::info("Conductor aprobado desde admin: Driver ID {$driver->id}, User ID {$user->id}, free_trial_until: {$driver->free_trial_until}");

			// Enviar notificación push al conductor
			$driverName = trim($driver->first_name . ' ' . $driver->last_name);
			$freeUntil = date('d/m/Y', strtotime($driver->free_trial_until));
			$pushTitle = '¡Bienvenido a AnDre!';
			$pushBody = "Hola {$driverName}, tu registro ha sido aprobado. ¡Ya estás registrado! Tienes 30 días GRATIS para usar la app hasta el {$freeUntil}. Después podrás recargar tu saldo.";

			if (!empty($user->token_firebase)) {
				$pushResult = \App\Helpers\FcmHelper::sendNotification(
					$user->token_firebase,
					$pushTitle,
					$pushBody,
					[
						'type' => 'driver_approved',
						'driver_id' => (string) $driver->id,
						'free_trial_until' => $driver->free_trial_until,
					]
				);
				\Log::info("Push notification resultado: " . json_encode($pushResult));
			} else {
				\Log::warning("Conductor {$driver->id} no tiene token Firebase, no se envió push.");
			}

			// Guardar notificación en base de datos (si existe la tabla)
			try {
				\DB::table('notifications')->insert([
					'user_id' => $user->id,
					'title' => $pushTitle,
					'message' => $pushBody,
					'type' => 'driver_approved',
					'created_at' => \Carbon\Carbon::now(),
					'updated_at' => \Carbon\Carbon::now(),
				]);
			} catch (\Throwable $notifErr) {
				\Log::warning("No se pudo guardar notificación en DB: " . $notifErr->getMessage());
			}

			return redirect('/customer-admin/model/driver/view/' . $driver->id)
				->with('message_success', "✅ Conductor aprobado exitosamente. 30 días gratis hasta el {$freeUntil}.");

		} catch (\Throwable $e) {
			\Log::error("Error al aprobar conductor {$driverId}: " . $e->getMessage());
			return redirect()->back()->with('message_error', 'Error al aprobar: ' . $e->getMessage());
		}
	}

	public function rejectDriver($driverId)
	{
		try {
			$driver = \App\Driver::findOrFail($driverId);
			$user = \App\User::find($driver->user_id);

			if ($user) {
				$user->is_verify = 0;
				$user->save();
			}

			$driver->active = 0;
			$driver->save();

			\Log::info("Conductor rechazado/bloqueado desde admin: Driver ID {$driver->id}");

			return redirect('/customer-admin/model/driver/view/' . $driver->id)
				->with('message_success', 'Conductor rechazado/bloqueado.');

		} catch (\Throwable $e) {
			\Log::error("Error al rechazar conductor {$driverId}: " . $e->getMessage());
			return redirect()->back()->with('message_error', 'Error al rechazar: ' . $e->getMessage());
		}
	}
}
