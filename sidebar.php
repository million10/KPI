<?php
// Include necessary scripts or configurations
include '../includes/db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Employee Page |</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  
  <!-- CSS Links -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  
  <!-- Inline Styles -->
  <style>
    .main-header {
      background-color: #3c8dbc;
      color: #fff;
    }
    .navbar {
      margin-bottom: 0;
    }
    .navbar-nav {
      margin: 0;
      display: flex;
      justify-content: space-around;
	  .navbar-brand img { height: 50px; }
    }
    .navbar-nav > li > a {
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      padding: 15px 20px;
      text-align: center;
    }
    .navbar-nav > li > a:hover {
      background-color: #367fa9;
      color: #fff;
    }
    
    @media (max-width: 768px) {
      .navbar-header {
        display: block; /* Ensure header is visible */
      }
      .navbar-collapse {
        background-color: #3c8dbc;
      }
      .navbar-nav {
        display: block;
        text-align: left;
        padding-left: 0;
      }
      .navbar-nav > li {
        border-top: 1px solid #fff;
      }
      .navbar-nav > li > a {
        padding: 10px 15px;
        color: #fff;
      }
      .navbar-nav > li > a:hover {
        background-color: #367fa9;
      }
      .navbar-toggle {
        border: none;
        background: transparent;
      }
      .navbar-toggle .icon-bar {
        background-color: #fff;
      }
    }

    @media (min-width: 769px) {
      .navbar-nav {
        display: flex;
        flex-direction: row;
      }
      .navbar-nav > li {
        display: inline-block;
      }
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container-fluid">
	  
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu">
            <span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
            
          </button>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
          <ul class="nav navbar-nav">
              <li><a class="navbar-brand" href="employee_dashboard.php">
      <img src="../assets/OIP.JPG" alt="JJU Logo"></li> 
            <li> <a href="assigned_tasks.php"><i class="fas fa-clipboard-list"></i> assigned Tasks</a>
</li>   <li> <a href="report.php"><i class="fas fa-upload"></i> Completed Task</a>
</li>
  <li>  <a href="settings.php"><i class="fas fa-user-cog"></i> Settings</a></li>
  <li>  <a href="../index.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</li>          </ul>
        </div>
      </div>
    </nav>
  </header>

</div>

<!-- JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
