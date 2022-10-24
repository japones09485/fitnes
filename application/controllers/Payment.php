<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment extends CI_Controller
{

    public function __construct (){
        parent::__construct();
    }

    function response(){
        /*En esta página se reciben las variables enviadas desde ePayco hacia el servidor.
        Antes de realizar cualquier movimiento en base de datos se deben comprobar algunos valores
        Es muy importante comprobar la firma enviada desde ePayco
        Ingresar  el valor de p_cust_id_cliente lo encuentras en la configuración de tu cuenta ePayco
        Ingresar  el valor de p_key lo encuentras en la configuración de tu cuenta ePayco
        */

        $p_cust_id_cliente = '78547';
        $p_key             = '4fc0dd5dac1ce434e79ce4271d6d20575abc1f7a';

        $x_ref_payco      = $_REQUEST['x_ref_payco'];
        $x_transaction_id = $_REQUEST['x_transaction_id'];
        $x_amount         = $_REQUEST['x_amount'];
        $x_currency_code  = $_REQUEST['x_currency_code'];
        $x_signature      = $_REQUEST['x_signature'];
        $idClase          = $_REQUEST['x_extra1']; // Aqui viene el ID de clase
        $nombre           = $_REQUEST['x_extra2']; // Nombre del cliente
        $email            = $_REQUEST['x_extra3']; // Correo electronico
        $idUser            = $_REQUEST['x_extra4']; // ID de usuario en la base de datos

        $signature = hash('sha256', $p_cust_id_cliente . '^' . $p_key . '^' . $x_ref_payco . '^' . $x_transaction_id . '^' . $x_amount . '^' . $x_currency_code);

        $x_response     = $_REQUEST['x_response'];
        $x_motivo       = $_REQUEST['x_response_reason_text'];
        $x_id_invoice   = $_REQUEST['x_id_invoice'];
        $x_autorizacion = $_REQUEST['x_approval_code'];

        //Validamos la firma
        if ($x_signature == $signature) {
			// insert de pago
			$this->load->model('pagos_model','pag');
			$this->pag->insert(array(
				'pag_fk_usuario'=>  $nombre,
				'pag_fecha'=> date( 'Y-m-d H:i:s' ),
				'pag_fk_clase'=> $x_motivo,
				'pag_valor'=> $x_amount,
				'pag_ref_transaccion'=>  $x_ref_payco,
				'pag_estado' => $x_cod_response
				
			));

            /*Si la firma esta bien podemos verificar los estado de la transacción*/
            $x_cod_response = $_REQUEST['x_cod_response'];
            switch ((int) $x_cod_response) {
                case 1:
                    # code transacción aceptada
                    //echo 'transacción aceptada';
					$this->mail_confirmacion_post($idClase, $nombre, $email, $x_cod_response);
					
                    break;
                case 2:
                    # code transacción rechazada
                    //echo 'transacción rechazada';
                    break;
                case 3:
                    # code transacción pendiente
                    //echo 'transacción pendiente';
                    break;
                case 4:
                    # code transacción fallida
                    //echo 'transacción fallida';
                    break;
            }
        } else {
            die('Firma no valida');
        }
    }

    //mail confirmacion de pago
    function mail_confirmacion_post($idClase, $name, $email, $tipo){
        $this->load->model('clases_model','clas');
        $id = $idClase;
        $name_user = $name;
        $emailusuario = $email;
        $clase = $this->clas->infoId($id);
        if($clase->clas_tipo == 0){
            $tipoclase='Personalizada';
        }else if($clase->clas_tipo==1){
            $tipoclase='Grupal';
        }
        $body = $this->load->view('confirmacion', array('clase'=>$clase,'tipoclase'=>$tipoclase,'usuario'=>$name_user), TRUE);
        $img = snappy_image('img/logo.png');
        $config ['mailtype'] = 'html';
        $this->email->initialize($config);
        $this->email->from('cityfitnessworld.confirmaciones@cityfitnessworld.com', 'Confirmación de pago City Fitness World');
        $this->email->to($emailusuario);
        $this->email->bcc(array(
            'cityfitnessworld.confirmaciones@cityfitnessworld.com',
        ));
        $this->email->subject('TEST');
        $this->email->message($body);
        if($this->email->send()){
            $resp['ok'] = TRUE;
        }else{
            $resp['ok'] = FALSE;
        }
        $this->response($resp);
    }
}
