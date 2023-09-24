<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MainModel;

class MainController extends BaseController
{
    public function delete($id)
    {
        $main = new MainModel();
        $main->delete($id);
        return redirect()->to('/test');
    }

    public function update($id)
    {
        $main = new MainModel();
        
        $data = [
            'main' => $main->findAll(),
            'rian' => $main->find($id), 
        ];
        return view('main', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id'); 
        $data = [
            'StudName' => $this->request->getPost('StudName'),
            'StudGender' => $this->request->getPost('StudGender'),
            'StudCourse' => $this->request->getPost('StudCourse'),
            'StudSection' => $this->request->getPost('StudSection'),
            'StudYear' => $this->request->getPost('StudYear'),
            'Section' => $this->request->getPost('Section'),
        ];
        
        $main = new MainModel();
        
        if (!empty($id)) {
            $main->update($id, $data);
        } else {
            $main->save($data);
        }
        
        return redirect()->to('/test');
    }

    public function test()
    {
        $main = new MainModel();
        $data['main'] = $main->findAll();
        return view('main', $data);
    }

    public function index()
    {
        
    }
}
