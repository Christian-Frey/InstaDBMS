<?php
//
// Name: header.php
// Author: Christian
// Purpose: Puts all of the header information into one file to make it easier
//          maintain. Just use require_once('path/to/header.php'), then
//          call buildHeader();. Include the header.js file as a script
//          to handle the user input.
//
function buildHeader()
{
    // Using global forces PHP to use the value of mysqli from conn.php,
    // not the local and non-existant scope it want to use.
    global $mysqli;
    require_once('conn.php');
    echo '<div class=header>
        <a href="home.php" id="projectName">instaDBMS</a>
        <input id="searchSite" name="searchSite" type="text"
               placeholder=" Search?">
        <a id=uploadPhoto href="uploadPhoto.php">Upload Photo</a>';

        // Connecting to the server...*dial up noises*

        // Getting the username of the user based on their user_id.
        $stmtUN = $mysqli->prepare("SELECT user_name FROM user where user_id= ?");
        // Checking if the user is a moderator based on the user_id.
        $stmtIsAMod = $mysqli->prepare("SELECT mod_id FROM
             moderator where mod_id = ?");
        $stmtIsAMod->bind_param('i', $_COOKIE['instaDBMS']);
        $stmtIsAMod->execute();
        $stmtIsAMod->store_result();

        // They are a mod, so lets give them special mod buttons.
        if ($stmtIsAMod->num_rows == 1)
        {
            echo '<a id="viewReport" href="viewReports.php">View Reports</a>';
        }
        // Now we can get the users name to display on their page.
        $stmtUN->bind_param("s", $_COOKIE['instaDBMS']);
        $stmtUN->execute();
        $stmtUN->bind_result($un);
        while ($stmtUN->fetch())
           echo "<a id=user_name href='profile.php'>" . $un . "</a>";

    echo '<a href="javascript:;" class="log_out">Log out</a>
    </div>';
}
