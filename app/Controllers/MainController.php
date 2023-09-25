<?php

namespace App\Controllers;

use App\Controllers\BaseController;
class MainController extends BaseController
{
    private $playlist;

    public function __construct()
    {
        $this->playlist = new \App\Models\PlaylistModel();
    }
    public function index()
    {
        //
    }
    public function main()
    {
        $data = [
            'playlist' => $this->playlist->findAll(),
        ];
        return view('main', $data);
    }
    public function createPlaylist(){
        $data = [
            'name' => $this->request->getVar('pname'),
        ];
        $this->playlist->save($data);
        return redirect()->to('/main');
    }

    public function deletePlaylist($playlistID)
    {
        // Find the playlist by its ID
        $playlist = $this->playlist->find($playlistID);

        if ($playlist) {

            $this->bridge->where('playlist_id', $playlistID)->delete();

            // Now, delete the playlist
            $this->playlist->delete($playlistID);
        }


        return redirect()->to('/main');
    }
}
