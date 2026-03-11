<?php 

namespace App\Helpers;

use Facebook\Facebook;

class Func {


    public static function generateOTP($otp_length = 6) {
        $digits = '0123456789';
        $OTP = '';
        for ($i = 0; $i < $otp_length; $i++) {
            $OTP .= $digits[random_int(0, 9)];
        }
        return $OTP;
    }

    public static function paymentGenerationProcessing($school_price, $school_registration, $date) {
      $strtotime = strtotime($date);
      $initial_year = date('Y', $strtotime);
      $initial_month = intval(date('m', $strtotime));
      $initial_day = intval(date('d', $strtotime));
      $amount = $school_price->amount;
      $detail = $school_registration->customer_dependant->name.' en '.$school_registration->school->name;
      $scolarship_discount = false; // Define si se incluye una beca
      $general_discount = false; // Define si ya fue afectado por algun descuento general por fechas
      $generate_payment = true; // Define si se genera el pago o no
      if($school_price->type=='inscription'){
        $name = 'Matrícula de '.$detail.' ('.$initial_year.'-'.($initial_year+1).')';
        if($school_registration->dont_pay_enrollment){
          $amount = 0;
          $name .= ' - Sin costo de mátricula';
        }
        // Si el estudiante tiene beca del 100%, no paga matricula
        if($school_registration->scolarship=='total'){
          $amount = 0;
          $scolarship_discount = true;
          $name .= ' - Mátricula incluida en beca total';
        }
        if(!$scolarship_discount){
          // Descuento del 100% si se hace en verano desde octubre a enero y del 50% si se hace en agosto o septiembre.
          if(in_array($initial_month, [1,10,11,12])){
            $amount = 0;
            $general_discount = true;
            $name .= ' - Descuento del 100% por beca';
          } else if(in_array($initial_month, [8,9])){
            $amount = $amount*0.5;
            $general_discount = true;
            $name .= ' - Descuento de 50% por beca';
          }
        }
      } else if($school_price->type=='monthly'){
        $name = 'Mensualidad de '.$detail.' ('.date('m/Y', $strtotime).')';
        // Aplicar Becas a estudiantes
        if($school_registration->scolarship=='half'){
          $amount = $amount * 0.5;
          $scolarship_discount = true;
          $name .= ' - Descuento de media beca';
        } else if($school_registration->scolarship=='total'){
          $amount = 0;
          $scolarship_discount = true;
          $name .= ' - Descuento de beca total';
        }
      } else if($school_price->type=='anual_insurance'){
        $name = 'Seguro de salud de '.$detail.' ('.$initial_year.'-'.($initial_year+1).')';
        // Si no requiere seguro, no se le cobra ni se genera deuda
        if(!$school_registration->insurance){
          $amount = 0;
          $generate_payment = false;
        }
      }
      if($generate_payment){
        $customer_payment = new \App\CustomerPayment;
        $customer_payment->customer_id = $school_registration->customer_dependant->parent_id;
        $customer_payment->customer_dependant_id = $school_registration->customer_dependant_id;
        $customer_payment->transaction_code = \Func::generateTransactionPaymentCode();
        $customer_payment->has_invoice = 1;
        $customer_payment->status = 'pending';
        $customer_payment->name = $name;
        $customer_payment->date = $school_registration->created_at->format('Y-m-d');
        $customer_payment->due_date = date( "Y-m-d", strtotime( $school_registration->created_at->format('Y-m-d')." +1 month" ) );;
        //$customer_payment->amount = $event->amount;
        if($amount==0){
          $customer_payment->status = 'paid';
          $customer_payment->paid_method = 'pagostt';
          $customer_payment->payment_date = $date;
        }
        $customer_payment->save();
        $payment_item = new \App\PaymentItem;
        $payment_item->customer_payment_id = $customer_payment->id;
        $payment_item->name = $name;
        $payment_item->quantity = 1;
        $payment_item->detail = $name;
        $payment_item->amount = $amount;
        $payment_item->save();
      }
    }

