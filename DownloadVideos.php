<?php
  $videoDir = '/home/pi/Videos/';
  $urlDir = 'http://signs.pilotpen.com/Videos/';

  $videosOnline = json_decode(trim(utf8_decode(file_get_contents("{$urlDir}md5List.json")), '?'));
  $onlineHash = [];
  $videosAvailable = [];

  foreach($videosOnline as $video) {
    $videosAvailable[] = $video->filename;
    $onlineHash[$video->filename] = $video->md5Hash;
  }

  $videosOnDisk = array_diff(scandir($videoDir), ['..', '.']);

  $videosNeedToDownload = array_diff($videosAvailable, $videosOnDisk);

  $filesToDelete = array_diff($videosOnDisk, $videosAvailable);

  $videosToCheck = array_intersect($videosAvailable, $videosOnDisk);

  foreach($videosToCheck as $video) {
    if (md5(file_get_contents("{$videoDir}{$video}")) != $onlineHash[$video])
      $videosNeedToDownload[] = $video;
  }

  $countFilesToDelete = count($filesToDelete);
  echo "Deleting {$countFilesToDelete} files\n";
  foreach($filesToDelete as $file) 
    unlink("{$videoDir}{$file}");

  $countVideosNeedToDownload = count($videosNeedToDownload);
  echo "Downloading {$countVideosNeedToDownload} files \n";
  foreach($videosNeedToDownload as $video)
    passthru("wget \"{$urlDir}{$video}\" -O \"{$videoDir}{$video}\"  -q --progress=bar --show-progress");
?>
