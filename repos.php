<?php

//Main Configuration Options
$fossil_settings_path=".";
$fossil_path="/usr/bin/fossil";
$fossil_repos_path="./repositories";
$base_path="/repos/all";
$repos_base_path="/repos";
$admin_user = "demo";
$admin_password = "demo";

$default_private_settings = "private_settings.fossil";
$default_public_settings = "public_settings.fossil";
$default_repo_username = "user";
$default_repo_password = "user";
$copyright = "My Repo";
//End Configurations

session_start();

//Login action
if(isset($_REQUEST['login']))
{
    if(
        $_REQUEST['username'] == $admin_user &&
        $_REQUEST['password'] == $admin_password
    )
    {
        $_SESSION["logged"] = true;
        header("Location: $base_path");
    }
    else
        $message = "Wrong username or password.";
}

//Logout action
elseif(isset($_REQUEST['logout']))
{
    unset($_SESSION["logged"]);
    
    header("Location: $base_path");
}

//Add repo
elseif(isset($_REQUEST['addrepo']))
{
    if(trim($_REQUEST['name']) != "")
    {
        $name = trim($_REQUEST['name']);
        $username = trim($_REQUEST['username']);
        $password = trim($_REQUEST['password']);
        $template = trim($_REQUEST['template']);
        
        if($template != "")
        {
            $template = "$fossil_repos_path/$template.fossil";
            $message = `HOME="$fossil_settings_path" "$fossil_path" init --template "$template"  -A "$username" "$fossil_repos_path/$name.fossil" 2>&1 && echo ""`;
        }
        elseif($_REQUEST["access"]=="private")
        {
            $message = `HOME="$fossil_settings_path" "$fossil_path" init --template "$default_private_settings"  -A "$username" "$fossil_repos_path/$name.fossil" 2>&1 && echo ""`;
        }
        elseif($_REQUEST["access"]=="public")
        {
            $message = `HOME="$fossil_settings_path" "$fossil_path" init --template "$default_public_settings"  -A "$username" "$fossil_repos_path/$name.fossil" 2>&1 && echo ""`;
        }
        elseif($_REQUEST["access"]=="default")
        {
            $message = `HOME="$fossil_settings_path" "$fossil_path" init -A "$username" "$fossil_repos_path/$name.fossil" 2>&1 && echo ""`;
        }
        
            
        $message = rtrim(str_replace("\n", "<br />", $message), "<br />");
        
        //Set password
        if($password != "")
            `HOME="$fossil_settings_path" "$fossil_path" user password "$username" "$password" -R "$fossil_repos_path/$name.fossil"`;
    }
}

