<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}

require_once(__DIR__ . '/config/db.php');
$namequery = "SELECT `Username` FROM `Credentials` WHERE `User_ID`=" . $_SESSION['User_ID'];
$nameres = mysqli_query($con, $namequery);
$namerow = $nameres->fetch_row();

$query = "SELECT * FROM `User_Info` WHERE `User_ID`=" . $_SESSION['User_ID'] . " ORDER BY `Description` ASC";
$result = mysqli_query($con, $query);
if (isset($_POST['logout']) && $_POST['logout'] == 1) {
    echo '<script>
            var confirmLogout = window.confirm("Are you sure you want to log out?");
            if (confirmLogout) {
                window.location.href = "/vault/logout";
            } else {
                window.history.back();
            }
          </script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost:8000/vault/">
    <link href=" https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Vault</title>
    <style>
        /* Style the dropdown button */

        /* Style the dropdown content (hidden by default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 10px;
        }

        /* Style the dropdown links */
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show the dropdown content when hovering over the dropdown button */
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <!-- <img src="images/profile.jpg"> -->
                    <!-- <i class='bx bxl-netlify'></i> -->
                    <h2>Password<br><span class="danger">Manager</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <a href="/vault" class="active">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <!-- <a href="#">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>User</h3>
                </a> -->
                <a href="/vault/settings">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="/vault/add-password">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>Add Password</h3>
                </a>
                <a href="/vault/uploads">
                    <span class="material-icons-sharp">
                        upload
                    </span>
                    <h3>Upload</h3>
                </a>
                <form method="post">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit">
                        <span class="material-icons-sharp">
                            logout
                        </span>
                        <h3>Logout</h3>
                    </button>
                </form>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Dashboard</h1>
            <!-- passwords -->
            <div class="passwords">
                <h2>Your Passwords</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Website</th>
                            <th>Link</th>
                            <th>Add Date</th>
                            <th>Word/Phrase</th>
                            <th>Reset Reminder</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                $changeColour = (strtotime(date("Y-m-d")) > strtotime($row['Add_Date']) + 86400 * 180);
                                $rowClass = $changeColour ? 'remind-color' : '';
                                echo '<tr class="' . $rowClass . '">';
                            ?>
                                <td>
                                    <?php echo $row['Website']; ?>
                                </td>
                                <td>
                                    <?php echo $row['Link']; ?>
                                </td>
                                <td>
                                    <?php echo $row['Add_Date']; ?>
                                </td>
                                <td>
                                    <?php
                                    if ($row['Wrd/Phr'] == 1) echo 'P';
                                    else echo 'W';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($row['RST'] == 1) echo
                                    '<span class="material-icons-sharp">
                                        check
                                    </span>';
                                    else echo
                                    '<span class="material-icons-sharp">
                                        close
                                    </span>';
                                    ?>
                                </td>
                                <!-- <td><span class="material-icons-sharp" onclick="myFunction()" id="dropbtn">arrow_drop_down</span><td>
                                <td><span class="material-icons-sharp" onclick="myFunction()" id="dropbtn">edit</span></td>
                                <td><span class="material-icons-sharp" >delete</span></td>
                                <div id="myDropdown" class="dropdown-content">
                                    <br>
                                    <h2>Password</h2>
                                    <span class="material-icons-sharp" >link</span>Link<br>
                                    <span class="material-icons-sharp" >person</span>Username<br>
                                    <span class="material-icons-sharp" >visibility</span>Password<br>
                                    <span class="material-icons-sharp" ></span>Expiry<br>
                                </div> -->
                                <td>
                                    <div class="dropdown">
                                        <!-- <button class="dropdown-btn">Options</button> -->
                                        <span class="material-icons-sharp">more_vert</span>
                                        <div class="dropdown-content">
                                            <a href="#"><span class="material-icons-sharp" onclick="myFunction(this)" id=<?php echo "expbtn" . $row['Link']; ?>>expand_more</span>View</a>
                                            <a href="#"><span class="material-icons-sharp">edit</span>Edit</a>
                                            <a href="#"><span class="material-icons-sharp">delete</span>Delete</a>
                                        </div>
                                    </div>
                                </td>
                        </tr>
                        <tr id=<?php echo $row['Link']; ?> class="dropdown">
                            <td><span class="material-icons-sharp" onclick="myFunction(this)" id=<?php echo "expbtn" . $row['Link']; ?>>link</span>Link<br></td>
                            <td><span class="material-icons-sharp">person</span>Username<br></td>
                            <td><span class="material-icons-sharp">visibility</span>Password<br></td>
                            <td><span class="material-icons-sharp"></span>Expiry<br></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php
                            }
                    ?>
                    </tbody>
                </table>
                <!-- <a href="#">Show All</a> -->
            </div>
            <!-- End of Recent Orders -->

        </main>
        <!-- End of Main Content -->

        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Hey, <b><?php echo $namerow[0] ?></b></p>
                    </div>
                    <div class="profile-photo">
                        <img src="<?php echo '/vault/Icons/' . $_SESSION['User_ID'] . '_user_icon.png' ?>">
                    </div>
                </div>

            </div>
            <!-- End of Nav -->
        </div>


    </div>
    <!-- <script src="orders.js"></script> -->
    <script src="index.js"></script>
    <!-- <script>
        /* When the user clicks on the button, 
        toggle between hiding and showing the dropdown content */
        function myFunction() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
        if (!event.target.matches('#dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
            }
        }
        }
    </script> -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const dropdownBtns = document.querySelectorAll(".dropdown-btn");
        dropdownBtns.forEach((btn) => {
            btn.addEventListener("click", function () {
                const dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            });
        });
    });

     /* When the user clicks on the button, 
        toggle between hiding and showing the dropdown content */
        function myFunction(elem) {
            var currentElem = document.getElementById(elem.id);
            var dropdownContent = currentElem.parentElement.parentElement.nextElementSibling;
            if (dropdownContent.style.display === 'table-row') {
                dropdownContent.style.display = 'none';
            } else {
                dropdownContent.style.display = 'table-row';
            }
        }
</script>

</body>

</html>