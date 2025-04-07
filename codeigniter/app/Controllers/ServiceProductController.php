<?php
namespace App\Controllers;

use App\Models\ServiceProductModel;
use CodeIgniter\RESTful\ResourceController;

class ServiceProductController extends ResourceController
{
    public function index()
    {
        $model = new ServiceProductModel();
        $perPage = $this->request->getGet('limit') ?? 10;
        $data = $model->withProduct()->paginate($perPage);
        return $this->respond([
            'data' => $data,
            'pager' => $model->pager->getDetails()
        ]);
    }
}
