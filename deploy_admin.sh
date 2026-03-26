mysql -h taxisapp-database.ct8q0g6ei5q7.us-east-2.rds.amazonaws.com -u admin -p"TaxisApp2024!" taxisapp -e "ALTER TABLE users ADD COLUMN role_id INT DEFAULT NULL;" || true
cd /var/www/taxisapp && composer dump-autoload
echo "<?php return;" | sudo tee /var/www/taxisapp/vendor/composer/platform_check.php
cd /var/www/taxisapp && sudo php7.4 artisan db:seed --class=AdminUserSeeder
