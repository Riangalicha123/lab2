<!DOCTYPE html>
<html lang="en">

<body>

  <?php include 'include/Add_a_Songs.php';
  include 'include/top.php';
  include 'include/ModalPlaylist.php';
  include 'include/addToPlaylist.php';
  ?>



          <div class="container">
            <div class="row">
              <div class="col">
                <a href="/viewmain" class="btn btn-dark ml-2">
                  <i class="fas fa-home"></i> Home
                </a>
              </div>
              <div class="col">
                <!-- Search Form -->
                <form action="/search" method="get">
                  <div class="input-group">
                    <input type="search" name="search" class="form-control" placeholder="Search song">
                    <input type="hidden" name="context" value="<?= $context ?>">
                    <?php if ($context === 'playlist'): ?>
                      <input type="hidden" id="playlistIDInput" name="playlistID" value="">
                    <?php endif ?>
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-dark">Search</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>




        <!-- Music Player Header -->
        <h1 class="mt-4">Music Player</h1>

        <!-- Action Buttons -->
        <div class="mb-3">
          <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">My
            Playlist
          </button> 
          
        </div>
    </div>


    <!-- Audio Player -->
    <div class="audio-controls mb-3">
      <audio id="audio" controls autoplay></audio>
    </div>
          <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#manageSongsModal">Manage
            Songs
          </button> 

    <!-- Music Playlist -->
      <ul class="list-group" id="playlist">
        <?php foreach ($music as $musicItem): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center"
            data-src="<?= base_url('/uploads/songs/' . $musicItem['file_path']) ?>">
            <?= $musicItem['title'] ?>
            <?php if ($context === 'playlist'): ?>
              <div class="btn-group">
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#myModal"
                  onclick="setMusicID('<?= $musicItem['music_id'] ?>')">
                  <i class="fas fa-plus"></i>+
                </button>
                <a href="<?= site_url('/removeFromPlaylist/' . $musicItem['id']) ?>" class="btn btn-secondary btn-sm">
                  <i class="fas fa-minus"></i>-
                </a>
              </div>
            <?php else: ?>
              <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#myModal"
                onclick="setMusicID('<?= $musicItem['music_id'] ?>')">
                <i class="fas fa-plus"></i>+
              </button>
            <?php endif ?>
          </li>
        <?php endforeach ?>
      </ul>




  </div>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Get the playlist ID from the URL
      const urlParams = new URLSearchParams(window.location.search);
      const playlistID = urlParams.get('playlistID');

      // Set the value of the hidden input field
      const playlistIDInput = document.getElementById('playlistIDInput');
      if (playlistID) {
        playlistIDInput.value = playlistID;
      }
    });
  </script>

  <script>
    $(document).ready(function () {
      // Get references to the button and modal
      const modal = $("#myModal");
      const modalData = $("#modalData");
      const musicID = $("#musicID");
      // Function to open the modal with the specified data
      function openModalWithData(dataId) {
        // Set the data inside the modal content
        modalData.text("Data ID: " + dataId);
        musicID.val(dataId);
        // Display the modal
        modal.css("display", "block");
      }

      // Add click event listeners to all open modal buttons

      // When the user clicks the close button or outside the modal, close it
      modal.click(function (event) {
        if (event.target === modal[0] || $(event.target).hasClass("close")) {
          modal.css("display", "none");
        }
      });
    });
  </script>
  <script>
    const audio = document.getElementById('audio');
    const playlist = document.getElementById('playlist');
    const playlistItems = playlist.querySelectorAll('li');

    let currentTrack = 0;

    function playTrack(trackIndex) {
      if (trackIndex >= 0 && trackIndex < playlistItems.length) {
        const track = playlistItems[trackIndex];
        const trackSrc = track.getAttribute('data-src');
        audio.src = trackSrc;
        audio.play();
        currentTrack = trackIndex;
      }
    }

    function nextTrack() {
      currentTrack = (currentTrack + 1) % playlistItems.length;
      playTrack(currentTrack);
    }

    function previousTrack() {
      currentTrack = (currentTrack - 1 + playlistItems.length) % playlistItems.length;
      playTrack(currentTrack);
    }

    playlistItems.forEach((item, index) => {
      item.addEventListener('click', () => {
        playTrack(index);
      });
    });

    audio.addEventListener('ended', () => {
      nextTrack();
    });

    playTrack(currentTrack);
  </script>
  <script>
    function setMusicID(musicID) {
      // Set the value of the hidden input field
      document.getElementById('musicID').value = musicID;
    }
  </script>

</body>

</html>