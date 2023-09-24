<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MainModel;

class MainController extends BaseController
{
    private $playlist;
    private $ref;
    private $songs;

    public function __construct()
    {
        $this->playlist = new \App\Models\Playlist();
        $this->ref = new \App\Models\RefTableModel();
        $this->songs = new \App\Models\Songs();
        helper('url');
    }

    public function index()
    {
        $searchQuery = $this->request->getGet('search');
        $playlistId = $this->request->getGet('playlist_id');

        if (!empty($playlistId)) {
            $data = $this->fetchPlaylistAndSongs($playlistId);
        } elseif (!empty($searchQuery)) {
            $data = $this->searchSongs($searchQuery);
        } else {
            $data = $this->fetchAllSongs();
        }

        $data['playlists'] = $this->playlist->findAll();
        $data['searchQuery'] = $searchQuery;

        return view('index', $data);
    }

    public function playlist($playlistId)
    {
        $data = $this->fetchPlaylistAndSongs($playlistId);
        $data['playlists'] = $this->playlist->findAll();
        return view('index', $data);
    }

    private function fetchPlaylistAndSongs($playlistId)
    {
        $playlist = $this->playlist
            ->select('playlist.playlist_id, playlist.name, music.music_id, music.title, music.artist, music.album, music.genre, music.file_path')
            ->join('playlistmusic', 'playlistmusic.playlist_id = playlist.playlist_id')
            ->join('music', 'music.music_id = playlistMusic.music_id')
            ->where('playlist.playlist_id', $playlistId)
            ->findAll();

        return ['playlistContent' => $playlist];
    }

    private function searchSongs($searchQuery)
    {
        $searchResults = $this->songs->like('title', $searchQuery)
                                    ->orLike('artist', $searchQuery)
                                    ->findAll();

        return ['searchResults' => $searchResults];
    }

    private function fetchAllSongs()
    {
        $songs = $this->songs->findAll();
        return ['songs' => $songs];
    }

    public function saveMusic()
    {
        $musicFilePath = $this->request->getFile('musicFilePath');

        if (!$musicFilePath->isValid() || $musicFilePath->hasMoved()) {
            return redirect()->back()->with('error', 'File upload failed.');
        }

        $newName = $this->generateUniqueMp3FileName();
        $musicFilePath->move(ROOTPATH . 'public/uploads', $newName);

        $data = [
            'title' => $this->request->getVar('musicTitle'),
            'artist' => $this->request->getVar('musicArtist'),
            'album' => $this->request->getVar('musicAlbum'),
            'genre' => $this->request->getVar('musicGenre'),
            'file_path' => $newName,
        ];

        $this->songs->insert($data);

        return redirect()->to('/');
    }

    public function savePlaylist()
    {
        $data = [
            'name' => $this->request->getVar('name'),
        ];

        $this->playlist->insert($data);

        return redirect()->to('/');
    }

    public function addToPlaylist()
    {
        $data = [
            'playlist_id' => $this->request->getVar('playlist'),
            'music_id' => $this->request->getVar('musicId'),
        ];

        $this->ref->insert($data);
        return redirect()->to('/');
    }

    private function generateUniqueMp3FileName()
    {
        $directory = ROOTPATH . 'public/uploads/';
        do {
            $newName = uniqid() . '.mp3'; 
            $filePath = $directory . $newName;
        } while (file_exists($filePath)); 

        return $newName;
    }
    

    
}