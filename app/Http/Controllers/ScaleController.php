<?php

namespace App\Http\Controllers;

use lepiaf\SerialPort\SerialPort;
use lepiaf\SerialPort\Parser\SeparatorParser;
use lepiaf\SerialPort\Configure\TTYConfigure;

class ScaleController extends Controller
{

    public function index()
    {
        $serialPort = new SerialPort(new SeparatorParser(), new TTYConfigure());

        $serialPort->open("/dev/ttyACM0");
        while ($data = $serialPort->read()) {
            echo $data . "\n";

            if ($data === "OK") {
                $serialPort->write("1\n");
                $serialPort->close();
            }
        }
    }
}
