<?php
include 'updater.php';
$version = 1.3;

$query = $argv[1];
$value = explode(" ", $query, 2);
$sec = str_replace("\\", "", $value[1]);
$cmd = str_replace("\\", "", $value[0]);

$config = json_decode(file_get_contents("tmp.txt"));
$directory = $config->directory;
$project = $config->project;
$main = $config->directory . $config->project;

if($version == getversion()){
	switch ($cmd) {
		case 'directory':
			$config->directory = $sec;
			fwrite(fopen('tmp.txt','w'), json_encode($config));
			echo "Directory: " . $config->directory;
		break;
		
		case 'new':
			if(empty($config->directory)){
				echo "Set a directory using the 'directory' command";
				die();
			}
			if(!empty($sec)){
				$config->project = $sec;
				fwrite(fopen('tmp.txt','w'), json_encode($config));
				shell_exec("cd $config->directory && mkdir $sec && cd $sec");
				shell_exec("cd $main ; curl -silent -L https://github.com/laravel/laravel/zipball/master > laravel.zip ; unzip -qq laravel.zip ; rm laravel.zip ; cd *-laravel-* ; mv * .. ; cd .. ; rm -R *-laravel-*");
				shell_exec("cd $main ; chmod -R o+w storage;");
				shell_exec("cd $main ; curl https://raw.github.com/JeffreyWay/Laravel-Generator/master/generate.php > application/tasks/generate.php");
				shell_exec("cd $main ; curl https://raw.github.com/gist/3693377/506ede69c059fa8df1cfc10f72847bc740ec79f1/application.php > application/config/application.php");
				shell_exec("cd $main ; php artisan key:generate");
				shell_exec("cd $main ; /Applications/Sublime\ Text\ 2.app/Contents/SharedSupport/bin/subl .");
				echo "Your new project has been created!";
			} else {
				echo "Please enter a project name";
			}
		break;

		//generate controller
		case 'c':
		case 'controller':
			if(empty($project)){
				echo "No project set, use the 'project' command";
			}else{
				shell_exec("cd $main ; php artisan generate:controller $sec");
				echo "Success!";
			}
		break;


		//generate model
		case 'm':
		case 'model':
			if(empty($project)){
				echo "No project set, use the 'project' command";
			}else{
				shell_exec("cd $main ; php artisan generate:model $sec");
				echo "Success!";
			}
		break;


		//generate view
		case 'v':
		case 'view':
			if(empty($dir)){
				echo "No project set, use the 'project' command";
			}else{
				shell_exec("cd $main ; php artisan generate:view $sec");
				echo "Success!";
			}
		break;

		//generate migration
		case 'mig':
		case 'migration':
			if(empty($dir)){
				echo "No project set, use the 'project' command";
			}else{
				shell_exec("cd $main ; php artisan generate:migration $sec");
				echo "Success!";
			}
		break;

		//generate assets
		case 'a':
		case 'assets':
			if(empty($dir)){
				echo "No project set, use the 'project' command";
			}else{
				shell_exec("cd $main ; php artisan generate:assets $sec");
				echo "Success!";
			}
		break;

		//generate test
		case 't':
		case 'test':
			if(empty($dir)){
				echo "No project set, use the 'project' command";
			}else{
				shell_exec("cd $main ; php artisan generate:test $sec");
				echo "Success!";
			}
		break;

		//generate resource
		case 'r':
		case 'resource':
			if(empty($dir)){
				echo "No project set, use the 'project' command";
			}else{
				shell_exec("cd $main ; php artisan generate:resource $sec");
				echo "Success!";
			}
		break;

		//get and set the current project directory
		case 'set': //legacy command
		case 'p':
		case 'project':
			//if no parameter is set show current project
			if(empty($sec)){
				if(empty($project)){
					echo "No project set, use the 'project' command";
				}else{
					echo "Current project is set to '$project'";
				}
			//else set a project, allows changing of projects
			}else{
				$config->project = $sec;
				fwrite(fopen('tmp.txt','w'), json_encode($config));
				echo "Project set to $sec";
			}
		break;

		//list laravel projects
		case 'l':
		case 'list':
			//returns all directories containing a "laravel" directory
			$output = shell_exec("cd $directory ; du -d 2 | grep 'laravel' | awk '{print $2}'");
			$output = str_replace('./', '', $output);
			$output = str_replace('/laravel', '', $output);
			echo 'Available projects: '.$output;
		break;

		//display version
		case 'version':
			echo "Current version: " . $version;
		break;

		//allows you to run any command through artisan
		case 'artisan':
			shell_exec("cd $main ; php artisan $sec");
		break;

		//installs the migration tables
		case 'install':
			shell_exec("cd $main ; php artisan migrate:install");
		break;

		//migrates a migration
		case 'migrate':
			shell_exec("cd $main ; php artisan migrate");
		break;

		//rolls back a migration
		case 'rollback':
			shell_exec("cd $main ; php artisan migrate:rollback");
		break;


		case 'help':
		default:
			//do something here.
		break;
	}
}else{
	update($version);
}