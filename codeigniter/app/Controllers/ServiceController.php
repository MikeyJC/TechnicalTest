<?php
namespace App\Controllers;

use App\Models\ServiceModel;
use CodeIgniter\RESTful\ResourceController;

class ServiceController extends ResourceController
{
    public function index()
    {
        $model = new ServiceModel();
        $perPage = $this->request->getGet('limit') ?? 10;
        $data = $model->paginate($perPage);
        return $this->respond([
            'data' => $data,
            'pager' => $model->pager->getDetails()
        ]);
    }
}
