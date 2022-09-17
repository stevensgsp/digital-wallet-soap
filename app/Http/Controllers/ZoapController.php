<?php

namespace App\Http\Controllers;

use App\Soap\Server;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Laminas\Soap\AutoDiscover;
use Laminas\Soap\Server\DocumentLiteralWrapper;
use Laminas\Soap\Wsdl;
use SoapFault;
use Viewflex\Zoap\ZoapController as ViewflexZoapController;

class ZoapController extends ViewflexZoapController
{
    /**
     * Return results of a call to the specified service.
     *
     * @param $key
     * @return Factory|Response|View
     */
    public function server($key)
    {
        $output = new Response();
        ob_start();

        try {
            $this->init($key);

            foreach ($this->headers as $key => $value) {
                $output->headers->set($key, $value);
            }

            if (isset($_GET['wsdl'])) {
                // Create wsdl object and register type(s).
                $wsdl = new Wsdl('wsdl', $this->endpoint);

                foreach ($this->types as $key => $class) {
                    $wsdl->addType($class, $key);
                }

                // Set type(s) on strategy object.
                $this->strategy->setContext($wsdl);

                foreach ($this->types as $key => $class) {
                    $this->strategy->addComplexType($class);
                }

                // Auto-discover and output xml.
                $discover = new AutoDiscover($this->strategy);
                $discover->setBindingStyle(array('style' => 'document'));
                $discover->setOperationBodyStyle(array('use' => 'literal'));
                $discover->setClass($this->service);
                $discover->setUri($this->endpoint);
                $discover->setServiceName($this->name);
                echo $discover->toXml();
            } else {
                $server = new Server($this->endpoint . '?wsdl');
                $server->setClass(new DocumentLiteralWrapper(new $this->service()));
                $server->registerFaultException($this->exceptions);
                $server->setOptions($this->options);

                // Intercept response, then decide what to do with it.
                $server->setReturnResponse(true);
                $response = $server->handle();

                // Deal with a thrown exception that was converted into a SoapFault.
                // SoapFault thrown directly in a service class bypasses this code.
                if ($response instanceof SoapFault) {
                    $output->headers->set("Status", 500);
                    echo self::serverFault($response);
                } else {
                    echo $response;
                }
            }
        } catch (\Exception $e) {
            $output->headers->set("Status", 500);
            echo self::serverFault($e);
        }

        $output->setContent(ob_get_clean());
        return $output;
    }

    /**
     * Return error response and log stack trace.
     *
     * @param \Exception $exception
     * @return Factory|View
     */
    public static function serverFault(\Exception $exception)
    {
        self::log($exception->getTraceAsString());
        $faultcode = $exception->faultcode;
        $faultstring = $exception->getMessage();
        return view('zoap::fault', compact('faultcode', 'faultstring'));
    }
}
