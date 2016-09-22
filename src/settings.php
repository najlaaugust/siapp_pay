<?php
return [
		'settings' => [
				// database settings
				'dbguy' => [
						'serverName' => 'isr-sqlcheetah.isr.umich.edu',
						'uid' => 'SRCSI_WebUser',
						'pwd' => 'srcsiCheetara!',
						'database' => 'SRCSI'
				],
				
				// Monolog settings
				'logger' => [
						'name' => 'Summer Institute Webservice',
						'path' => __DIR__ . '/../logs/app.log'
				]
		],
];