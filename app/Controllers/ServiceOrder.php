<?php

namespace Valle\Controllers;

use Slim\Views\Twig;
use Valle\Models\ServiceOrder as ServiceOrderModel;

class ServiceOrder
{
    public function __construct(Twig $view, ServiceOrderModel $model)
    {
        $this->view = $view;
        $this->model = $model;
    }
}