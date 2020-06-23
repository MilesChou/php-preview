<?php

namespace App\Http\Controllers\Before;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConstructorPromotion extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var int
     */
    private $defaultValue;

    /**
     * @param Request $request
     * @param int $defaultValue
     */
    public function __construct(Request $request, $defaultValue = 10)
    {
        $this->request = $request;
        $this->defaultValue = $defaultValue;
    }

    public function __invoke()
    {
        return response('IP: ' . $this->request->ip() . ', Default: ' . $this->defaultValue);
    }
}