    public static function send_sms($number, $message, $sender = NULL, $transactional = false) {
      \Log::info('Trying to send SMS');
      $params = array(
        'credentials' => array(
          'key' => config('services.aws.key'),
          'secret' => config('services.aws.secret'),
        ),
       'region' => config('services.aws.region'), // < your aws from SNS Topic region
       'version' => 'latest'
      );
      $sns = new \Aws\Sns\SnsClient($params);
      if(!$sender){
        $sender = config('app.APP_NAME');
      }
      $type = 'Promotional';
      if($transactional){
        $type = 'Transactional';
      }
      $message = str_replace(
      array('á','é','í','ó','ú','ñ'),
      array('a','e','i','o','u','n'),
      $message);
      $message = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$message);
      $args = array(
        "Message" => $message,
        "PhoneNumber" => $number,
        "MessageAttributes" => [
          'AWS.SNS.SMS.SMSType'=>['DataType'=>'String','StringValue'=>$type],
          'AWS.SNS.SMS.SenderID'=>['DataType'=>'String','StringValue'=>$sender]
        ]
      );
      $result = $sns->publish($args)->get('MessageId');
      \Log::info('SMS Published Result: '.json_encode($result));
      return $result;
    }

    public static function generateTransactionPaymentCode() {
      //$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
      $array = [8,4,4,4,12];
      return \Func::generateRandomString($characters, $array);
    }

    public static function generateRandomString($characters, $array) {
      $charactersLength = strlen($characters);
      $randomString = '';
      foreach($array as $key => $length){
        if($key>0){
          $randomString .= '-';
        }
        for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
      }
      return $randomString;
    }

    public static function process_pdf_image($image) {
      $arrContextOptions=array(
                      "ssl"=>array(
                          "verify_peer"=>false,
                          "verify_peer_name"=>false,
                      ),
                  );
      $type = pathinfo($image, PATHINFO_EXTENSION);
      $avatarData = file_get_contents($image, false, stream_context_create($arrContextOptions));
      $avatarBase64Data = base64_encode($avatarData);
      $imageData = 'data:image/' . $type . ';base64,' . $avatarBase64Data;
      return $imageData;
    }

    public static function generate_card_pdf($file, $school_registration) {
        $pdf = \PDF::loadView('pdf.card-pdf', ['item'=>$school_registration->customer_dependant, 'school'=>$school_registration->school]);
        $pdf_response = $pdf->setOption('page-width',55)->setOption('page-height',86)->setOrientation('landscape')->save($file.'.pdf');
        $file_name = \Asset::upload_file($file.'.pdf', 'school-registration-card_file');
        unlink($file.'.pdf');
        return $file_name;
    }

    public static function send_certificate($participant) {
        $pathToFile = asset(\Asset::get_file('participant-certificate_file', $participant->certificate_file));
        \Mail::send('emails.notifications.certificate', ['item'=>$participant, 'file'=>$pathToFile], function ($m) use ($participant, $pathToFile) {
            $m->to([$participant->email])->subject('Seminarios 360 | Certificado de Participación');
            $m->attach($pathToFile);
        });
        return true;
    }

    public static function menu_link($item, $level) {
        if($item->url()==NULL) {
            $link_attributes = ''; 
            $link = '<a '.$link_attributes.'>'.$item->title.'';
        } else if($item->hasChildren()) {
            $link = '<a '.$item->attributes().' href="'.$item->url().'">'.$item->title.' <i class="fa fa-angle-down"></i> ';
        } else if($item->hasChildren()||$item->url()==url('productos')) {
            $link = '<a '.$item->attributes().' href="'.$item->url().'">'.$item->title.' <i class="fa fa-angle-down"></i> ';
        } else {
            $link = '<a '.$item->attributes().' href="'.$item->url().'">'.$item->title.'';
        }
        return  $link.'</a>';
    }

    public static function facebook_page_query($query, $page = NULL) {
        if($page==NULL){
            $page = \Config::get('services.facebook.page');
        }
        $fb = new Facebook([
          'app_id' => \Config::get('services.facebook.id'),
          'app_secret' => \Config::get('services.facebook.secret'),
          'default_graph_version' => 'v2.4',
          'default_access_token' => isset($_SESSION['facebook_access_token']) ? $_SESSION['facebook_access_token'] : \Config::get('services.facebook.id').'|'.\Config::get('services.facebook.secret')
        ]);
        $result = $fb->get('/'.$query);
        return $result->getGraphObject()->asArray();
    }

    public static function new_row($key, $columns) {
        if(is_int(($key+1)/$columns)){
            return '</div><div class="row">';
        } else {
            return false;
        }
    }

    public static function short_string($string, $lenght) {
    	if(strlen($string)>$lenght){
    	   $string = substr($string,0,$lenght-2).'..';
    	}
		return $string;
    }

    public static function share_link($social_network, $link) {
    	$link = str_replace(' ','%20',$link);
    	if($social_network=='facebook'){
    	    $url = 'https://www.facebook.com/sharer/sharer.php?u='.$link;
    	} else if($social_network=='twitter'){
    		$url = 'https://twitter.com/home?status='.$link;
    	}
		return $url;
    }

    public static function vardump() {
      $arg_list = func_get_args();
      foreach ( $arg_list as $variable ) {
        echo '<pre style="color: #000; background-color: #fff;">';
        echo htmlspecialchars( var_export( $variable, true ) );
        echo '</pre>';
      }
    }
}