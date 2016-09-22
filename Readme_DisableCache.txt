app -> config -> config.yml & config_dev.yml
twig:
	cache: false


app.php
	$kernel->loadClassCache(); ---- auskommentieren

app_dev.php
	$kernel->loadClassCache(); ---- auskommentieren