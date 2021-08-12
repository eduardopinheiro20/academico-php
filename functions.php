<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use RedBeanPHP\OODBBean;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

function rabbitMqConnection(): AMQPStreamConnection
{
    do {
        try {
            $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        } catch (AMQPIOException) {
            sleep(5);
            echo 'Retrying' . PHP_EOL;
        }
    } while(!isset($connection));

    return $connection;
}

function sendMailTo(OODBBean $student): void
{
    $mensagem = <<<FIM
    Olá, $student->name! Seu pagamento foi confirmado e sua matrícula foi criada com sucesso.
    Para acessar sua conta e começar a estudar conosco, acesse: http://localhost:4200/login?email=$student->email
    
    Bons estudos!
    FIM;

    $usuario = 'carlosv775@gmail.com';
    $email = (new Email())
        ->from($usuario)
        ->to($student->email)
        ->subject('Matrícula confirmada')
        ->text($mensagem);

    $senha = 'guhwlvppptrhafob';
    $transport = Transport::fromDsn("gmail+smtp://$usuario:$senha@default");
    $mailer = new Mailer($transport);
    $mailer->send($email);
}
