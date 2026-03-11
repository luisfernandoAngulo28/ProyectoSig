<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use Validator;
use Asset;
use AdminList;
use AdminItem;
use PDF;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

class CustomAdminController extends Controller
{

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url)
	{
		$this->middleware('auth', ['except' => ['getCustomerRegister', 'postCustomerRegister']]);
		//$this->middleware('permission:dashboard');
		$this->prev = $url->previous();
		$this->module = 'custom-admin';
	}

	public function getIndex()
	{
		$array['activities'] = \App\Activity::with('node', 'user')->orderBy('created_at', 'DESC')->get()->take(20);
		$array['notifications'] = \Auth::user()->notifications->take(20);
		return view('list.dashboard', $array);
	}

	public function getRedirect()
	{
		if (!auth()->check()) {
			return redirect('auth/login')->with('message_error', 'Debe iniciar sesión.');
		}
		$user = auth()->user();
		if ($user->hasRole('subadmin')) {
			return redirect('admin/model-list/sale')->with('message_success', 'Inició sesión como subadministrador.');
		}
		if ($user->hasRole('admin')) {
			return redirect('admin/model-list/sale')->with('message_success', 'Inició sesión como administrador.');
		}
		return redirect('inicio');
	}

	public function getCustomerRegister()
	{
		$user = auth()->user();
		$array['i'] = NULL;
		$array['expeditions'] = ['LP' => 'LP', 'SC' => 'SC', 'CB' => 'CB', 'CH' => 'CH', 'TA' => 'TA', 'OR' => 'OR', 'PO' => 'PO', 'BE' => 'BE', 'PA' => 'PA', 'EXTRANJERO' => 'EXTRANJERO'];
		return view('content.create-customer', $array);
	}

	public function postCustomerRegister(Request $request)
	{
		if ($request->has('first_name') && $request->has('last_name') && $request->has('ci_number') && $request->has('ci_expedition') && $request->has('email') && $request->has('phone') && $request->has('address') && $request->has('member_code') && $request->has('nit_number') && $request->has('nit_name')) {

			$customer = \App\Customer::where('ci_number', $request->input('ci_number'))->orWhere('email', $request->input('email'))->orWhere('phone', $request->input('phone'))->first();
			if ($customer) {
				return redirect($this->prev)->with('message_error', 'Ya existe una cuenta con el CI, celular o email proporcionado, intente recuperar su contraseña.')->withInput();
			}

			$item = new \App\Customer;
			$item->full_name = $request->input('first_name') . ' ' . $request->input('last_name');
			$item->first_name = $request->input('first_name');
			$item->last_name = $request->input('last_name');
			$item->ci_number = $request->input('ci_number');
			$item->ci_expedition = $request->input('ci_expedition');
			$item->member_code = $request->input('member_code');
			$item->email = $request->input('email');
			$item->phone = $request->input('phone');
			$item->address = $request->input('address');
			$item->nit_number = $request->input('nit_number');
			$item->nit_name = $request->input('nit_name');
			$item->save();

			$user = $item->user;
			auth()->login($user);

			return redirect('admin/redirect')->with('message_success', '"' . $item->first_name . ' ' . $item->last_name . '" fue registrado correctamente.');
		} else {
			return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos para registrar su usuario.')->withInput();
		}
	}

	public function getMyAccounts()
	{
		$user = auth()->user();
		$customer = $user->customer;
		$array = ['customer' => $customer];
		return view('content.my-accounts', $array);
	}

	public function postEditPassword(Request $request)
	{
		$user = auth()->user();
		if ($user->customer) {
			$customer = $user->customer;
			$rules = \App\Customer::$rules_password;
			$validator = \Validator::make($request->all(), $rules);
			if ($validator->passes()) {
				$customer->member_code = $request->input('member_code');
				$customer->save();
				$user = $customer->user;
				$user->password = $request->input('member_code');
				$user->save();
				return redirect($this->prev)->with('message_success', 'Su contraseña fue editada correctamente.');
			} else {
				return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos.')->withInput();
			}
		} else {
			return redirect($this->prev)->with('message_error', 'No tiene una cuenta asociada.');
		}
	}

	public function getCustomerDependant()
	{
		$user = auth()->user();
		$array['i'] = NULL;
		return view('content.create-customer-dependant', $array);
	}

	public function postCustomerDependant(Request $request)
	{
		if (auth()->check() && auth()->user()->customer && $request->has('first_name') && $request->has('last_name') && $request->has('ci_number') && $request->has('birth_date') && $request->has('emergency_name') && $request->has('emergency_number')) {

			$customer = auth()->user()->customer;

			$item = new \App\CustomerDependant;
			$item->parent_id = $customer->id;
			$item->name = $request->input('first_name') . ' ' . $request->input('last_name');
			$item->first_name = $request->input('first_name');
			$item->last_name = $request->input('last_name');
			$item->ci_number = $request->input('ci_number');
			$item->birth_date = $request->input('birth_date');
			$item->emergency_name = $request->input('emergency_name');
			$item->emergency_number = $request->input('emergency_number');
			$item->save();

			return redirect('school-registrations')->with('message_success', '"' . $item->first_name . ' ' . $item->last_name . '" fue registrado correctamente.');
		} else {
			return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos para registrar el participante.')->withInput();
		}
	}

	public function getSchoolRegistrationList()
	{
		$array['items'] = [];
		$user = auth()->user();
		$customer = $user->customer;
		$season = \App\Season::where('active', 1)->first();
		if (count($customer->customer_dependants) > 0) {
			foreach ($customer->customer_dependants as $customer_dependant) {
				$array['items'][] = ['dependant' => $customer_dependant, 'registrations' => $customer_dependant->school_registrations()->where('season_id', $season->id)->get()];
			}
		}
		$array['customer'] = $customer;
		return view('content.my-school-registrations', $array);
	}

	public function getSchoolRegistration($customer_id, $customer_dependant_id)
	{
		$user = auth()->user();
		$customer = $user->customers()->where('id', $customer_id)->first();
		if (!$customer) {
			return redirect($this->prev)->with('message_error', 'No se encontró un cliente asociado.');
		}
		$array['customer'] = $customer;
		$customer_dependant = $customer->customer_dependants()->where('id', $customer_dependant_id)->first();
		if (!$customer_dependant) {
			return redirect($this->prev)->with('message_error', 'No se encontró un cliente dependiente asociado.');
		}
		$array['customer_dependant'] = $customer_dependant;
		$array['i'] = NULL;
		$array['schools'] = \App\School::lists('name', 'id')->toArray();
		return view('content.create-school-registration', $array);
	}

	public function postSchoolRegistration(Request $request)
	{
		if (auth()->check() && $request->has('school_id') && $request->has('insurance') && $request->has('customer_id') && $request->has('customer_dependant_id')) {

			$user = auth()->user();
			$school = \App\School::find($request->input('school_id'));
			if (!$school) {
				return redirect($this->prev)->with('message_error', 'No se encontró la escuela.')->withInput();
			}
			$customer = $user->customers()->where('id', $request->input('customer_id'))->first();
			if (!$customer) {
				return redirect($this->prev)->with('message_error', 'No se encontró un cliente asociado.')->withInput();
			}
			$customer_dependant = $customer->customer_dependants()->where('id', $request->input('customer_dependant_id'))->first();
			if (!$customer_dependant) {
				return redirect($this->prev)->with('message_error', 'No se encontró un cliente dependiente asociado.')->withInput();
			}

			$season = \App\Season::where('active', 1)->first();
			if (!$season) {
				return redirect($this->prev)->with('message_error', 'No hay una temporada activa.')->withInput();
			}
			$item = new \App\SchoolRegistration;
			$item->company_id = $school->company_id;
			$item->school_id = $school->id;
			$item->customer_id = $customer->id;
			$item->season_id = $season->id;
			$item->customer_dependant_id = $customer_dependant->id;
			$item->registration_year = date('Y');
			$item->insurance = $request->input('insurance');
			$item->other_insurance = $request->input('other_insurance');
			//$item->status = 'holding';
			$item->save();

			return redirect('school-registrations')->with('message_success', '"' . $customer_dependant->first_name . ' ' . $customer_dependant->last_name . '" fue registrado correctamente en la escuela "' . $school->name . '".');
		} else {
			return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos para registrar al hijo.')->withInput();
		}
	}

	public function getMyPayments()
	{
		$array['items'] = [];
		$user = auth()->user();
		$customer = $user->customer;
		if (count($customer->customer_dependants) > 0) {
			foreach ($customer->customer_dependants as $customer_dependant) {
				$array['items'][] = ['dependant' => $customer_dependant, 'payments' => $customer_dependant->pending_payments];
			}
		}
		$array['customer'] = $customer;
		return view('content.my-payments', $array);
	}

	public function getMyHistory()
	{
		$array['items'] = [];
		$user = auth()->user();
		$customer = $user->customer;
		if (count($customer->customer_dependants) > 0) {
			foreach ($customer->customer_dependants as $customer_dependant) {
				$array['items'][] = ['dependant' => $customer_dependant, 'payments' => $customer_dependant->paid_payments];
			}
		}
		$array['customer'] = $customer;
		return view('content.my-history', $array);
	}

	public function getManualPayment($id)
	{
		if ($item = \App\CustomerPayment::find($id)) {
			$item->transaction_payment_code = \Pagostt::generatePaymentCode();
			$item->payment_date = date('Y-m-d');
			$item->status = 'paid';
			$item->paid_method = 'manual';
			$item->save();
		}
		return redirect($this->prev)->with('message_success', 'Pago realizado correctamente.');
	}

	public function reportTotalesDrivers($view = NULL, $script = NULL)
	{
		$totalTimes = \App\DriverActivation::with('driver.user')
			->selectRaw('parent_id, 
					SUM(IF(status="active", TIME_TO_SEC(TIMEDIFF(end_time, initial_time)), 0)) AS total_active_time_sec,
					SEC_TO_TIME(SUM(IF(status="active", TIME_TO_SEC(TIMEDIFF(end_time, initial_time)), 0))) AS total_active_time,
					SUM(IF(status="busy", TIME_TO_SEC(TIMEDIFF(end_time, initial_time)), 0)) AS total_busy_time_sec,
					SEC_TO_TIME(SUM(IF(status="busy", TIME_TO_SEC(TIMEDIFF(end_time, initial_time)), 0))) AS total_busy_time,
					(SELECT SUM(rides.price) FROM rides WHERE rides.driver_id = driver_activations.parent_id) as total_price')
			->groupBy('parent_id')
			->get();

		if ($view == 'excel') {
			if ($script == 'script') {
				$dir = public_path('excel-report/' . date('Y-m-d') . '/totales-de-conductores');
			} else {
				$dir = public_path('excel');
			}
			array_map('unlink', glob($dir . '/*'));
			$file = \Excel::create('totales-de-conductores-' . date('Y-m-d'), function ($excel) use ($totalTimes) {
				$excel->sheet('totales-de-conductores', function ($sheet) use ($totalTimes) {
					$col_array[] = 'Id';
					$col_array[] = 'Nombre del Conductor';
					$col_array[] = 'Total de Horas Activas';
					$col_array[] = 'Total de Horas Inactivas';
					$col_array[] = 'Total Generado';

					$sheet->row(1, $col_array);
					$sheet->row(1, function ($row) {
						$row->setFontWeight('bold');
					});
					$sheet->freezeFirstRow();
					$count = 1;
					foreach ($totalTimes as $totalTime) {
						$col_array =  [];
						$count += 1;
						$col_array[] = $totalTime->driver->id;
						$col_array[] = $totalTime->driver->user->name;
						$col_array[] = $totalTime->total_active_time;
						$col_array[] = $totalTime->total_busy_time;
						$col_array[] = $totalTime->total_price;
						$sheet->row($count, $col_array);
					}
				});
			})->store('xlsx', $dir, true);
			if ($script == 'script') {
				return $file['full'];
			}
			return response()->download($file['full']);
		}
		if (request()->segment(1) === 'customer-admin') {
			return view('content.reportes.drivers-totals-subadmin', [
				'items' => $totalTimes
			]);
		} else {
			return view('content.reportes.drivers-totals-admin', [
				'items' => $totalTimes
			]);
		}
	}

	public function reportOrganizationTotalRequest($view = NULL, $script = NULL)
	{
		$totalSolicitudes = \App\Request::where('type', 'trip')->where('taxi_company', NULL)->count();
		$sinOrganization = [];

		$totalSolicitudesConOrganizacions = \App\Organization::withCount('request')
			->selectRaw('(SELECT COUNT(id) FROM requests WHERE requests.type = "trip" AND requests.taxi_company = organizations.id) as total_viajes')
			->get();

		if ($view == 'excel') {
			if ($script == 'script') {
				$dir = public_path('excel-report/' . date('Y-m-d') . '/totales-de-solicitudes');
			} else {
				$dir = public_path('excel');
			}
			array_map('unlink', glob($dir . '/*'));
			$file = \Excel::create('totales-de-solicitudes-' . date('Y-m-d'), function ($excel) use ($totalSolicitudesConOrganizacions) {
				$excel->sheet('totales-de-solicitudes', function ($sheet) use ($totalSolicitudesConOrganizacions) {
					$col_array[] = 'Id';
					$col_array[] = 'Nombre de la Empresa';
					$col_array[] = 'Total de Solicitudes';
					$col_array[] = 'Total Viajes';

					$sheet->row(1, $col_array);
					$sheet->row(1, function ($row) {
						$row->setFontWeight('bold');
					});
					$sheet->freezeFirstRow();
					$count = 1;
					foreach ($totalSolicitudesConOrganizacions as $totalSolicitudesConOrganizacion) {
						$col_array =  [];
						$count += 1;
						$col_array[] = $totalSolicitudesConOrganizacion->id;
						$col_array[] = $totalSolicitudesConOrganizacion->name;
						$col_array[] = $totalSolicitudesConOrganizacion->request_count;
						$col_array[] = $totalSolicitudesConOrganizacion->total_viajes;
						$sheet->row($count, $col_array);
					}
				});
			})->store('xlsx', $dir, true);
			if ($script == 'script') {
				return $file['full'];
			}
			return response()->download($file['full']);
		}
		if (request()->segment(1) === 'customer-admin') {
			return view('content.reportes.request-totals-subadmin', [
				'items' => $totalSolicitudesConOrganizacions
			]);
		} else {
			return view('content.reportes.request-totals-admin', [
				'items' => $totalSolicitudesConOrganizacions
			]);
		}
	}

	public function reportUserAmount($view = NULL, $script = NULL)
	{
		$totalPassengers = \App\Ride::where('status', 'END_TRIP')
			->select('user_id', \DB::raw('SUM(price) as total_amount'))
			->groupBy('user_id')
			->get();

		// $totalPassenger = \App\User::with('rides')->selectRaw('users.*, COALESCE(SUM(rides.price), 0) as monto_gastado')
		// 	->leftJoin('requests', 'users.id', '=', 'requests.user_id')
		// 	->leftJoin('rides', 'requests.id', '=', 'rides.parent_id')
		// 	->groupBy('users.id')
		// 	->get();

		if ($view == 'excel') {
			if ($script == 'script') {
				$dir = public_path('excel-report/' . date('Y-m-d') . '/users-monto-gastado');
			} else {
				$dir = public_path('excel');
			}
			array_map('unlink', glob($dir . '/*'));
			$file = \Excel::create('users-monto-gastado' . date('Y-m-d'), function ($excel) use ($totalPassengers) {
				$excel->sheet('users-monto-gastado', function ($sheet) use ($totalPassengers) {
					$col_array[] = 'Id';
					$col_array[] = 'Nombre de la persona';
					$col_array[] = 'Numero de Celular';
					$col_array[] = 'Monto Gastado';

					$sheet->row(1, $col_array);
					$sheet->row(1, function ($row) {
						$row->setFontWeight('bold');
					});
					$sheet->freezeFirstRow();
					$count = 1;
					foreach ($totalPassengers as $totalPassenger) {
						
						$user = \App\User::where('id', $totalPassenger->user_id)->first();
						$col_array =  [];
						$count += 1;
						$col_array[] = $user ? $user->id : null;
						$col_array[] = $user ? $user->name : null;
						$col_array[] = $user ? $user->cellphone : null;
						$col_array[] = $totalPassenger->total_amount;
						$sheet->row($count, $col_array);
					}
				});
			})->store('xlsx', $dir, true);
			if ($script == 'script') {
				return $file['full'];
			}
			return response()->download($file['full']);
		}
		if (request()->segment(1) === 'customer-admin') {
			return view('content.reportes.user-amount-subadmin', [
				'items' => $totalPassengers
			]);
		} else {
			return view('content.reportes.user-amount-admin', [
				'items' => $totalPassengers
			]);
		}
	}
}
