<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MusicController extends BaseController
{
    private $music;
    private $spotify;
    private $subaybayan;
    private $db;

    public function index()
    {
        //
    }

    public function __construct()
    {
        $this->music = new \App\Models\Music();
        $this->spotify = new \App\Models\Spotify();
        $this->subaybayan = new \App\Models\Subaybayan();
        $this->db = \Config\Database::connect();
        helper('form');
    }

    public function viewmain()
    {
        $context = 'home';
        $data = [
            'spotify' => $this->spotify->findAll(),
            'music' => $this->music->findAll(),
            'context' => $context,
        ];
        return view('viewmain', $data);
    }


    public function magupload()
{
    $file = $this->request->getFile('song');
    $newFileName = $file->getRandomName();

    $data = [
        'title' => pathinfo($file->getName(), PATHINFO_FILENAME), 
        'artist' => 'Unknown', 
        'file_path' => $newFileName,
        'duration' => 0, 
        'album' => 'Unknown', 
        'genre' => 'Unknown',
    ];

    $tuntunin = [
        'song' => [
            'uploaded[song]',
            'mime_in[song,audio/mpeg]',
            'max_size[song,10240]',
            'ext_in[song,mp3]',
        ],
    ];

    if ($this->validate($tuntunin)) {
        if ($file->isValid() && !$file->hasMoved()) {
            if ($file->move(FCPATH . 'uploads\songs', $newFileName)) {
                
                $this->music->save($data);
                echo 'File uploaded successfully';
            } else {
                echo $file->getErrorString() . ' ' . $file->getError();
            }
        }
    } else {
        $data['validation'] = $this->validator;
    }

    return redirect()->to('/viewmain');
}



    public function addToPlaylist()
    {
        $musicID = $this->request->getPost('musicID');
        $playlistID = $this->request->getPost('playlist');


        $data = [
            'playlist_id' => $playlistID,
            'music_id' => $musicID
        ];

        $this->subaybayan->insert($data);

        return redirect()->to('/viewmain');
    }

    public function removeFromPlaylist($musicID)
    {

        $builder = $this->db->table('subaybayan');
        $builder->where('id', $musicID);
        $builder->delete();

        return redirect()->to('/viewmain');
    }

    public function create_playlist()
    {
        $data = [
            'name' => $this->request->getVar('playlist_name'),
            'music' => $this->music->findAll(),
        ];

        $this->spotify->insert($data);
        return redirect()->to('/viewmain');
    }

    public function delete_playlist($playlistID)
    {
        // Find the playlist by its ID
        $playlist = $this->spotify->find($playlistID);

        if ($playlist) {

            $this->subaybayan->where('playlist_id', $playlistID)->delete();

            // Now, delete the playlist
            $this->spotify->delete($playlistID);
        }


        return redirect()->to('/viewmain');
    }

    public function viewPlaylist($playlistID)
    {
        $context = 'playlist';

        $builder = $this->db->table('subaybayan');

        $builder->select('subaybayan.id, music.*');

        $builder->join('music', 'music.music_id = subaybayan.music_id');

        $builder->where('subaybayan.playlist_id', $playlistID);

        $musicInPlaylist = $builder->get()->getResultArray();

        $data = [
            'music' => $musicInPlaylist,
            'spotify' => $this->spotify->findAll(),
            'context' => $context,
        ];

        return view('viewmain', $data);
    }

    public function search()
    {
        $searchTerm = $this->request->getGet('search');
        $context = $this->request->getGet('context');
        $builder = $this->db->table('music');

        if ($context === 'home') {

            // Search all songs
            $builder->like('title', $searchTerm);
        } elseif ($context === 'playlist') {

            // Search songs in the current playlist
            $playlistID = $this->request->getGet('playlistID');
            $builder
                ->join('subaybayan', 'subaybayan.music_id = music.music_id')
                ->where('subaybayan.playlist_id', $playlistID)
                ->like('music.title', $searchTerm);
        } else {
            
        }


        $results = $builder->get()->getResultArray();

    
        $data = [
            'music' => $results,
            'spotify' => $this->spotify->findAll(),
            'context' => $context,
        ];

        return view('viewmain', $data);
    }




}