//Remove repo
elseif(isset($_REQUEST['removerepo']) && !isset($_REQUEST["no"]))
{
    if(trim($_REQUEST['name']) != "")
    {
        $repo = trim($_REQUEST['name']);
        `rm $fossil_repos_path/$repo.fossil`;
    }
    
    header("Location: $base_path");
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Personal Fossil Repositories</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    
    <style>
        body {
            padding-top: 50px;
        }
        .row {
          margin: 0 auto 20px auto;
        }
        .row
        {
            width: 100%;
        }
        .row .row {
          margin-top: 10px;
          margin-bottom: 0;
        }
        [class*="col-"] {
          padding-top: 15px;
          padding-bottom: 15px;
          background-color: #eee;
          border: 1px solid #ddd;
          background-color: rgba(86,61,124,.15);
          border: 1px solid rgba(86,61,124,.2);
        }
    </style>

  </head>
  <body>
      
      <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?=$base_path?>">Repositories</a>
        </div>
        <div class="navbar-collapse collapse">
          <!--<ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>
              </ul>
            </li>
          </ul>-->
          
          <?php if(isset($_SESSION["logged"])){ ?>
          <form class="navbar-form navbar-right" action="<?=$base_path?>">
            <input type="hidden" name="logout">
            <button type="submit" class="btn btn-cancel">Logout</button>
          </form>
          <?php } else { ?>
          <form class="navbar-form navbar-right" action="<?=$base_path?>">
              <input type="hidden" name="login">
            <div class="form-group">
              <input type="text" name="username" placeholder="Username" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" name="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form>
          <?php } ?>
          
        </div><!--/.navbar-collapse -->
      </div>
    </div>
    
    <div class="container">

<?php if(isset($_REQUEST["remove"])){ //Remove repository ?>
    
    <h1>Are you sure you want to delete the repo?</h1>
    
    <h2>Name: <?=$_REQUEST["remove"]?></h2>
    
    <form method="post" action="<?=$base_path?>">
        <input type="hidden" name="removerepo" value="1" />
        <input type="hidden" name="name" value="<?=$_REQUEST['remove']?>" />
        <button type="submit" class="btn btn-success">Yes</button>
        <button type="submit" name="no" class="btn btn-cancel">No</button>
    </form>

<?php } elseif(isset($_SESSION["logged"])){ //Repositories ?>

    <h1>Repositories</h1>
    
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                        Add Repo
                    </a>
                </h2>
    
                <div id="collapseOne" class="panel-collapse collapse out">
                    <div class="panel-body">
                        <form method="post">
                            <div class="form-group">
                                <input type="hidden" name="addrepo" />
                                <input placeholder="Name" type="text" name="name" class="form-control" />
                                <input placeholder="Username" type="text" name="username" value="<?=$default_repo_username?>" class="form-control" />
                                <input placeholder="Password" type="password" name="password" value="<?=$default_repo_password?>" class="form-control" />
                                <select name="template" placeholder="Template" class="form-control">
                                    <option value="">Template</option>
                                    <?php
                                        $repos = (scandir($fossil_repos_path));

                                        unset($repos[0]);
                                        unset($repos[1]);
                                        
                                        foreach($repos as $repo)
                                        {
                                            $repo = str_replace(".fossil", "", $repo);
                                            
                                            print "<option value=\"$repo\">$repo</option>";
                                        }
                                    ?>
                                </select>
                            </div>  
                            <div class="form-group">
                                <input id="private" type="radio" name="access" value="private" checked />
                                <label for="private">private</label>
                                <input id="public" type="radio" name="access" value="public" />
                                <label for="public">public</label>
                                <input id="default" type="radio" name="access" value="default" />
                                <label for="default">default</label>
                            </div>  
                            <button class="btn btn-success">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <hr />
    
    <?php
        if(isset($message))
            print '<div class="alert alert-success">'.$message.'</div>';
    ?>
    
    <h2>Current Repositories</h2>
    
    <?php
        $repos = (scandir($fossil_repos_path));

        unset($repos[0]);
        unset($repos[1]);

        foreach($repos as $repo)
        {
            if($repo == "users")
                continue;
                
            $repo_name = str_replace(".fossil", "", $repo);
            
            print '<div class="row">' . "\n";
            
            print '<div class="col-md-12">';
            print '<a target="_blank"href="'
            .$repos_base_path.'/'.$repo_name.'/login?u='.
            $default_repo_username.'&p='.$default_repo_password.'">'.
            $repo_name.'</a>' . "\n";
            print '<a class="btn btn-default btn-lg pull-right" href="?remove='.$repo_name.'">' . "\n";
            print '<span class="glyphicon glyphicon-remove"></span>' . "\n";
            print '</a>' . "\n";
            print '<div style="clear: both"></div>' . "\n";
            print '</div>' . "\n";
            
            print '</div>' . "\n";
        }

    ?>

<?php } else { //Login ?>
    
    <h1>Welcome!</h1>
    
    <?php
        if(isset($message))
            print '<div class="alert alert-danger"><strong>Error:</strong> '.$message.'</div>';
    ?>

    <p>To start viewing and managing all your repositorites please login with your username and password.</p>

<?php } ?>

    <hr />

    <footer>
        <p class="pull-right">&copy; <?=$copyright?> <?=date("Y", time())?></p>
    </footer>

    </div>
  </body>
</html>
