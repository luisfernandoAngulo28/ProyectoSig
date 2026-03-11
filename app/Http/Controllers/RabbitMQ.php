<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Helpers\SpecialFunc;

class RabbitMQ extends Controller
{
    private $connection;
   


    public function __construct() {
        
        $host = config('services.rabbit_mq.rabbit_host');
        $port = config('services.rabbit_mq.rabbit_port');
        $user = config('services.rabbit_mq.rabbit_user');
        $password = config('services.rabbit_mq.rabbit_password');
        $vhost = '/';
        try {
            $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        } catch (\Throwable $th) {
            echo "Error en la conexion con rabbit: ". $th;
            // throw new Error("Error en la conexion con rabbit", $th);   
        }
    }

    /* CONSUMIR MENSAJE DE RABBIT CON EXCHANGE*/

    public function consumerMessageExchange()
    {   
        $nameQueues = 'laravel.user.recover_password';
        // $exChange = 'my-exchange-nest';
        $exChange = 'domain_events';
        $exChangeType= 'topic';
        $key= 'socket.user.recover_password';

        $channel = $this->connection->channel();

        $channel->exchange_declare($exChange, $exChangeType, false, true, false);
        $channel->queue_declare($nameQueues, false, true, false, false);
        $channel->queue_bind($nameQueues, $exChange, $key);  //* unimos la cola al exchange


        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $channel->basic_consume($nameQueues, '', false, false, false, false, function($message) use ($channel) {
            
            $messageDecode = json_decode($message->body, true); // decodificamos el JSON
            $event = $messageDecode['data']['type'];
            // PROCESAMOS EL MENSAJE
            // var_dump($messageDecode);
            $this->processMessage( $event, $messageDecode['data']['attributes']);
            
            $channel->basic_ack($message->delivery_info['delivery_tag']);    
        });

        // * Inicia el bucle de recepción de mensajes
        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }

    // Funcion para procesar los mensajes
    public function processMessage($event, $dataMessage){
        switch($event){
            case 'upload-image':
                $response = \SpecialFunc::upload_image($dataMessage['urlImage'], $dataMessage['folder'], $dataMessage['nameImage']);
                echo "Url de la imagen: ". $response . "\n";
            break;
            case 'send-code-by-sms-twilo':
                echo "SMS enviado: \n";
                $response = \SpecialFunc::send_sms_twilo($dataMessage['number'], $dataMessage['message']);
                echo $response. "\n";
            break;
            case 'socket.user.recover_password':
                echo "enviando email: \n";
                // var_dump([$dataMessage['email']]);
                $response = \SpecialFunc::send_email_recover_password('correo@gmail.com');
                // $response = \SpecialFunc::send_email("Titulo del Email desde Nest", [$dataMessage['email']], 'Message Titulo', 'message content' );
                echo "Termino el proceso: ". $response. "\n";
            break;

            case 'payments':
                $response = \SpecialFunc::make_ride_sale(1,1,1,"Desde Hasta", 2, "Torres","123123");
                $response['event'] = 'response-payment';
                echo $response;
                publishMessagePayments($response);
            break;

        }
        return true;
    }

    public function paymentMethods (){
        $response = \SpecialFunc::make_ride_sale(1,1,1,"Desde Hasta", 2, "Torres","123123");
        var_dump($response);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publishMessagePayments($message)
    {
        $channel = $this->connection->channel();

        $nameQueues = 'payment-response';
        $exChange = 'exchange-payment';
        $exChangeType= 'topic';
        $key= 'my-exchange-payment';


        $channel->queue_declare($nameQueues, false, true, false, false);

        for ($i=0; $i <= 6; $i++) { 
            
            $msg = new AMQPMessage($message);

            $channel->basic_publish($msg, '', $nameQueues);

            echo " [x] Sent 'Hello World desde Laravel!'.$i.'</br>' \n";
            sleep(1);
        }

        $channel->close();
        $this->connection->close();
    }


    // public function publishMessageExchange()
    // {
    //     $channel = $this->connection->channel();

    //     $channel->exchange_declare($this->exChange, $this->exChangeType, false, false, false);

    //     for ($i=0; $i <= 6; $i++) { 
            
    //         $msg = new AMQPMessage('Hello World desde Laravel!'. $i, ['delivery_mode' => 2]);

    //         $channel->basic_publish($msg, $this->exChange);

    //         echo " [x] Sent 'Hello World desde Laravel Exchange: '.$i.'</br>' \n";
    //         sleep(1);
    //     }

    //     $channel->close();
    //     $this->connection->close();
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function consumerMessage()
    // {   
       
    //     $channel = $this->connection->channel();

    //     $channel->queue_declare($this->nameQueues, false, true, false, false);

    //     echo " [*] Waiting for messages. To exit press CTRL+C\n";

    //     $channel->basic_consume($this->nameQueues, '', false, false, false, false, function($message) use ($channel) {
            
    //         $i = 1e6; 
    //         while($i--){}

    //         // Procesar el mensaje
    //         echo "Mensaje recibido: " . $message->body . "\n";
        
    //         // Confirmar el mensaje
    //         $channel->basic_ack($message->delivery_info['delivery_tag']);
    //     });

    //     // Inicia el bucle de recepción de mensajes
    //     while (count($channel->callbacks)) {
    //         $channel->wait();
    //     }

    //     $channel->close();
    //     $this->connection->close();
    // }

   
}
