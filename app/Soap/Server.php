<?php

namespace App\Soap;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laminas\Soap\Server as LaminasSoapServer;
use SoapFault;

class Server extends LaminasSoapServer
{
    /**
     * Generate a server fault
     *
     * Note that the arguments are reverse to those of SoapFault.
     *
     * If an exception is passed as the first argument, its message and code
     * will be used to create the fault object if it has been registered via
     * {@Link registerFaultException()}.
     *
     * @link   http://www.w3.org/TR/soap12-part1/#faultcodes
     *
     * @param  string|Exception $fault
     * @param  int $code SOAP Fault Codes
     * @return SoapFault
     */
    public function fault($fault = null, $code = 400)
    {
        $this->caughtException = is_string($fault) ? new Exception($fault) : $fault;

        if ($fault instanceof ValidationException) {
            $message = $fault->getMessage();
            $code    = 422;
        } elseif ($fault instanceof ModelNotFoundException) {
            $message = $fault->getMessage();
            $code    = 404;
        } elseif ($fault instanceof Exception) {
            if ($this->isRegisteredAsFaultException($fault)) {
                $message = $fault->getMessage();
                $eCode   = ($fault->getCode() === 0) ? ($fault->status ?? 0) : $fault->getCode();
                $code    = empty($eCode) ? $code : $eCode;
            } else {
                $message = 'Unknown error';
            }
        } elseif (is_string($fault)) {
            $message = $fault;
        } else {
            $message = 'Unknown error';
        }

        return new SoapFault($code, $message);
    }
}
