<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MusicController extends BaseController
{
    private $music;
    private $playlists;
    private $playlist_track;
    private $db;

    public function index()
    {
        //
    }

    public function __construct()
    {
        $this->music = new \App\Models\Music();
        $this->playlists = new \App\Models\Playlist();
        $this->playlist_track = new \App\Models\TrackPlaylist();
        $this->db = \Config\Database::connect();
        helper('form');
    }

    public function main()
    {
        $context = 'home';
        $data = [
            'playlists' => $this->playlists->findAll(),
            'music' => $this->music->findAll(),
            'context' => $context,
        ];
        return view('main', $data);
    }


    public function upload()
{
    $file = $this->request->getFile('song');
    $newFileName = $file->getRandomName();

    $data = [
        'title' => pathinfo($file->getName(), PATHINFO_FILENAME), // Set the title based on the filename
        'artist' => 'Unknown', // Set a default artist if not provided
        'file_path' => $newFileName,
        'duration' => 0, // You can calculate the duration if needed
        'album' => 'Unknown', // Set a default album if not provided
        'genre' => 'Unknown', // Set a default genre if not provided
    ];

    $rules = [
        'song' => [
            'uploaded[song]',
            'mime_in[song,audio/mpeg]',
            'max_size[song,10240]',
            'ext_in[song,mp3]',
        ],
    ];

    if ($this->validate($rules)) {
        if ($file->isValid() && !$file->hasMoved()) {
            if ($file->move(FCPATH . 'uploads\songs', $newFileName)) {
                // Save the data to the database
                $this->music->save($data);
                echo 'File uploaded successfully';
            } else {
                echo $file->getErrorString() . ' ' . $file->getError();
            }
        }
    } else {
        $data['validation'] = $this->validator;
    }

    return redirect()->to('/main');
}



    public function addToPlaylist()
    {
        $musicID = $this->request->getPost('musicID');
        $playlistID = $this->request->getPost('playlist');


        $data = [
            'playlist_id' => $playlistID,
            'music_id' => $musicID
        ];

        $this->playlist_track->insert($data);

        return redirect()->to('/main');
    }

    public function removeFromPlaylist($musicID)
    {

        $builder = $this->db->table('playlist_track');
        $builder->where('id', $musicID);
        $builder->delete();

        return redirect()->to('/main');
    }

    public function create_playlist()
    {
        $data = [
            'name' => $this->request->getVar('playlist_name'),
            'music' => $this->music->findAll(),
        ];

        $this->playlists->insert($data);
        return redirect()->to('/main');
    }
    // public function edit_playlist($playlist) //unnecesarry
    // {
    //     $data = [
    //         'playlist_records' => $this->playlists->where('playlist_id', $playlist)->first(),
    //         'playlists' => $this->playlists->findAll(),
    //     ];

    //     return view('main', $data);
    // }

    public function delete_playlist($playlistID)
    {
        // Find the playlist by its ID
        $playlist = $this->playlists->find($playlistID);

        if ($playlist) {

            $this->playlist_track->where('playlist_id', $playlistID)->delete();

            // Now, delete the playlist
            $this->playlists->delete($playlistID);
        }


        return redirect()->to('/main');
    }

    public function viewPlaylist($playlistID)
    {
        $context = 'playlist';

        $builder = $this->db->table('playlist_track');

        $builder->select('playlist_track.id, music.*');

        $builder->join('music', 'music.music_id = playlist_track.music_id');

        $builder->where('playlist_track.playlist_id', $playlistID);

        $musicInPlaylist = $builder->get()->getResultArray();

        $data = [
            'music' => $musicInPlaylist,
            'playlists' => $this->playlists->findAll(),
            'context' => $context,
        ];

        return view('main', $data);
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
                ->join('playlist_track', 'playlist_track.music_id = music.music_id')
                ->where('playlist_track.playlist_id', $playlistID)
                ->like('music.title', $searchTerm);
        } else {
            //nag iisp pa
        }

        // Get the search results
        $results = $builder->get()->getResultArray();

        // Pass the search results to the view
        $data = [
            'music' => $results,
            'playlists' => $this->playlists->findAll(),
            'context' => $context,
        ];

        return view('main', $data);
    }




}