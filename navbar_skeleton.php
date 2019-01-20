<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only"><?php echo $_SITE_NAVIGATE_TOGGLE; ?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Kontest</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li><a href="home.php"><?php echo $_SITE_TITLE_HOME; ?></a></li>
          <?php
              if($userRow['user_role'] >= 50)
              echo "<li><a href=\"users.php\">". $_SITE_TITLE_USER ."</a></li>\n";
          ?>
        <li><a href="contest.php"><?php echo $_SITE_TITLE_CONTEST; ?></a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                      <span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $userRow['user_name']; ?>&nbsp;<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $_SITE_NAVIGATE_PROFILE; ?></a></li>
            <li><a href="about.php"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;<?php echo $_SITE_NAVIGATE_ABOUT; ?></a></li>
            <li><a href="logout.php?logout=true"><span class="glyphicon glyphicon-log-out"></span>&nbsp;<?php echo $_SITE_NAVIGATE_LOGOFF; ?></a></li>
          </ul>
        </li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>
