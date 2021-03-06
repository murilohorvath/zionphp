<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?=$title?></title>
	<link href="https://fonts.googleapis.com/css?family=Kanit:200" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
	<link type="text/css" rel="stylesheet" href="/zion/lib/zion/http-status.css" />

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
    <div id="httpstatus">
        <div class="httpstatus">
            <div class="httpstatus-000">
                <h1><?=$status?></h1>
            </div>
            
            <h2><?=$title?></h2>
            <p>
            	<?=$message?>
            	<br>
            	<a href="/zion/mod/core/User/loginform">Voltar para o início</a>
            </p>
            
            <div class="httpstatus-social">
            	<a href="https://github.com/vcd94xt10z/zionphp" target="_blank"><i class="fa fa-github"></i></a>
            </div>
        </div>
    </div>
</body>
</html>